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
use Pes\View\Renderer\RendererModelAwareInterface;
use Pes\View\Template\TemplateInterface;

use Psr\Container\ContainerInterface;

/**
 * use použit jako definice fallback rendereru - použit pro renderování, pokud nebyl získán žádný uživatelsky zadaný renderer.
 */
use Pes\View\Renderer\ImplodeRenderer as FallbackRenderer;
use Pes\View\Template\ImplodeTemplate as FallbackTemplate;

use Pes\View\Exception\{
    BadRendererForTemplateException, NoViewmodelViewException
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

    protected $viewModel;

    /**
     * Lze nastavit data pro renderování. Tato data budou použita metodou render().
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function setData(iterable $data): ViewInterface {
        $this->data = $data;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param type $viewModel
     * @return ViewInterface
     */
    public function setViewModel($viewModel): ViewInterface {
        $this->viewModel = $viewModel;
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
     * Nastaví template objekt pro renderování. Tato template bude použita metodou render(). Pokud parametr template je null, dojde k použití dalších
     * možností při resolvování rendereru - vit resolveRenderer().
     * @param TemplateInterface $template
     * @return \Pes\View\ViewInterface
     */
    public function setTemplate(TemplateInterface $template = null): ViewInterface {
        $this->template = $template;
        return $this;
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
        $renderer = $this->resolveRenderer();
        if ($renderer instanceof RendererModelAwareInterface) {
            if(!isset($this->viewModel)) {
                throw new NoViewmodelViewException("Není nastaven view model. Nalezený renderer ". get_class($renderer)." je typu RendererModelAwareInterface a vyžasuje nastavení view modelu.");
            }
            $renderer->setViewModel($this->viewModel);
            $str = $renderer->render();
        } else {
            $str = $renderer->render($this->data);
        }
        return $str;
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

    /**
     * Renderuje data s použitím případné template a vytvoří obsah.
     *
     * Renderuje data:
     * <ol>
     * <li>Data zadaná jako parametr metody.</li>
     * <li>Pokud parameter data není zadán, renderuje data zadaná metodou setData (view->setData($data)).</li>
     * </ol>
     *
     * Použije renderer vybraný v závislosti na kombinaci nastavených template, renderer, renderer name, fallback renderer:
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
     * - Výchozí režim je pro renderování template je použití defaultního rendereru definovaného v template.
     * Defaultní template renderer lze přebít zadáním rendereru metodou setRenderer(). Od okažiku nastavení rendereru jsou všechny template renderovány zadaným rendererem.
     *
     * - Pro renderování bez template je samozřejmě nutné nastavit renderer metodou setRenderer() vždy.
     *
     */
    protected function resolveRenderer(): RendererInterface {
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

    protected function getRendererByName($rendererName): RendererInterface {
        if (!isset($this->rendererContainer)) {
            throw new LogicException("Nelze získat renderer podle jména rendereru, není zadán renderer kontejner.");
        } else {
            return $this->rendererContainer->get($rendererName);
        }
    }

    protected function getDefaultTemplateRenderer(TemplateInterface $template): RendererInterface {
        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Nelze získat renderer jako default renderer šablony, není zadán renderer kontejner.");
        } else {
            return $this->rendererContainer->get($template->getDefaultRendererService());
        }
    }

    protected function setRendererTemplate(RendererInterface $renderer, TemplateInterface $template=null) {
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

    private function resolveRendererOld() {
        if (isset($this->renderer)) {
            $renderer = $this->renderer;
        } else {
            if (isset($this->rendererName)) {
                $renderer = $this->rendererContainer->get($this->rendererName);
                // pokud je renderer i $this->template a renderer je typu TemplateRendererInterface, předá se $this->template resolvovanému rendereru - je třeba ověřit kompatibilitu
                if ($this->template) {
                    if (!$this->checkRendererTemplateCompatibility($renderer, $this->template)) {
                        throw new BadRendererForTemplateException(
                                "Template ".get_class($template)." vyžaduje renderer typu $templateDefaultRendererClass. "
                                . "Zadaný renderer ".get_class($renderer)." nelze použít pro renderování template.");
                    }
                }
            } elseif ($this->template) {
                $renderer = $this->rendererContainer->get($this->template->getDefaultRendererService());
            } else {
                $renderer = $this->useFallbackRendereAndTemplate();   // vytváří user error
            }
        }
        return $renderer;
    }

    protected function checkRendererTemplateCompatibility(RendererInterface $renderer, TemplateInterface $template): bool {
        $templateDefaultRendererClass = $template->getDefaultRendererService();
        return ($renderer instanceof $templateDefaultRendererClass);
    }

    protected function getFallbackRendereWithTemplate(): RendererInterface {
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