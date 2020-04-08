<?php

namespace Pes\View\Renderer;

use Pes\View\Renderer\Exception\UnsupportedTemplateException;

use Pes\View\Template\TemplateInterface;
use Pes\Dom\Node\NodeInterface;
use Pes\Dom\Node\Tag\TagInterface;
use Pes\Dom\Node\Text\TextInterface;

use Pes\View\Recorder\RecorderProviderInterface;

/**
 * Description of TagRenderer
 *
 * @author pes2704
 */
class NodeRenderer implements NodeRendererInterface, RendererRecordableInterface {

    const SEPARATOR = PHP_EOL;

    private $template;

    private $separator=self::SEPARATOR;

    /**
     * @var RecorderProviderInterface
     */
    private $recorderProvider;

    /**
     * Přijímá separátor, string, který bude  vložen vždy mezi jendnotlivé vyrenderované nody (tagy). Defaultní hodnota je PHP_EOL, t.j. odřádkování.
     * @param string $separator
     */
    public function __construct($separator=self::SEPARATOR) {
        $this->separator = $separator;
    }

    public function setTemplate(TemplateInterface $template) {
        if ($template->getDefaultRendererService() !== NodeTemplate::class) {
            throw new UnsupportedTemplateException("Renderer ". get_called_class()." nepodporuje renderování template typu ". get_class($this->template));
        }
        $this->template = $template;
    }

    public function setRecorderProvider(RecorderProviderInterface $recorderProvider): RendererRecordableInterface {
        $this->recorderProvider = $recorderProvider;
        return $this;
    }

    /**
     * Data se nijak nezpracovávají!!
     * @param type $data
     * @return string
     */
    public function render( $data=NULL) {
        assert(!isset($data), 'Není implementováno zpracování dat. Data se nijak nezpracovávají!! ');

        return $this->renderNode($this->template->getNode());
    }

    /**
     *
     * @param NodeInterface $node
     * @return string
     */
    private function renderChildren(NodeInterface $node) {
        foreach ($node->getChildrens() as $child) {
            $pieces[] = $this->renderNode($child);
        }
        return implode($this->separator, $pieces ?? ['']);
    }

    /**
     * @param NodeInterface $node
     * @return string
     */
    private function renderNode($node) {
        if ($node instanceof TagInterface) {
            $attributes = $node->getAttributesNode()->getString();
            $attributesString = $attributes ? ' '.$attributes : '';
            // potomek je párový tag - rekurzivně volám renderování
            if ($node->isPairTag()) {
                // tag je párový - všechny tagy ve frameworku jsou párové s výjimkou pouze povinně nepárových tagů (dobrovolně párové tagy jsou implementovány jako párové
                // párový tag má počáteční a koncovou značku a může mít obsah - buď text node (Node\Data\Text) nebo potomkovské tagy
                $pieces[] = "<{$node->getName()}$attributesString>";
                $pieces[] = $this->renderChildren($node);
                $pieces[] = "</{$node->getName()}>";
            } else {
                // tag je povinně nepárový - nemůže mít obsah
                $pieces[] = "<{$node->getName()}$attributesString />";
            }
        } elseif ($node instanceof TextInterface) {
            $pieces[] = $node->getText();
        } else {
            $pieces[] = $this->renderChildren($node);
        }
        return implode($this->separator, $pieces);
    }
}
