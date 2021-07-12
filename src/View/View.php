<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View;

use Psr\Container\ContainerInterface;

use Pes\Type\ContextDataInterface;
use Pes\Type\ContextData;

use Pes\View\Renderer\RendererInterface;
use Pes\View\Renderer\TemplateRendererInterface;
use Pes\View\Template\TemplateInterface;

/**
 * use použit jako definice fallback rendereru - použit pro renderování, pokud nebyl získán žádný uživatelsky zadaný renderer.
 */
use Pes\View\Renderer\ImplodeRenderer as FallbackRenderer;
use Pes\View\Template\ImplodeTemplate as FallbackTemplate;

use Pes\Type\Exception\InvalidDataTypeException;
use Pes\View\Exception\{
    BadRendererForTemplateException, InvalidTypeForSetDataException
};

/**
 * View - objekt je vytvořen se zadaným rendererem a s jeho použitím vytváří textový obsah z aktuálně zadaných dat.
 * Generovaný obsah je dán rendererem.
 *
 * @author pes2704
 */
class View implements ViewInterface {

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     *
     * @var string
     */
    protected $rendererName;

    /**
     * @var RendererInterface
     */
    protected $fallbackRenderer;

    protected $fallbackRendererName;

    /**
     * @var ContainerInterface
     */
    protected $rendererContainer;

    /**
     * @var TemplateInterface
     */
    protected $template;

    /**
     *
     * @var ContextData
     */
    protected $contextData;

    /**
     *
     * @var \SplObjectStorage
     */
    public $componentViews;

    /**
     * Lze nastavit data pro renderování. Tato data budou použita metodou render().
     *
     * @param iterable $contextData
     * @return ViewInterface
     */
    public function setData($contextData): ViewInterface {
        if ($contextData instanceof ContextDataInterface) {
            $this->contextData = $contextData;
        } else {
            try {
                $this->contextData = new ContextData($contextData);
            } catch (InvalidDataTypeException $exc) {
                throw new InvalidTypeForSetDataException('Data musí být typu ContextDataInterface nebo vhodná data pro konstruktor ContextData. Dta jsou typu'. gettype($contextData).'.', 0, $exc);
            }

        }
        return $this;
    }

    /**
     * Deprecated.
     *
     * @param type $viewModel
     * @return ViewInterface
     */
    public function setViewModel($viewModel): ViewInterface {
        assert(false, 'Deprecated!');
        $this->contextData = $viewModel;
    }

    public function setRendererContainer(ContainerInterface $rendererContainer): ViewInterface {
        $this->rendererContainer = $rendererContainer;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param RendererInterface $renderer
     * @return \Pes\View\ViewInterface
     */
    public function setRenderer(RendererInterface $renderer): ViewInterface {
        $this->renderer = $renderer;
        return $this;
    }


    /**
     * {@inheritdoc}
     *
     * @param $rendererName
     * @return ViewInterface
     */
    public function setRendererName($rendererName): ViewInterface {
        $this->rendererName = $rendererName;
        return $this;
    }

    public function setFallbackRenderer(RendererInterface $renderer): ViewInterface {
        $this->fallbackRenderer = $renderer;
        return $this;
    }

    public function setFallbackRendererName($fallbackRendererName): ViewInterface {
        $this->fallbackRendererName = $fallbackRendererName;
        return $this;
    }

    /**
     * Nastaví template objekt pro renderování. Tato template bude použita metodou render(). Pokud parametr template je null, dojde k použití dalších
     * možností při resolvování rendereru - viz resolveRenderer().
     *
     * @param TemplateInterface $template
     * @return \Pes\View\ViewInterface
     */
    public function setTemplate(TemplateInterface $template = null): ViewInterface {
        $this->template = $template;
        return $this;
    }

    /**
     * Metoda umožňuje použít objekt view přímo jako proměnnou (proměnou v šabloně) pro další view.
     *
     * Interně volá metodu třídy View->getString(). Pokud je třeba renderovat nějaká data, je nutné je zadat metodou setData($data).
     *
     * Protože v PHP nesmí při vykonávání magické metodu __toString dojít k výjimce, volání getString je v try-cath bloku a případná výjimka
     * je převedena na E_USER_ERROR.
     * To obvykle vede na Fatal error, ale nezobrazí se zavádějící hlášení o výjimce v metodě __toString s řádkem chyby, kterým je řádek v templatě,
     * ve které došlo k pokusu o renderování nějakého view, který byl použit jako proměnná.
     *
     * Pokud je potřebné vyhazovat výjimky z důvodu jejich zachycování nebo pro ladění, je třeba volat přímo metodu View->getString().
     *
     * @return string
     */
    public function __toString() {
        try {
            $str = $this->getString();
        } catch (\Throwable $e) {

            trigger_error("Výjimka ".get_class($e)." při vykonávání metody __toString objektu ".get_class($this).". ".
                    "Exception in: ".$e->getFile()." on line: ".$e->getLine().". ".$e->getMessage()."."
                     .PHP_EOL.str_replace('\n', PHP_EOL, $e->getTraceAsString()), E_USER_ERROR);
        }
        return $str;
    }

    /**
     * Nalezne vhodný renderer pomocí metody resolveRenderer(), pokud je renderer typu RendererModelAwareInterface nastaví rendereru viewModel nastavený metodou setViewModel()
     * a renderuje bez použití dat nastavených metodou setData(), pokud renderer není typu RendererModelAwareInterface, renderuje s použitím dat nastavených metodou setData().
     *
     * Pokud je renderer typu RendererModelAwareInterface a view nemá nastaven viewModel metodou setViewModel() vyhofí výjimku.
     *
     * @return string
     */
    public function getString() {
        $this->beforeRenderingHook();
        $renderer = $this->resolveRenderer();
        return $renderer->render($this->contextData);  // předává data jako pole
    }

    public function beforeRenderingHook(): void {

    }

    /**
     * Metoda pro přidání komponentních view. Při renderování kompozitního view budou renderována komponentní view a vygenerovaný výsledek bude vložen
     * do kompozitního view na místo proměnné zadané zde jako jméno. Pokud kompozitní view je null, proměnná je nahrazena prázdným retězcem.
     * Jednotlivá komponentní view budou renderována bez předání (nastavení) template a dat, musí mít tedy před renderováním kompozitního view nastavenu šablonu
     * a data pokud je potřebují pro své renderování.
     *
     * @param \Pes\View\ViewInterface $componentView Komponetní view nebo null
     * @param string $name Jméno proměnné v kompozitním view, která má být nahrazena výstupem zadané komponentní view
     * @return \Pes\View\CompositeViewInterface
     */
    public function appendComponentView(ViewInterface $componentView=null, $name): CompositeViewInterface {
        // použití SplObjectStorage umožňuje hlídat duplicitní přidání shodného objektu - riziko je velké např. při nesprávném použití kontejneru pro vytváření view objektů
        if (!isset($this->componentViews)) {
            $this->componentViews = new \SplObjectStorage();
        }
        if ($this->componentViews->contains($componentView)) {
            $usedWithName = $this->componentViews->offsetGet($componentView);
            $cls = get_class($componentView);
            throw new Exception\DuplicateComponentViewException("Komponentní objekt view $cls se jménem $name nelze přidat, v kompozitním view již je přidán identický objekt pod jménem $usedWithName. Jednotlivá kompozitní view musí být různé objekty.");
        } else {
            if (isset($componentView)) {
                $this->componentViews->attach($componentView, $name);
            } else {
                $this->componentViews->attach(new View(), $name);
            }
        }
        return $this;
    }

    /**
     * Metoda pro přidání komponentních view jako pole nebo \Traversable objekt. Interně volá metodu appendComponentView()
     * @param iterable $componentViews
     * @return \Pes\View\CompositeViewInterface
     */
    public function appendComponentViews(iterable $componentViews): CompositeViewInterface  {
        foreach ($componentViews as $name => $componentView) {
            $this->appendComponentView($componentView, $name);
        }
        return $this;
    }

    ##### private methods ###########################

    /**
     *
     * Vybere renderer v závislosti na kombinaci nastavených setTemplate(), setRenderer(), setRendererName(), setFallbackRenderer(), setFallbackRendererName():
     * <ul>
     * <li>Je template - template se renderuje rendererem získaným v tomto pořadí:
     *  <ul>
     *    <li>rendererem zadaným metodou setRenderer()</li>
     *    <li>rendererem získaným z renderer kontejneru podle jména zadaného metodou setRendererName() </li>
     *    <li>default rendererem šablony získaným z renderer kontejneru podle jména poskytnutého metodou template getDefaultRendererService()</li>
     *  </ul>
     * </li>
     * <li>Není template - renderuje rendererem získaným v tomto pořadí:
     *  <ul>
     *    <li>rendererem zadaným metodou setRenderer()</li>
     *    <li>rendererem získaným z renderer kontejneru podle rendererName metodou setRendererName() </li>
     *    <li>rendererem zadaným metodou setFallbackRenderer()</li>
     *    <li>rendererem získaným z renderer kontejneru podle jména zadaného metodou setFallbackRendererName()</li>
     *    <li>fallback rendererem získaným privátní metodou getFallbackRendereAndTemplate() pro získání alespoň nějakého výstupu. Jméno třídy fallback rendereru je definováno jako
     * alias FallbackRenderer v příkazu use uvedeném v třídě View.</li>
     *  </ul>
     * </li>
     * </ul>
     * </ol>
     *
     * Použití:
     *
     * Pomocí metod setRendererName() a setFallbackRendererName() lze nastavit jméno služby renderer kontejneru (obvykle jméno třídy rendereru) a rendery josu získávána z tohoto kontejneru
     * (kontejner musí být také nastaven).
     *
     * - Výchozí režim je pro renderování template je použití defaultního rendereru definovaného v template. Pokud někdy template chybí, použije se fallback renderer.
     * Defaultní template renderer lze přebít zadáním rendereru metodou setRenderer(). Od okažiku nastavení rendereru jsou všechny template renderovány zadaným rendererem.
     *
     * - Pro renderování bez template je samozřejmě nutné nastavit renderer metodou setRenderer() nebo setRendererName() vždy.
     *
     */
    private function resolveRenderer(): RendererInterface {
        if (isset($this->template)) {
            if (isset($this->renderer)) {
                $renderer = $this->renderer;
            } elseif (isset($this->rendererName)) {
                $renderer = $this->getRendererByName($this->rendererName);
            } else {
                $renderer = $this->getDefaultTemplateRenderer($this->template);
            }
            $this->setRendererTemplate($renderer, $this->template);
        } elseif (isset($this->renderer)) {
            $renderer = $this->renderer;
        } elseif (isset($this->rendererName)) {
            $renderer = $this->getRendererByName($this->rendererName);
        } elseif (isset($this->fallbackRenderer)) {
            $renderer = $this->fallbackRenderer;
        } elseif (isset($this->fallbackRendererName)) {
            $renderer = $this->getRendererByName($this->fallbackRendererName);
        } else {
            $renderer = $this->getFallbackRendereWithTemplate();
        }
        return $renderer;
    }

    private function getRendererByName($rendererName): RendererInterface {
        if (!isset($this->rendererContainer)) {
            throw new LogicException("Nelze získat renderer podle jména rendereru, není zadán renderer kontejner.");
        } else {
            return $this->rendererContainer->get($rendererName);
        }
    }

    private function getDefaultTemplateRenderer(TemplateInterface $template): RendererInterface {
        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Nelze získat renderer jako default renderer šablony, není zadán renderer kontejner.");
        } else {
            return $this->rendererContainer->get($template->getDefaultRendererService());
        }
    }

    private function setRendererTemplate(RendererInterface $renderer, TemplateInterface $template=null) {
        if ($renderer instanceof TemplateRendererInterface) {
            if ($template) {
                if (!$this->checkRendererTemplateCompatibility($renderer, $this->template)) {
                    throw new BadRendererForTemplateException(
                            "Template ".get_class($template)." vyžaduje renderer typu $templateDefaultRendererClass. "
                            . "Zadaný renderer ".get_class($renderer)." nelze použít pro renderování template.");
                }
                $renderer->setTemplate($template);
            }
        }
    }

    private function checkRendererTemplateCompatibility(RendererInterface $renderer, TemplateInterface $template): bool {
        $templateDefaultRendererClass = $template->getDefaultRendererService();
        return ($renderer instanceof $templateDefaultRendererClass);
    }

    private function getFallbackRendereWithTemplate(): RendererInterface {
        $rendererName = $this->rendererName ?? 'undefined';
        $containerClass = isset($this->rendererContainer) ? get_class($this->rendererContainer) : 'renderer container is not set';
        $templateClass = isset($this->template) ? get_class($this->template) : 'undefined';
        user_error("Nepodařilo se získat renderer, je použit fallback renderer a fallback template. "
                . " Renderer name: {$rendererName},"
                . " renderer container: $containerClass,"
                . " template: {$templateClass}", E_USER_NOTICE);
        $renderer = new FallbackRenderer();
        $renderer->setTemplate(new FallbackTemplate());
        return $renderer;
    }

    /**
     * Metoda renderuje všechny vložené component renderery. Výstupní kód z jednotlivých renderování vkládá do kontextu
     * composer rendereru vždy pod jménem proměnné, se kterým byl component renderer přidán. Nakonec renderuje
     * compose renderer. Při renderování compose rendereru použije data zadaná jako parametr, pokud nebyla zadána, data zadaná metodou setData($data).
     *
     * @return string
     */
    private function getComponets() {
        if (is_iterable($this->componentViews)) {
            foreach ($this->componentViews as $componentView) {
                $this->contextData[$this->componentViews->getInfo()] = $componentView->getString();
            }
        }
        return parent::getString();
    }
}