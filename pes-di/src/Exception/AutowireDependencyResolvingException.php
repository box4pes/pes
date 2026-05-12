<?php

namespace Pes\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 *
 * @author pes2704
 */
class AutowireDependencyResolvingException extends \LogicException implements AutowireExceptionInterface { }
