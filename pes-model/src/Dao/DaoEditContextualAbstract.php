<?php

namespace Pes\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Pes\Model\Builder\SqlInterface;
use Pes\Model\Context\ContextProviderInterface;
use Pes\Model\RowData\RowDataInterface;

/**
 * Description of DaoContextualAbstract
 *
 * @author pes2704
 */
abstract class DaoEditContextualAbstract extends DaoEditAbstract implements DaoContextualInterface {

    /**
     * @var ContextProviderInterface
     */
    protected $contextFactory;


    public function __construct(HandlerInterface $handler, SqlInterface $sql, $fetchClassName, ContextProviderInterface $contextFactory=null) {
        parent::__construct($handler, $sql, $fetchClassName);
        $this->contextFactory = $contextFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $id
     * @return RowDataInterface|null
     */
    public function get(array $id): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $this->sql->touples($this->getPrimaryKeyAttributes())
                    )
                );
        $touplesToBind = $this->getPrimaryKeyPlaceholdersValues($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getOutOfContext(array $id): ?RowDataInterface {
        return parent::get($id);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $unique
     * @return RowDataInterface|null
     */
    public function getUniqueOutOfContext(array $unique): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                                        $this->getContextConditions(),
                                        $this->sql->touples(array_keys($unique)))
                                  );
        $touplesToBind = $this->getPrimaryKeyPlaceholdersValues($unique);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
    // TODO: dodělat contextual - ?? je potřeba současně kontextové metody i out of context metody
    public function findNonUnique(array $nonUniqueKey): iterable {
        if ($this instanceof DaoContextualInterface) {
            $where = $this->sql->where($this->sql->and($this->getContextConditions(), $whereBinds));
        } else {
            $where = $this->sql->where($this->sql->and($this->sql->touples($whereBinds)));
        }
        $this->findNonUnique($nonUniqueKey);
    }

    public function findNonUniqueOutOfContext(array $nonUniqueKey): iterable  {

    }

    /**
     * {@inheritDoc}
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return iterable
     */
    public function find($whereClause = "", $touplesToBind = []): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                        $this->getContextConditions(),
                        $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * {@inheritDoc}
     *
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return iterable
     */
    public function findOutOfContext($whereClause = "", $touplesToBind = []): iterable {
        return parent::find($whereClause, $touplesToBind);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable
     */
    public function findAll(): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->getContextConditions());
        return $this->selectMany($select, $from, $where, []);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable
     */
    public function findAllOutOfContext(): iterable {
        return parent::findAll();
    }
}
