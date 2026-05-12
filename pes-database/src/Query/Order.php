<?php
namespace Pes\Query;

use Pes\Query\OrderingEnum;

/**
 * Description of Order
 *
 * @author pes2704
 */
class Order implements OrderInterface {
    private $order = array();
    private $orderType;
    
    public function __construct() {
        $this->orderType = new OrderingEnum();
    }
    
    public function addOrdering($attrinute, $orderingTypeValue) {
        $enum = $this->orderType;
        $orderType = $enum($orderingTypeValue);  //pro chybný parametr vyhodí výjimku
        $this->order[] = array('attribute'=>$attrinute, 'type'=>$orderType);
        return $this;
    }
    
    /**
     * Vrací iterátor vhodný pro víceúrovňové třídění (multisort). 
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->order);
    }
    
    /**
     * Vrací řetězec ve formátu SQL
     * @return string
     */
    public function getSqlString() {
        $str = '';
        foreach ($this->order as $value) {
            $arr[] = $value['attribute'].' '.$value['type'];
        }
        if (isset($arr)) {
            return implode(', ', $arr);
        }
    }
}
