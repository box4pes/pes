<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Recorder;

use Pes\View\Recorder\RecorderProviderInterface;
use Psr\Log\LoggerInterface;


/**
 * Description of RecordsLogger
 *
 * @author pes2704
 */
class RecordsLogger {

    const NAMES_SEPARATOR = ' | ';

    /**
     * @var LoggerInterface Description
     */
    private $logger;

    private $loggingTime;


    /**
     * Konstruktor. Přijímá logger pro logování záznamů pořízených rekordérem užití proměnných v template.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Zaznamená do logu informace o užití proměnných v průběhu renderování.
     * Informace zapíše do logu pomocí loggeru předaného jako parametr konstruktoru.
     *
     */
    public function logRecords(RecorderProviderInterface $recorderProvider) {
        $this->loggingTime = date("Y-m-d H:i:s");
        foreach ($recorderProvider->getRecorders() as $recorder) {
            $this->log($recorder);
        }
    }

    private function log(VariablesUsageRecorderInterface $recorder) {
        $index = $recorder->getIndex();

        if ( !$recorder->getContextVars()) {
            $contextMessage = "Data kontextu jsou prázdná.";
        } else {
            $contextMessage = "Data kontextu mají ".count($recorder->getContextVars())." položek.";
        }

        $this->logger->debug(" [{time}] RecordsLogger: Renderování template {index}: {info} {empty}",  ['time'=>$this->loggingTime, 'index'=>$index, 'info'=>$recorder->getRecordInfo(), 'empty' => $contextMessage]);

        if ($recorder->getContextVars()) {
                    $this->logger->info("RecordsLogger: Template {index}. Seznam proměnných kontextu: {contextVars}", ['index'=>$index, 'contextVars'=> $this->listVars($recorder, $recorder->getContextVars())]);
        }
        if ($recorder->getUndefinedVarsErors()) {
            foreach ($recorder->getUndefinedVarsErors() as $info) {
                $this->logger->warning("RecordsLogger: Template {index} - nedefinovaná proměnná. Chyba: {errstr} na řádku {line}.", ['index'=>$index, 'errstr'=>$info['errstr'], 'line'=>$info['line']]);
            }
        }
        if ($recorder->getUnusedVars()) {
            $this->logger->notice("RecordsLogger: Template {index}. Seznam nepoužitých proměnných: {unusedVars}", ['index'=>$index, 'unusedVars'=> $this->listVars($recorder, $recorder->getUnusedVars())]);
        }
    }

    private function listVars(VariablesUsageRecorderInterface $recorder, $vars) {
        if ($recorder instanceof VariablesUsageRecorder) {
            switch ($recorder->getRecordLevel()) {
                case VariablesUsageRecorder::RECORD_LEVEL_KEYS:
                    return implode(self::NAMES_SEPARATOR, $vars);
                case VariablesUsageRecorder::RECORD_LEVEL_INFO:
                    return implode(self::NAMES_SEPARATOR, $vars);
                case VariablesUsageRecorder::RECORD_LEVEL_FULL:
                    return PHP_EOL."Proměnná ".implode(PHP_EOL."Proměnná ", $vars);
            }
        } else {
            return implode(self::NAMES_SEPARATOR, $vars);
        }
    }
}
