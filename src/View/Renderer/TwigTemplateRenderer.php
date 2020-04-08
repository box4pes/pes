<?php

namespace Pes\View\Renderer;

use Pes\View\Template\TemplateInterface;
use Pes\View\Template\TemplateInterface;
use Pes\View\Renderer\Exception\UnsupportedTemplateException;

/**
 * Renderer používající pro generování obsahu template objekt, který jako šablony používá Twig šablony.
 * Je to dekorátor pro Twig_Environment template objekt.
 *
 * @author pes2704
 */
class TwigTemplateRenderer extends TemplateRendererAbstract implements TwigTemplateRendererInterface {

    private $template;

    public function setTemplate(TemplateInterface $template) {
        if ($template->getDefaultRendererService() !== TwigTemplateRenderer::class) {
            throw new UnsupportedTemplateException("Renderer ". get_called_class()." nepodporuje renderování template typu ". get_class($this->template));
        }
        $this->template = $template;
    }

    /**
     * Vrací výstup získaný ze zadaného template objektu.
     * Metoda implementuje metodu rozhraní render(). Volá metodu render() Twig objektu.
     *
     * @param iterable $data Pole nebo objekt Traversable
     * @return string
     */
    public function render(iterable $data=NULL) {
        return $this->template->render($data);
    }
}
