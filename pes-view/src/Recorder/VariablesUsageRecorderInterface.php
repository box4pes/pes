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
 *
 * @author pes2704
 */
interface VariablesUsageRecorderInterface {
    public function getRecordLevel();
    public function getIndex();
    public function getRecordInfo();
    public function setRecordInfo($info):self;
    public function getUndefinedVarsErors();
    public function addUndefinedVarError($errstr, $file, $line):self;
    public function getUnusedVars();
    public function setUnusedVars(array $unusedVars):self;
    public function addUnusedVar($key, $value):self;
    public function getContextVars();
    public function setContextVars(array $contextVars): self;
    public function addContextVar($key, $value):self;
}
