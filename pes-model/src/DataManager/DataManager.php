<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\DataManager;

use Pes\Model\Dao\DaoEditInterface;

use Pes\Model\RowData\RowDataInterface;

/**
 * Description of DataManager
 *
 * @author pes2704
 */
abstract class DataManagerAbstract implements DataManagerInterface {

    /**
     * @var DaoEditInterface
     */
    private $dao;
    private $persitedData;
    private $dataToAdd;
    private $dataToRemove;

    private $flushed = false;

    public function __construct(DaoEditInterface $dao) {
        $this->dao = $dao;
        $this->persitedData = new \ArrayObject();
        $this->dataToAdd = new \ArrayObject();
        $this->dataToRemove = new \ArrayObject();
    }

    public function get(array $id): ?RowDataInterface {
        $index = $this->indexFromKeyParams($id);
        if ($this->dataToAdd->offsetExists($index)) {
            return $this->dataToAdd->offsetGet($index);
        } elseif (!$this->persitedData->offsetExists($index)) {
            $data = $this->dao->get($id);
            $this->persitedData->offsetSet($index, $data);
            $this->flushed = false;
        }
        return $this->persitedData->offsetExists($index) ? $this->persitedData->offsetGet($index) : null;
    }

    public function find($whereClause="", $touplesToBind=[]): iterable {
        $collection = [];
        foreach ($this->dao->find($whereClause, $touplesToBind) as $rowData) {
            $index = $this->indexFromRowData($rowData);
            if ($this->dataToAdd->offsetExists($index)) {
                $collection[] = $this->dataToAdd->offsetGet($index);
            } elseif ($this->persitedData->offsetExists($index)) {
                $collection[] = $this->persitedData->offsetGet($index);
            } else {
                $this->persitedData->offsetSet($index, $rowData);
                $collection[] = $rowData;
                $this->flushed = false;
            }
        }
        return $collection;
    }

    public function set($index, RowDataInterface $data): void {
        if ($this->persitedData->offsetExists($index)) {
            $this->persitedData->offsetSet($index, $data);
        } else {
            $this->dataToAdd->offsetSet($index, $data);
            $this->flushed = false;
        }
    }

    public function unset($index): void {
        if ($this->persitedData->offsetExists($index)) {
            $this->dataToRemove->offsetSet($index, $this->persitedData->offsetGet($index));
            $this->persitedData->offsetUnset($index);
            $this->flushed = false;
        }
        if ($this->dataToAdd->offsetExists($index)) {
            $this->dataToAdd->offsetUnset($index);
        }
    }

    public function flush(): void {
        if ($this->flushed) {
            return;
        }
        foreach ($this->dataToAdd as $rowData) {
            $this->dao->insert($rowData);
        }
        foreach ($this->persitedData as $rowData) {
            if ($rowData->isChanged()) {
                $this->dao->update($rowData);
            }
        }
        foreach ($this->dataToRemove as $rowData) {
            $this->dao->delete($rowData);
        }
        $this->flushed = true;
    }

}

