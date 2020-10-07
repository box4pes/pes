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

    protected $data;

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

    public function setFallbackRenderer(RendererInterface $renderer): ViewInterface {
        $this->fallbackRenderer = $renderer;
        return $this;
    }

    public function setFallbackRendererName($fallbackRendererName): ViewInterface {
        $this->fallbackRendererName = $fallbackRendererName;
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
     * <li>Data zadaná jako parametr metody.</l>
     * <li>Pokud parameter data není zadán, renderuje data zadaná metodou setData (view->setData($data)).</li>
     * </ol>
     *
     * Použije renderer:
     * <ol>
     * <li>renderer nastavený metodou View->setRendererName()</li>
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
        $renderer = $this->resolveRenderer();
        return $renderer->render($data ?? $this->data);
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

    ##### private methods ###########################

    private function resolveRenderer() {
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
            $renderer = $this->getFallbackRendereAndTemplate();
        }
        return $renderer;
    }

    private function getRendererByName($rendererName) {
        if (!isset($this->rendererContainer)) {
            throw new LogicException("Nelze získat renderer podle jména rendereru, není zadán renderer kontejner.");
        } else {
            return $this->rendererContainer->get($rendererName);
        }
    }

    private function getDefaultTemplateRenderer(TemplateInterface $template) {
        if (!isset($this->rendererContainer)) {
            throw new LogicException("Nelze získat renderer jako default renderer šablony, není zadán renderer kontejner.");
        } else {
            return $this->rendererContainer->get($template->getDefaultRendererService());
        }
    }

    private function setRendererTemplate(RendererInterface $renderer, TemplateInterface $template=null) {
        if ($renderer instanceof TemplateRendererInterface) {
            if ($template) {
                $this->checkRendererTemplateCompatibility($renderer, $template);
                $renderer->setTemplate($template);
            }
        }
    }

    private function resolveRendererOld() {
        if (isset($this->renderer)) {
            $renderer = $this->renderer;
        } else {
            if (isset($this->rendererName)) {
                $renderer = $this->rendererContainer->get($this->rendererName);
                // pokud je renderer i $this->template a renderer je typu TemplateRendererInterface, předá se $this->template resolvovanému rendereru - je třeba ověřit kompatibilitu
                if ($this->template) {
                    $this->checkRendererTemplateCompatibility($renderer, $this->template);
                }
            } elseif ($this->template) {
                $renderer = $this->rendererContainer->get($this->template->getDefaultRendererService());
            } else {
                $renderer = $this->useFallbackRendereAndTemplate();   // vytváří user error
            }
        }
        return $renderer;
    }

    private function checkRendererTemplateCompatibility(RendererInterface $renderer, TemplateInterface $template) {
        $templateDefaultRendererClass = $template->getDefaultRendererService();
        if ( !($renderer instanceof $templateDefaultRendererClass)) {
            throw new BadRendererForTemplateException(
                    "Template ".get_called_class($template)." vyžaduje renderer typu $templateDefaultRendererClass. "
                    . "Zadaný renderer ".get_called_class($renderer)." nelze použít pro renderování template.");
        }
    }

    private function getFallbackRendereAndTemplate() {
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
}