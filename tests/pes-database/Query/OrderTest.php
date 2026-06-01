<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\Order;
use Pes\Query\OrderingEnum;

/**
 * Description of OrderTest
 *
 * @author pes2704
 */
class OrderTest extends TestCase {
    /**
     * @var Order
     */
    protected $order;

    public function setUp(): void {
        $this->order = (new Order())
            ->addOrdering('a', OrderingEnum::DESCENDING)
            ->addOrdering('n', OrderingEnum::ASCENDING)
            ->addOrdering('x', OrderingEnum::DESCENDING);
    }

    public function testGetSqlString() {
        $str = $this->order->getSqlString();
        $this->assertEquals('a DESC, n ASC, x DESC', $str);
    }

    public function testIterator() {
        foreach ($this->order as $ordering) {
            $arr[$ordering['attribute']] = $ordering['type'];
        }
        $this->assertEquals(['a'=>'DESC', 'n'=>'ASC', 'x'=>'DESC'], $arr);
    }
}