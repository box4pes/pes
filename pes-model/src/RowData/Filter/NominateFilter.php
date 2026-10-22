<?php

namespace Pes\Model\RowData\Filter;

/**
 * Description of NominateFilter
 *
 * @author pes2704
 */
class NominateFilter extends \FilterIterator implements NominateFilterInterface {

    private $names = [];

    /**
     * {@inheritDoc}
     *
     * @param array $keys
     * @return void
     */
    public function nominate(array $keys=[]): void {
        $this->names = $keys;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function accept(): bool {
        $k = $this->key();
        return in_array($this->key(), $this->names);  // nelze použít isset() - akceptuje položky, které existují a nejsou null
    }
}
