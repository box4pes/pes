<?php

namespace Pes\View;

use Pes\View\InheritDataViewInterface;

use ArrayObject;

/**
 *
 * @author pes2704
 */
class CompositeView extends View implements CompositeViewInterface {


    /**
     * Metoda pro přidání komponentních view. Při renderování kompozitního view budou renderována komponentní view a vygenerovaný výsledek bude vložen
     * do kompozitního view na místo proměnné zadané zde jako jméno. Pokud kompozitní view je null, proměnná je nahrazena prázdným retězcem.
     * Jednotlivá komponentní view budou renderována bez předání (nastavení) template a dat, musí mít tedy před renderováním kompozitního view nastavenu šablonu
     * a data pokud je potřebují pro své renderování.
     *
     * @param \Pes\View\ViewInterface $componentView Komponetní view nebo null
     * @param string $name Jméno proměnné v kompozitním view, která má být nahrazena výstupem zadané komponentní view
     * @return ViewInterface
     */
    public function appendComponentView(ViewInterface $componentView, $name): ViewInterface {
        if (!isset($this->componentViews)) {
            $this->componentViews = new ArrayObject();
        }
        $this->componentViews->offsetSet($name, $componentView);
        return $this;
    }

    /**
     * Metoda pro přidání komponentních view jako pole nebo \Traversable objekt. Interně volá metodu appendComponentView()
     * @param iterable $componentViews
     * @return ViewInterface
     */
    public function appendComponentViews(iterable $componentViews): ViewInterface  {
        foreach ($componentViews as $name => $componentView) {
            $this->appendComponentView($componentView, $name);
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
     * Metoda renderuje všechny vložené component view.
     *
     * Výstupní řetězec z jednotlivých renderování vkládá do kontextu
     * tohoto (composite) view vždy pod jménem proměnné, se kterým byl component view přidán.
     *
     * Pokud komponentní view implementuje InheritDataInterface, předá data tohoto (kompozitního) view do komponentu pomocí metody inheritData().
     *
     * @return string
     */
    protected function renderComponets(): void {
        if (is_iterable($this->componentViews)) {
            foreach ($this->componentViews as $name => $componentView) {
                $this->contextData[$name] = $this->renderComponent($componentView);
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
