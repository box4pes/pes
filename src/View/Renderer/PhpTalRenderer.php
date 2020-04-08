<?php

namespace Pes\View\Renderer;

use Pes\View\Template\TemplateInterface;
use \PHPTAL;
use Pes\View\Renderer\Exception\UnsupportedTemplateException;

/**
 * Renderer používající pro generování obsahu template objekt, který jako šablony používá PHPTAL šablony.
 * Je to dekorátor pro PHPTAL template objekt.
 *
 * @author pes2704
 */
class PhpTalRenderer extends TemplateRendererAbstract implements PhpTalRendererInterface {

    private $template;

    public function setTemplate(TemplateInterface $template) {
        if ($template->getDefaultRendererService() !== PhpTalRendererInterface::class) {
            throw new UnsupportedTemplateException("Renderer ". get_called_class()." nepodporuje renderování template typu ". get_class($this->template));
        }
        $this->template = $template;
    }

    /**
     * Vrací výstup získaný ze zadaného template objektu.
     * Metoda implementuje metodu rozhraní render(). Volá metodu execute() PHPTAL objektu.
     *
     * @param mixed $data Pole nebo objekt Traversable nebo Closure, kterí vrací pole nebo objekt Traversable
     * @return string
     */
    public function render(iterable $data=NULL) {
        if ($data) {
            foreach($data as $klic => $hodnota) {
                $this->template->$klic = $hodnota;
            }
        }
        return $this->template->execute();
    }
}
