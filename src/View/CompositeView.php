<?php

namespace Pes\View;

/**
 *
 * @author pes2704
 */
class CompositeView extends View implements CompositeViewInterface {

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     *
     * @var ViewInterface in \SplObjectStorage
     */
    private $componentViews;

    public function __construct() {
        $this->componentViews = new \SplObjectStorage();
    }

    /**
     * Metoda pro přidání komponentních view. Při renderování kompozitního view budou renderována komponentní view a vygenerovaný výsledek bude vložen
     * do kompozitního view na místo proměnné zadané zde jako jméno.
     * Jednotlivá komponentní view budou renderována bez předání (nastavení) template a dat, musí mít tedy před renderováním kompozitního view nastavenu šablonu
     * a data pokud je potřebují pro své renderování.
     *
     * @param \Pes\View\ViewInterface $componentView Komponetní view
     * @param string $name Jméno proměnné v kompozitním view, která má být nahrazena výstupem zadané komponentní view
     * @return \Pes\View\CompositeViewInterface
     */
    public function appendComponentView(ViewInterface $componentView, $name): CompositeViewInterface {
        $this->componentViews->attach($componentView, $name);
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
    /**
     * Metoda renderuje všechny vložené component renderery. Výstupní kód z jednotlivých renderování vkládá do kontextu
     * composer rendereru vždy pod jménem proměnné, se kterým byl component renderer přidán. Nakonec renderuje
     * compose renderer. Při renderování compose rendereru použije data zadaná jako parametr, pokud nebyla zadána, data zadaná metodou setData($data).
     *
     * @param mixed $data
     * @return string
     */
    public function getString($data=NULL) {

        $composeViewData = array();
        if (count($this->componentViews)>0) {
            foreach ($this->componentViews as $componentView) {
                $composeViewData[$this->componentViews->getInfo()] = $componentView->getString();
            }
        }
        // $composeViewData se musí spojit se správnými daty už tady. Buď s $data, pokud byla zadána nebo $this->data.
        if($this->data) {
            $data = array_merge($data ?? $this->data, $composeViewData);
        } else {
            $data = $composeViewData;
        }
        return parent::getString($data);
    }
}
