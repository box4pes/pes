<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Container;

/**
 *
 * @author pes2704
 */
interface AutowiringContainerInterface extends ContainerSettingsAwareInterface {
    public function throwExceptions($throwExceptions=\FALSE);
}
