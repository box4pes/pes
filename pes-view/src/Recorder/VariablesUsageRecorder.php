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

/**
 * Description of VariablesUsageRecorder
 *
 * Recorder provádí záznam užití proměnných při rendrování v Rendereru. Záznam provádí co nejstručněji a nejrychleji. Teprve na konci
 * skriptu jsou všechny Recordery přečteny a z jejich záznamů je pořízen log.
 *
 * @author pes2704
 */
class VariablesUsageRecorder implements VariablesUsageRecorderInterface {

    const RECORD_LEVEL_KEYS = 'KEYS';
    const RECORD_LEVEL_INFO = 'INFO';
    const RECORD_LEVEL_FULL = 'FULL';

    private $recordLevel;
    private $index;
    private $info;
    private $undefinedVars = [];
    private $unusedVars = [];
    private $contextVars = [];

    public function __construct($index, $recordLevel = self::RECORD_LEVEL_KEYS) {
        $this->index = $index;
        $this->recordLevel = $recordLevel;
    }

    public function getRecordLevel() {
        return $this->recordLevel;
    }

    public function getIndex() {
        return $this->index;
    }

    public function getRecordInfo() {
        return $this->info;
    }

    public function setRecordInfo($info):VariablesUsageRecorderInterface {
        $this->info = $info;
        return $this;
    }

    public function addUndefinedVarError($errstr, $file='?', $line='?'):VariablesUsageRecorderInterface {
        $this->undefinedVars[] = ['errstr'=>$errstr, 'file'=>$file, 'line'=>$line];
        return $this;
    }

    public function getUndefinedVarsErors() {
        return $this->undefinedVars;
    }

    public function getUnusedVars() {
        return $this->unusedVars;
    }

    public function getContextVars() {
        return $this->contextVars;
    }

    public function setUnusedVars(array $unusedVars):VariablesUsageRecorderInterface {
        if ($unusedVars) {
            $this->unusedVars = array_merge($unusedVars, $this->unusedVars);
        }
        return $this;
    }

    public function addUnusedVar($key, $value=NULL):VariablesUsageRecorderInterface {
        $this->unusedVars[] = $key;
        return $this;
    }

    public function setContextVars(array $contextVars):VariablesUsageRecorderInterface {
        if ($contextVars) {
            switch ($this->recordLevel) {
                case self::RECORD_LEVEL_KEYS:
                    $this->contextVars = array_merge(array_keys($contextVars), $this->contextVars);
                    break;
                default:
                    foreach ($contextVars as $key => $value) {
                        $this->addContextVar($key, $value);
                    }
                    break;
            }
        }
        return $this;
    }

    public function addContextVar($key, $value=NULL):VariablesUsageRecorderInterface {
        $this->contextVars[$key] = $key." {$this->renderValue($value)}";
        return $this;
    }

    private function renderValue($var) {
        $vartype = gettype($var);
        switch ($this->recordLevel) {
            case self::RECORD_LEVEL_KEYS:
                return $this->renderValueAsEmptyString($var);
            case self::RECORD_LEVEL_INFO:
                return $this->renderValueAsInfo($var);
            case self::RECORD_LEVEL_FULL:
                return $this->renderValueFull($var);
        }
    }

    private function renderValueAsEmptyString($var) {
        return '';
    }

    private function renderValueAsInfo($var) {
        $vartype = gettype($var);
        switch ($vartype) {
            case "boolean":
                $rendered = $vartype." ".($var ? "TRUE" : "FALSE");
                break;
            case "integer":
            case "double":    // (for historical reasons "double" is returned in case of a float, and not simply
            case "float":
                $rendered = $vartype." ".$var;
                break;
            case "string":
                $rendered = $vartype." ". strlen($var)." bytes";
                break;
            case "array":
                $rendered = $vartype." ".count($var)." elements";
                break;
            case "object":
            case "resource":
                $rendered = $vartype." ". get_class($var);
                break;
            case "NULL":
            case "unknown type":
                $rendered = $vartype;
                break;

        }

        return $rendered;
    }

    private function renderValueFull($var) {
        $vartype = gettype($var);
        switch ($vartype) {
            case "boolean":
                $rendered = $vartype." ".($var ? "TRUE" : "FALSE");
                break;
            case "integer":
            case "double":    // (for historical reasons "double" is returned in case of a float, and not simply
            case "float":
                $rendered = $vartype." ".$var;
                break;
            case "string":
                $rendered = $vartype." ". strlen($var)." bytes" . (strlen($var) ? ": \"".$var."\"" : "");
                break;
            case "array":
                $rendered = $vartype." ".count($var)." elements";
                break;
            case "object":
            case "resource":
                $rendered = $vartype." ". get_class($var);
                break;
            case "NULL":
            case "unknown type":
                $rendered = $vartype;
                break;

        }

        return $rendered;
    }
 }
