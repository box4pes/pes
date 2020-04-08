<?php

namespace Pes\View\Renderer;

use Pes\Type\ContextDataInterface;

/**
 * Description of ContextDataRendererInterface
 *
 * @author pes2704
 */
interface ContextDataRendererInterface extends TemplateRendererInterface {
    public function render(ContextDataInterface $data);
}
    