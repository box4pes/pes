<?php
namespace Pes\Repository;

use Pes\Collection\MapCollectionInterface;
use Pes\Collection\MapCollection;
use Pes\Criteria\CriteriaInterface;

/**
 * Description of Repo
 *
 * @author pes2704
 */
class MapRepository implements MapRepositoryInterface {
    /**
     *
     * @var MapCollectionInterface
     */
    private $collection = NULL; 

    public function __construct() {
    }
    
    function count() {
        if (NULL == $this->collection) {
            $this->createCollection(); 
        }
        return $this->collection->count();
    }
    
    function set($index, $value) {
        if (NULL == $this->collection) {
            $this->createCollection(); 
        }
        return $this->collection->set($index, $value);
    }  
    
    function get($index) {
        if (NULL == $this->collection) {
            $this->createCollection();
        } 
        return $this->collection->get($index);
    }
    function remove($index) {
        if (NULL == $this->collection) {
            $this->createCollection();
        } 
        return $this->collection->remove($index);
    }
    //Create 
    private function createCollection() {
        $this->collection = new MapCollection();
    }
}
