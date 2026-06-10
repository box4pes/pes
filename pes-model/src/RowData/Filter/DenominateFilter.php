<?php

namespace Pes\Model\RowData\Filter;

/**
 * Description of NominateFilter
 *
 * @author pes2704
 */
class DenominateFilter extends \FilterIterator implements DenominateFilterInterface {

    private $names = [];

    /**
     * {@inheritdoc}
     *
     * @param array $keys
     * @return void
     */
    public function denominate(array $keys=[]): void {
        $this->names = $keys;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function accept(): bool {
        return !in_array($this->key(), $this->names);
    }
}
