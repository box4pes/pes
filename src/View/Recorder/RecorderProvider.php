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

use Pes\View\Recorder\VariablesUsageRecorder;
use Pes\View\Recorder\VariablesUsageRecorderInterface;

/**
 * Description of RecorderProvider
 * Pro logování musí každý nový Renderer dostat jako parametr konstruktoru RecorderProvider.
 * RecorderProvider poskytuje Rekordery pro záznam užití proměnných při renderování a každý poskytnutý Recorder zaregistruje.
 * Po renderování jsou zpětně všechny poskytnuté Recordery dostupné.
 * 
 * @author pes2704
 */
class RecorderProvider implements RecorderProviderInterface {


    private $recorders;
    private $recordersRrecordLevel;

    public function __construct($recordersRrecordLevel = VariablesUsageRecorder::RECORD_LEVEL_KEYS) {
        $this->recorders = new \SplObjectStorage();
        $this->recordersRrecordLevel = $recordersRrecordLevel;
    }

    /**
     * Poskytne rekorder pro záznam užití proměnných v rendereru. Současně poskytnutý rekorder zaregistruje. Zaregistrované rekordery
     * je po renderování možno získat metodou getRecorders().
     *
     * @param type $index
     * @param VariablesUsageRecorderInterface $parentRecorder
     * @return VariablesUsageRecorderInterface
     */
    public function provideRecorder($index, VariablesUsageRecorderInterface $parentRecorder = NULL):VariablesUsageRecorderInterface {
        $recorder = new VariablesUsageRecorder($index, $this->recordersRrecordLevel);
        if ($parentRecorder AND $this->recorders->offsetExists($parentRecorder)) {
            $this->recorders->attach($recorder, $parentRecorder);
        } else {
            $this->recorders->attach($recorder);
        }
        return $recorder;
    }

    /**
     * Vrátí poskytnuté a zaregistrované renderery. Po renderování tyto renderery obsahují záznamy vložené do nich rendererem.
     * @return VariablesUsageRecorderInterface array of
     */
    public function getRecorders() {
        return $this->recorders;
    }
}
