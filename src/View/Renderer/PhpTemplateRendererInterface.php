<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer;

/**
 *
 * @author pes2704
 */
interface PhpTemplateRendererInterface extends TemplateRendererInterface, PhpTemplateFunctionsInterface, RendererRecordableInterface {

    /**
     * Nastaví data sdílená všemi šablonami. Tato data jsou extrahována vždy při renderování každé šablony. Při opakovaném renderování šablony jsou opakovaně extrahována, extrahované proměnné
     * nejsou sdílení, jsou vždy v lokálním kontextu.
     * 
     * @param iterable $sharedDate
     */
    public function setSharedData(iterable $sharedDate);
}
