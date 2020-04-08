<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer;

/**
 * Description of ContextDataRenderer
 *
 * @author pes2704
 */
class ContextDataRenderer {

    public function getString() {
        // loguje to co bylo potřeba pro již proběhlé renderování
        if ($this->context instanceof ContextDataInterface) {
            $this->context->logStatus(str_replace('\\', ' ', get_called_class()).'.log', $this->context);
        }
        
        if ($this->parts) {
            $str = $this->convertToString($this->parts);
        } else {
            $render = $this->render();
            if ($render instanceof Framework_View_ViewInterface) {   // metoda render vrací view (return $this;) ($render === $this)
                $str =  $this->convertToString($this->parts);
            } elseif (is_array($render)) {
                $str =  $this->convertToString($this->parts).$this->convertToString($render);  // render vytvoří $this->parts (nebo ne) a vrací pole
            } elseif (is_scalar($render)) {
                $str = $this->convertToString($this->parts).$render;  // render vytvoří $this->parts (nebo ne) a vrací skalár
            } else {
                $str =  $this->convertToString($this->parts);  // metoda render nevrací nic - asi chybí return $this; - mohla vytvořit $this->part a nevracet nic
            }
        }
        return $str ? $str : '';
    }}
