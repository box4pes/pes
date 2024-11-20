<?php

namespace Pes\View;

use Pes\View\InheritViewModelInterface;
use Pes\View\InheritDataInterface;

use ArrayObject;

/**
 *
 * @author pes2704
 */
class CompositeView extends View implements CompositeViewInterface {

    /**
     *
     * @var ArrayObject of View
     */
    protected $componentViews;

    /**
     * Metoda pro přidání komponentních view. 
     * 
     * Pokud je komponentní view typu InheritDataViewInterface, v tuto chvíli "zdědí" context data z kompozitního view.
     * Případný pozdější pokus o nahrazení dat komponentního view (rodiče) vyhodí výjimku ve View.
     *
     * Před renderováním kompozitního view budou nejprve renderována komponentní view a vygenerovaný výsledek bude vložen
     * do kompozitního view na místo proměnné zadané zde jako jméno. Pokud kompozitní view je null, proměnná je nahrazena prázdným retězcem.
     *
     * @param \Pes\View\ViewInterface $componentView Komponetní view nebo null
     * @param string $name Jméno proměnné v kompozitním view, která má být nahrazena výstupem zadané komponentní view
     * @return ViewInterface
     */
    public function appendComponentView(ViewInterface $componentView, $name): ViewInterface {
        $components = $this->provideComponentViews();
        $components->offsetSet($name, $componentView);
        return $this;
    }

    /**
     * {@inheritDoc}
     * 
     * Pokud jednotlivá (i jen některá) komponentní view jsou typu InheritDataViewInterface jsou jim předána data nadřazeného (kompozitního) view metodou inheritData().
     * 
     * @param iterable $componentViews
     * @return ViewInterface
     */
    public function appendComponentViews(iterable $componentViews): ViewInterface  {
        $components = $this->provideComponentViews();
        foreach ($componentViews as $name => $componentView) {
            $components->offsetSet($name, $componentView);
        }
        return $this;
    }

    public function getComponentView($name): ?ViewInterface {
        return $this->componentViews->offsetExists($name) ? $this->componentViews->offsetGet($name) : null;
    }

    public function getComponentViewsArray(): array {
        return $this->componentViews->getArrayCopy();
    }

    /**
     * Zavolá beforeRenderingHook(), nalezne vhodný renderer pomocí metody resolveRenderer(), renderuje kolekci komponentních views, výsledky jejich renderování přidá do kontextu vždy se jménem, se kterým byl komponentní view přidán
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
     * Metoda renderuje všechny vložené component view.
     *
     * Výstupní řetězec z jednotlivých renderování přidá do kontextu
     * tohoto (composite) view vždy pod jménem proměnné, se kterým byl přidán component view.
     *
     * Pokud komponentní view implementuje InheritDataInterface, předá data tohoto (kompozitního) view do komponentu pomocí metody inheritData().
     *
     * @return string
     */
    protected function renderComponets(): void {
        if (is_iterable($this->componentViews)) {
            $data = $this->provideData();
            foreach ($this->componentViews as $name => $componentView) {
                if (isset($componentView)) {
                    if ($componentView instanceof InheritDataInterface) {
                        /** @var InheritDataInterface $componentView */
                        $componentView->inheritData($this->contextData->getContextVariable($name), []);  // metoda inheritData přijímá iterable - nastavuje default hodnoty metody getContextVariable na []
                    }        
                    if ($componentView instanceof InheritViewModelInterface) {
                        /** @var InheritViewModelInterface $componentView */
                        $componentView->inheritViewModel($this->contextData);
                    }        
                    $data[$name] = $componentView->getString();
                }
                    
            }
        }
    }
}
