<?php

namespace Pes\View;

use Pes\View\InheritDataViewInterface;

use ArrayObject;

/**
 *
 * @author pes2704
 */
class CollectionView extends View implements CollectionViewInterface {

    /**
     *
     * @var ArrayObject of View
     */
    protected $componentViews;

    /**
     * Přijímá dvojici iterable kolekci view (položky typu ViewInterface) .
     * Kompozitní view při renderování nahradí proměnou daného jména výsledkem renderování kolekce komponentních view. Jednotlivá view z kolekce převede na string voláním metodu __toString().
     *
     *
     * @param iterable $componentViewCollection Kolekce view, položky typu ViewInterface
     * @return ViewInterface
     */
    public function appendComponentViewCollection(iterable $componentViewCollection): ViewInterface {
        $componentViews = $this->provideComponentViews();
        foreach ($componentViewCollection as $view) {
            $componentViews->append($view);
        }
        return $this;
    }

    /**
     * Zavolá beforeRenderingHook(), nalezne vhodný renderer pomocí metody resolveRenderer(), renderuje kolekci komponentních views, výsledky jejich renderování přidá do kontextu
     * a renderuje s použitím kontextu.
     *
     * @return string
     */
    public function getString() {
        // aktivity před renderováním - zde může dojít k přidání template, rendereru, dat apod.
        $this->beforeRenderingHook();
        // renderování komponentních view - pokud některé views používají stejný renderer (typicky PhpTemplateRenderer), používá se tatáž instance rendereru poskytnutá (singleton)
        // službou Renderer kontejneru - proto musí být nejdříve renderer použit pro jednotlivé komponenty a potom teprve pro renderování komposit view, resolveRenderer() při použití PhpTemplate
        // nastaví rendereru jeho template - to mění vnitřní stav rendereru!, renderer není bezstavový
        $this->renderComponets();
        // renderování kompozitu
        $renderer = $this->resolveRenderer();
        return $renderer->render($this->contextData);  // předává data jako pole
    }

#### protected ####

    /**
     * Poskytne componentViews, pokud neexistují, vytvoří nové (prázdné). Metoda tak vrací componentViews vždy, slouží k získání componentViews předtím, než do nich chce nějaká metoda přidávat.
     *
     * @return ArrayObject
     */
    protected function provideComponentViews(): ArrayObject {
        if (!isset($this->componentViews)) {
            $this->componentViews = new ArrayObject();
        }
        return $this->componentViews;
    }

    /**
     * Metoda renderuje všechny vložené component view v kolekci.
     *
     * Výstupní řetězec z jednotlivých renderování vkládá do kontextu
     * tohoto (composite) view metodou append(), t.j. bez jména (indexu).
     *
     * Pokud komponentní view v kolekci implementuje InheritDataInterface, předá data tohoto (kompozitního) view do komponentu pomocí metody inheritData().
     *
     * @return string
     */
    protected function renderComponets(): void {
        if (is_iterable($this->componentViews)) {
            $data = $this->provideData();
            foreach ($this->componentViews as $componentView) {
                $data->append($this->renderComponent($componentView));
            }
        }
    }

    private function renderComponent($componentView) {
        if ($componentView instanceof InheritDataViewInterface) {
            /** @var InheritDataViewInterface $componentView */
            $componentView->inheritData($this->contextData);
        }
        return $componentView->getString();
    }
}
