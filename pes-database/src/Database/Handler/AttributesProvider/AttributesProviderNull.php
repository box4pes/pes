<?php
namespace Pes\Database\Handler\AttributesProvider;

/**
 * AttributesProviderNull poskytuje základní nastavení, které očekávají ostatní části frameworku.
 * Objekt typu AttributesProviderInterface je povinným parametrem přo volání konstruktoru Handleru.
 * Pro případ, kdy skutečně nechci nastavivat žádné atributy, je možno použít tento Attributes provider.
 * @author pes2704
 */
final class AttributesProviderNull extends AttributesProviderAbstract {

    /**
     * @return array
     */
    public function getAttributesArray(array $attributes=[]) {
        if($this->logger) {
            $this->logger->debug(__CLASS__.': Nevytvořeny žádné atributy.  Parametry jsou ignorovány.');
        }
        return [];
    }
}
