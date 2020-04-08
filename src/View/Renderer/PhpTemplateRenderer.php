<?php

namespace Pes\View\Renderer;

use Pes\View\Template\TemplateInterface;
use Pes\View\Recorder\RecorderProviderInterface;
use Pes\View\Renderer\Exception\UnsupportedTemplateException;

/**
 * Renderer - vstupem je html/php šablona, což je spustitelný soubor PHP, výstupem je text.
 * Renderer generuje textový výstup tak, že vykonává PHP kód uložený v souboru html/php ne obecně text/php šablony. Soubor šablony je
 * standartní PHP soubor, tedy může obsahovat text a PHP kód v segmentech uzavřených do PHP tagů. Text v šabloné může být HTML nebo libovolný text.
 *
 * Objekt pro vytvoření obsahu s pomocí šablony příjímá data ve formě asociativního pole. Toto pole je pomocí PHP funkce extract() interně převedeno
 * na jednotlivé proměnné se jmény odpovídajícími indexům v asociativním poli dat.
 * V souborech šablon se pak využívají takto získané jednotlivé proměnné. Se soubory šablon se
 * pracuje tak, že jednotlivé proměnné jsou vždy v lokálním rozsahu proměnných PHP (local scope) a nikdy se neovlivňují proměnné v různých šablonách
 * nebo jinde v kódu. Je tak možné v různých šablonách používat stejně pojmenované proměnné.
 *
 * Objekt je schopen zaznamenat užití zadaných dat při renderování šablony. Zaznamenává informace o proměnných předaných jako data,
 * nedefinovaných proměnných použitých v šabloně a o proměnných předaných v datech, ale v šabloně nepoužitých.
 * Tyto informace je následně možné použít pro ladění nebo logování. Záznam je zaznamenán v specializovaném záznamovém objektu,
 * který lze získat voláním příslušné metody.
 *
 * @author pes2704
 */
class PhpTemplateRenderer implements PhpTemplateRendererInterface, PhpTemplateFunctionsInterface, RendererRecordableInterface {

    use PhpTemplateFunctionsTrait;

    const FILTER_DELIMITER = '|';

    private $template;

    /**
     * @var RecorderProviderInterface
     */
    private $recorderProvider;

    /**
     * Pro record info
     * @var string
     */
    private $templateFileNamesStack = [];

    public function setTemplate(TemplateInterface $template) {
        if ($template->getDefaultRendererService() !== PhpTemplateRenderer::class) {
            throw new UnsupportedTemplateException("Renderer ". get_called_class()." nepodporuje renderování template typu ". get_class($this->template));
        }
        $this->template = $template;
    }

    /**
     * Proměnná nastavována v protectedIncludeScope k užití pro metody insert() a další
     * @var VariablesUsageRecorderInterface
     */
    private $recorderForErrorHandler;

    private $originObLevel;

    private $actualTemplateVars = [];

    public function setRecorderProvider(RecorderProviderInterface $recorderProvider): RendererRecordableInterface {
        $this->recorderProvider = $recorderProvider;
        return $this;
    }

    /**
     *
     * Renderuje soubor šablony včetně vnořených šablon. Poskytuje metody pro vnořivání šablon a pomocné metody pro transformaci textu.
     *
     * <p>Vložené soubory jsou vkládány pomocí php příkazu include nebo voláním metod rendereru insert() a repeat() v kódu template.
     * Obsah souboru template se vykoná jako php skript, kód obsažený v souboru se vykonává uvnitř metody rendereru, proto jsou pro něj dostupné
     * všechny metody rendereru, například metoda insert() nebo repeat().</p>
     * <p>Příklad: Soubory šablon jsou vkládány použitím php příkazu include takto: <code><?php include sablona.php; ?></code> nebo voláním metody insert template objektu takto:
     * <code><?= $this->insert("contents/main/main.php", $data) ?></code> a repeat takto: <code><?= $this->insert("contents/main/repeated_fragment.php", $data) ?></code>.</p>
     * <p>Data jsou metodě, ve které (lokálně) proběhne vykonání kódu šablony a tedy i volání volání metod insert() nebo repeat() předána pomocí parametru se jménem $context. Pokud je proměnná $context typu array nebo
     * je iterovatelná is_iterable() jsou její prvky extrahovány do lokálních proměnných a ty jsou pak dostupné v kódu šablony. Proměnná $context je navíc vždy lokální
     * proměnnou v první úrovni šablony je vždy spolu s extrahovanými proměnnými dostupná. V šabloně první úrovně je dostupná vždy a pokud v metodě insert() nebo repeat() předám jako
     * data tuto proměnou - tedy předám proměnnou $context např. takto: <code><?= $this->insert("contents/main/repeated_fragment.php", $context) ?></code>
     * je pak stejná proměnná $context dostupná i v příslušné podřízené šabloně. Kontextová data je takto možno řízeně předávat do dalších úrovní šablon.
     * <p>Renderer dále nabízí použití pomocných metod pro transformaci textu definovaných v PhpTemplateFunctionsTrait. Tyto metody jsou obdobně dostupné z kódu
     * šablony voláním <code><?= $this->esc() ?></code> a podobně.<úp>
     *
     * @throws \Throwable <p>Znovu vyhodí Error nebo Exception, pokud vznikla někde při vykonávání kódu template. Před vyhozením takové chyby nebo výjimky
     * nejprve odešle na výstup obsah výstupního bufferu (všech úrovní bufferu), který do něj byl zapsán předtím, než chyba nebo výjimka vznikla.</p>
     *
     * @param iterable $data
     * @return string
     */
    public function render(iterable $data=NULL) {

        // unused vypnuto zde a na konci includeToProtectedScope()
        //        if (isset($recorderProvider)) {
        //            $recorder = $recorderProvider->provideRecorder();
        //            $recorder->setIndex($this->templateFileName)->setRecordInfo("Record for templates with root template file {$this->templateFileName}.");
        //        }
        //        $this->catchTemplateVars($this->templateFileName);

        $templateFilename = $this->template->getTemplateFilename();
        return $this->includeToProtectedScope($templateFilename, $data);
    }

###### rendering methods ####################

    private function catchTemplateVars($templateFileName) {
        $oldErrorHandler = set_error_handler(array($this, 'unusedErrorHandler'));
        $this->actualTemplateVars = [];
        // získání promenných v template
        try {
            include $templateFileName;
        // Ošeření výjimek a chyb vzniklých při vykonávání template tak, že pokračuji dál.
        } catch (\Throwable $e) {
        }
        set_error_handler($oldErrorHandler);
        return $this->actualTemplateVars;
    }

    /**
     * Parametr je proměnná se jménem $context - viz doc k renderTemplate.
     *
     * @param type $context
     * @return type
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    private function includeToProtectedScope($templateFileName, $context) {
        // v kontextu se nesmí použít jméno $bagForMethodVars___, $extractedVarName___, $extractedVarValue___
        $bagForMethodVars___ = new \stdClass();

        ## output buffering
        ob_start();
        // pro výpis všech úrovní output bufferu v případě chyby při vykonávání template
        if (!$this->originObLevel) {
            $this->originObLevel = ob_get_level();
        }

        ## příprava logování
        if (isset($this->recorderProvider)) {
            $this->templateFileNamesStack[] = $templateFileName;
            $bagForMethodVars___->variableUsageRecorder =  $this->recorderProvider->provideRecorder(implode('->', $this->templateFileNamesStack), $this->recorderForErrorHandler);   // nová template, last recorder jako parent recorder
            $bagForMethodVars___->oldRecorderForErrorHandler = $this->recorderForErrorHandler;
            $this->recorderForErrorHandler = $bagForMethodVars___->variableUsageRecorder;
            $bagForMethodVars___->oldErrorHandler = set_error_handler(array($this, 'templateErrorHandler'));
            // počet proměnných před extrahováním $data - pro record užití proměnných v šabloně
//            $numberOfVarsBefore = count(get_defined_vars())+1;   // včetně nově vzniklé $numberOfVarsBefore
        }

        ## extrahování - iterable ve foreach (zrušeno extrahování array funkcí extract($data))
        # - extrahuje jen prvky s indexy, které nejsou integer (s jinými čísly nepočítám, is_numeric() funguje, ale je cca 0,5ms pomalejší)
        # - probede trim() jména proměnné pro případ náhodnýcg mezer před či za jménem v řetězci (lehce se to stane)
        ##
        if ($context AND (is_array($context) OR $context instanceof \Traversable)) {
            foreach ($context as $extractedVarName___=>$extractedVarValue___) {
                if ($extractedVarName___ !== (int) $extractedVarName___) {      // ne integer index
                    $extractedVarName___ = trim($extractedVarName___);
                    if ($extractedVarValue___ instanceof \Closure) {
                        $$extractedVarName___ = $extractedVarValue___();     // Closure zavolám (kontejner)
                    } else {
                        $$extractedVarName___ = $extractedVarValue___;
                    }
                    if (isset($bagForMethodVars___->variableUsageRecorder)) {
                        $bagForMethodVars___->variableUsageRecorder->addContextVar($extractedVarName___, $extractedVarValue___);
                    }
                }
            }
            unset($extractedVarName___);
            unset($extractedVarValue___);
        }

        ## include šablony
        try {

            include $templateFileName;

        // Ošeření výjimek a chyb vzniklých při vykonávání template tak, abych nezahodil nějaký obsah, který byl zapsán do výstupního bufferu před vznikem chyby nebo výjimky
        // aktualizovaný příklad z http://php.net/manual/en/function.ob-get-level.php
        } catch (\Throwable $e) {
            while (ob_get_level() > $this->originObLevel) {
                ob_end_clean();
            }
            throw $e;
        }
        $ob = ob_get_clean();

        ## záznam pro logování
        if (isset($bagForMethodVars___->variableUsageRecorder)) {
            set_error_handler($bagForMethodVars___->oldErrorHandler);
            $bagForMethodVars___->variableUsageRecorder->setRecordInfo("Vzniko ".strlen($ob)." bytů.");
            $this->recorderForErrorHandler = $bagForMethodVars___->oldRecorderForErrorHandler;
            array_pop($this->templateFileNamesStack);
            //unused - vypnuto zde + na řádku 131 a v renderTemplate
            //$recorder->setUnusedVars($this->unusedVars(get_defined_vars(), $numberOfVarsBefore, $this->actualTemplateVars));
        }

        return $ob;
    }

    private function unusedVars($definedVarsArray, $numberOfVarsBefore, $templateVars) {
//        $u1 = array_slice($definedVarsArray, $numberOfVarsBefore);
//        $u2 = array_keys($u1);
//        $u3 = array_diff($u2, $templateVars);
        return array_diff(array_keys(array_slice($definedVarsArray, $numberOfVarsBefore)), $templateVars);
    }

###### template methods ####################

    /**
     * {@inheritdoc}
     *
     * @param type $templateFilename
     * @param type $data
     * @param type $emptyDataTemplateFilename
     * @return type
     */
    public function insert($templateFilename, $data=[], $emptyDataTemplateFilename='') {
        if ($data OR !$emptyDataTemplateFilename) {
            $ret = $this->includeToProtectedScope($templateFilename, $data);
        } else {
            $ret = $this->includeToProtectedScope($emptyDataTemplateFilename, $data);
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     * Tato metoda vrací jako náhradní hodnotu (pokud nejsou předána data ani náhradní šablona pro případ prázdných dat $emptyDataTemplateFilename) řetězec "..."
     *
     * @param type $templateFilename
     * @param \Traversable $data
     * @param type $emptyDataTemplateFilename
     * @return string
     */
    public function repeat($templateFilename, $data=[], $variableName='repeatItem', $emptyDataTemplateFilename='') {
        if ($data) {
            foreach ($data as $item) {
                if (is_array($item) OR $item instanceof \Traversable) {  // nečíselný klíč = položka ascociativní
                    $pieces[] = $this->includeToProtectedScope($templateFilename, $item);
                } else {   // číselný klíč = položka číslovaná -> data se předají ve formě náhradního asociativního pole s indexem $variableName
                    $pieces[] = $this->includeToProtectedScope($templateFilename, [$variableName=>$item]);
                }
            }
            $ret =  isset($pieces) ? implode(PHP_EOL, $pieces) : '';
        } elseif ($emptyDataTemplateFilename) {
                $pieces[] = $this->includeToProtectedScope($emptyDataTemplateFilename, $item);
        } else {
            $ret = '...';
        }
        return $ret;
    }

############# render error handler ####################################

    /**
     * Zaznamenává nedefinované proměnné. Tato metoda musí být po dobu vykonávání template nastavena jako error_handler
     * funkcí set_error_handler(array($this, 'templateErrorHandler')). Pak je volána při vzniku jakékoli chyby. Rozpoznává chyby
     * typu E_NOTICE a zjišťuje zda chybové hlášení začíná textem 'Undefined variable: '. Pokud ano, zaznamená výskyt nedefinované proměnné
     * do rekorderu.
     *
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     * @return boolean
     */
    function templateErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall through to the standard PHP error handler
            return FALSE;
        }
        if ($errno == E_NOTICE) {
            //text error message - příklad:
            //E_NOTICE, $errstr: Undefined variable: header
            //E_WARNING, $errstr: Invalid argument supplied for foreach()
            //E_NOTICE, $errstr: Trying to get property 'nemam' of non-object
            $pos = strpos($errstr, 'Undefined variable: ');
            if ($pos===0) {
                $name = substr($errstr, 20);
                if (isset($this->recorderForErrorHandler)) {
                    $this->recorderForErrorHandler->addUndefinedVarError($errstr, $errfile, $errline);
                }
            }
        }
        $development= PES_DEVELOPMENT ? TRUE : FALSE;
        if ($development) {
        /* Execute PHP internal error handler - here in any error case */
            return FALSE;
        }
        /* Don't execute PHP internal error handler */
        return TRUE;
    }

    /**
     * Speciálním způsobem aznamenává nedefinované proměnné. Je určen pro použití výhradně pro zjištění proměnných použitách v šabloně.
     * Je nastaven ja error handler před extrahování proměnných kontextu a pak volání include template.php; způsobí E_NOTICE 'Undefined variable: '
     * pro každou proměnnou uvedenoui v template.
     * Výjimkou jsou proměnné uvedená např. jako argument isset($variable) apod.
     *
     *
     * Tato metoda musí být po dobu vykonávání template nastavena jako error_handler
     * funkcí set_error_handler(array($this, 'unusedErrorHandler')). Pak je volána při vzniku jakékoli chyby. Rozpoznává chyby
     * typu E_NOTICE a zjišťuje zda chybové hlášení začíná textem 'Undefined variable: '. Pokud ano, zaznamená výskyt nedefinované proměnné
     * do rekorderu.
     *
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     * @return boolean
     */
    function unusedErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall through to the standard PHP error handler
            return FALSE;
        }
        if ($errno == E_NOTICE) {
            //text error message - příklad: Undefined variable: header
            $pos = strpos($errstr, 'Undefined variable: ');
            if ($pos===0) {
                $name = substr($errstr, 20);
                $this->actualTemplateVars[] = $name;
            }
        }

        /* Don't execute PHP internal error handler */
        return TRUE;
    }
}
