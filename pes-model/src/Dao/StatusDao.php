<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Dao;

use Pes\Session\SessionStatusHandlerInterface;
use Pes\Model\Exception\SessionFinishedException;

/**
 * Description of StatusDao
 *
 *
 *
 * @author pes2704
 */
class StatusDao {

    private $sessionHandler;

    /** @var bool true po úspěšném finish() v tomto requestu */
    private bool $finished = false;

    public function __construct(SessionStatusHandlerInterface $sessionHandler) {
        $this->sessionHandler = $sessionHandler;
    }

    public function isFinished(): bool {
        return $this->finished || session_status() !== PHP_SESSION_ACTIVE;
    }

    private function assertWritable(): void {
        if ($this->isFinished()) {
            throw new SessionFinishedException(
                'Session was finished (StatusDao::finish()); status data cannot be read or written.'
            );
        }
    }

    public function get($fragmentName) {
        $this->assertWritable();
        return $this->sessionHandler->getFragmentArrayReference($fragmentName);
    }

    public function set($fragmentName, $row) {
        $this->assertWritable();
        $this->sessionHandler->set($fragmentName, $row);
    }

    public function delete($fragmentName) {
        $this->assertWritable();
        $this->sessionHandler->delete($fragmentName);
    }

    public function finish() {
        $this->assertWritable();
        $this->sessionHandler->sessionFinish();
        $this->finished = true;
    }

    public function reset() {
        $this->sessionHandler->sessionReset();
        $this->finished = false;
    }
}
