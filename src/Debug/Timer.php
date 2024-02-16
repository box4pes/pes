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

    private $verbose;
    private $startTime;
    private $lasttime;
    
    /**
     * Nastaví počáteční čas časového intervalu.
     * 
     * @return float Čas startu v sekundách, čas je UNIX čas.
     */
    public function start() {
        $this->startTime = $this->lasttime = microtime(TRUE);
        return $this->startTime;
    }

    /**
     * Vrací čas uplynulý od posledního volání této metody.
     *
     * @return float Čas v sekundách
     */
    public function interval() {
        $lt = $this->lasttime;
        $this->lasttime = microtime(TRUE);
        return $this->lasttime - $lt;
    }

    /**
     * 
     * @return float Čas v sekundách
     */
    public function runtime() {
        return microtime(TRUE) - $this->startTime;
    }

}
