<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Debug;

/**
 * Description of Timer
 *
 * @author pes2704
 */
class Timer {

    private $startTime;
    private $lasttime;

    public function start() {
        $this->startTime = $this->lasttime = microtime(TRUE);
        return 'Reset timer'.PHP_EOL;
    }

    /**
     * Vrací textovou informaci o čase uplynulém od posledního volání této metody.
     *
     * @return string
     */
    public function interval() {
        $lt = $this->lasttime;
        $this->lasttime = microtime(TRUE);
        return 'Interval: '. ($this->lasttime - $lt) .' sec'.PHP_EOL;
    }

    public function runtime() {
        return 'Runtime: '. (microtime(TRUE) - $this->startTime).' sec'.PHP_EOL;

    }

}
