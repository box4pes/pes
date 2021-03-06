<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer\Container;

use Psr\Container\ContainerInterface;
use Pes\View\Recorder\RecorderProviderInterface;

/**
 *
 * @author pes2704
 */
interface TemplateRendererContainerInterface extends ContainerInterface {
    public function setRecorderProvider(RecorderProviderInterface $recorderProvider);
}
