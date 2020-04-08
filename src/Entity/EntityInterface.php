<?php
/**
 *
 * @author pes2704
 */
namespace Pes\Entity;

interface EntityInterface {
    public function isPublicProperty($name);
    public function getValues();
    public function getNames();
    public function getValuesAssoc();        
}
