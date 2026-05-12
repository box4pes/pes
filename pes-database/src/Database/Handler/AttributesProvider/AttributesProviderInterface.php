<?php
namespace Pes\Database\Handler\AttributesProvider;

use Psr\Log\LoggerAwareInterface;

/**
 *
 * @author pes2704
 */
interface AttributesProviderInterface extends LoggerAwareInterface {

    public function getAttributesArray(array $attributes=[]);
}
