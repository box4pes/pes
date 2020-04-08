<?php
namespace Pes\Database\Handler\AttributesProvider;

use Psr\Log\LoggerInterface;

/**
 *
 * @author pes2704
 */
abstract class AttributesProviderAbstract implements AttributesProviderInterface {

    protected $attributes = [];
    protected $logger;

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Nastaví atrinuty tak, že lokálně nastavené attr jsou přepsány attr z parametru, pokud mají stejný klíč
     * @param array $attributes
     * @return array
     */
    public function getAttributesArray(array $attributes=[]) {
        $this->attributes = $this->attributes + $attributes;
        if ($this->logger) {
            $this->logger->info(__CLASS__.' Nastaveny hodnoty atributů handleru (PDO): {attributes}', array('attributes'=>print_r($this->attributes, TRUE)));
        }
        return $this->attributes;  // lokálně nastavené attr jsou přepsány attr z parametru, pokud mají stejný klíč
    }
}
