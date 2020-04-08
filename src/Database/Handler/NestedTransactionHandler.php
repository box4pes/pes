<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Handler;

/**
 * Description of NestedTransactionHandler
 *
 * @author pes2704
 */
class NestedTransactionHandler extends Handler {

    private $savepointCounter = 0;

//    http://php.net/manual/en/pdo.begintransaction.php
//         steve at fancyguy dot com  (a oprava kesler dot alwin at gmail dot com)
//    The nested transaction example here is great, but it's missing a key piece of the puzzle.  Commits will commit everything, I only wanted commits to actually commit when the outermost commit has been completed.  This can be done in InnoDB with savepoints.

    /**
     * {@inheritDoc}
     *
     * První volání metody zahájí transakci voláním metodx PDO::beginTransaction().
     * Všechna další volání jen vytvoří nový bod návratu - provedou sql příkaz SAVEPOINT a jako aktuální bod návratu v handleru nastaví tento bod.
     */
    public function beginTransaction()
    {
        if (!$this->savepointCounter++) {
            $success = parent::beginTransaction();
            $message = $this->getInstanceInfo().' beginTransaction(). Proveden start transakce PDO::beginTransaction().';
            if (!$success) {
                $message .= '. Metoda PDO::beginTransaction() selhala.';
            }
            $this->logger->debug($message);
            return $success;
        } else {
            $sql = 'SAVEPOINT point'.$this->savepointCounter;
            $this->exec($sql);
            $message = $this->getInstanceInfo().' exec({sql})';
            $this->logger->debug($message, array('sql'=>$sql));
            return $this->savepointCounter >= 0;
        }
    }

    /**
     * {@inheritDoc}
     *
     * Pokud je nastav bod návratu (SAVEPOINT), metoda jen nastaví jako bod návratu bod předchátející aktuálnímu bodu návratu.
     * Pokud již není žádný bod návratu, metoda provede skutečný příkaz PDO::commit().
     */
    public function commit()
    {
        if (!--$this->savepointCounter) {
            $success = parent::commit();
            $message = $this->getInstanceInfo().' commit(). Proveden commit transakce PDO::commit().';
            if (!$success) {
                $message .= '. Metoda PDO::commit() selhala.';
            }
            $this->logger->debug($message);
            return $success;
        }
        $message = $this->getInstanceInfo().' nastaven {point} jako aktuální bod návratu.';
        $this->logger->debug($message, array('point'=>$this->savepointCounter));
        return $this->savepointCounter >= 0;
    }

    /**
     * {@inheritDoc}
     *
     * Pokud je nastaven bod návratu, metoda jen vrátí transakci do aktuálního bodu návratu voláním sql příkazu ROLLBACK TO a nastaví jako bod návratu bod předchátející aktuálnímu bodu návratu.
     * Pokud není bod návratu, metoda provede rollback celé transakce - volá PDO::rollback().
     */
    public function rollback()
    {
        if (--$this->savepointCounter) {   // >0 ??
            $this->exec('ROLLBACK TO point'.($this->savepointCounter + 1));
            $message = $this->getInstanceInfo().' nastaven {point} jako aktuální bod návratu.';
            $this->logger->debug($message, array('point'=>$this->savepointCounter));
            return true;
        }
        $success = parent::rollback();
        $message = $this->getInstanceInfo().' rollback(). Proveden rollback transakce PDP::rollback().';
        if (!$success) {
            $message .= '. Metoda PDO::rollback() selhala.';
        }
        $this->logger->debug($message);
        return $success;
    }

    public function __destruct() {
        if ($this->savepointCounter) {
            $message = $this->getInstanceInfo().' Transakce neproběhla celá. Metoda beginTransaction() byla volána vícekrát než metody commit() a rollback(). Aktuální bod návratu při ukončení handleru je {point}';
            $this->logger->warning($message, array('point'=>$this->savepointCounter));
        }
    }
}
