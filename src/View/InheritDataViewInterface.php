<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View;

/**
 *
 * @author pes2704
 */
interface InheritDataViewInterface extends ViewInterface {

    /**
     * View, který implementuje tento interface automaticky "zdědí" data komponetního view.
     *
     * Data nastavená metodou setData() nadřazenému, kompozitnímu view jsou bezprostředně před renderováním nastavena metodou inheritData() tomuto komponentnímu view.
     *
     * @param iterable $data
     * @return ViewInterface
     */
    public function inheritData(iterable $data): ViewInterface;
}
