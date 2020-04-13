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

use Pes\View\Renderer\RendererInterface;
use Pes\View\Renderer\TemplateRendererInterface;
use Pes\View\Renderer\Container\TemplateRendererContainer;
use Pes\View\Template\TemplateInterface;

use Psr\Container\ContainerInterface;

/**
 * use použit jako definice fallback rendereru - použit pro renderování, pokud nebyla zadána žádná template a tedy není znám default renderer.
 */
use Pes\View\Renderer\ImplodeRenderer as FallbackRenderer;
use Pes\View\Template\ImplodeTemplate as FallbackTemplate;

use Pes\View\Exception\{
    BadRendererForTemplateException
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

    protected $data;

    /**
     * @var TemplateInterface
     */
    protected $template;

    /**
     * @var ContainerInterface
     */
    protected $rendererContainer;

    protected $rendererName;

    /**
     * Lze nastavit data pro renderování. Tato data budou použita metodou render().
     * @param type $data
     * @return ViewInterface
     */
    public function setData($data=NULL): ViewInterface {
        $this->data = $data;
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

    public function setRendererContainer(ContainerInterface $rendererContainer): ViewInterface {
        $this->rendererContainer = $rendererContainer;
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


    /**
     * Nastaví template objekt pro renderování. Tato template bude použita metodou render().
     * @param TemplateInterface $template
     * @return \Pes\View\ViewInterface
     */
    public function setTemplate(TemplateInterface $template): ViewInterface {
        $this->template = $template;
        return $this;
    }

    /**
     * Renderuje data s použitím případné template a vytvoří obsah.
     *
     * Renderuje data:
     * <ol>
     * <li>Data zadaná jako parametr.</l>
     * <li>Pokud parameter data není zadán, renderuje data zadaná metodou setData (view->setData($data)).</li>
     * </ol>
     *
     * Použije renderer:
     * <ol>
     * <li>renderer nastavenž metodou View->setRendererName()</li>
     * <li>pokud je nastavena template, použije renderer z renderer kontejneru (RendererContainer) se jménem service získanou z template
     * metodou template->getDefaultRendererService()</li>
     * <li>fallback: pokud není zadán renderer, použije se fallback renderer pro získání alespoň nějakého výstupu. Jméno třídy fallback rendereru je definováno jako
     * alias FallbackRenderer v příkazu use uvedeném v třídě View.</li>
     * </ol>
     *
     * Použití:
     *
     * - Výchozí režim je pro renderování template je použití defaultního rendereru definovaného v template.
     * Defaultní template renderer lze přebít zadáním rendereru metodou setRenderer(). Od okažiku nastavení rendereru jsou všechny template renderovány zadaným rendererem.
     *
     * - Pro renderování bez template je samozřejmě nutné nastavit renderer metodou setRenderer() vždy.
     *
     * @param mixed $data
     * @return string
     */
    public function getString($data=NULL) {
        $this->resolveRenderer();
        if ($this->renderer instanceof TemplateRendererInterface) {
            $this->renderer->setTemplate($this->template);
        }
        return $this->renderer->render($data ?? $this->data);
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
            user_error(' Výjimka pri vykonávání metody __toString: '.$e->getMessage().' in: '.$e->getFile().' on line: '.$e->getLine().'.'
            .PHP_EOL.str_replace('\n', PHP_EOL, $e->getTraceAsString()), E_USER_ERROR);
            $str = '';
        }
        return $str;
    }

    ##### private methods ###########################

    private function resolveRenderer() {
        if (!isset($this->renderer)) {
            if (isset($this->rendererName)) {
                $this->renderer = $this->rendererContainer->get($this->rendererName);
                if ($this->template) {
                    $this->checkRendererTemplateConsistency($this->renderer, $this->template);
                }
            } elseif ($this->template) {
                $this->renderer = $this->rendererContainer->get($this->template->getDefaultRendererService());
            } else {
                $this->useFallbackRendereAndTemplate();   // vytváří user error
            }
        }
    }

    private function checkRendererTemplateConsistency(RendererInterface $renderer, TemplateInterface $template) {
        if (isset($template)) {
            if ( !($renderer instanceof $templateDefaultRendererClass)) {
                throw new BadRendererForTemplateException(
                        "Template ".get_called_class($template)." vyžaduje renderer typu ".$template->getDefaultRendererService().". "
                        . "Zadaný renderer ".get_called_class($renderer)." nelze použít pro renderování template.");
            }
        }
    }

    private function useFallbackRendereAndTemplate() {
        $containerClass = isset($this->rendererContainer) ? get_class($this->rendererContainer) : 'null';
        $templateClass = isset($this->template) ? get_class($this->template) : 'null';
        user_error("Nepodařilo se získat renderer, je použit fallback renderer a fallback template. "
                . " Renderer name: {$this->rendererName},"
                . " renderer container: $containerClass,"
                . " template: {$templateClass}", E_USER_NOTICE);
        $this->renderer = new FallbackRenderer();
        $this->template = new FallbackTemplate();
    }
}