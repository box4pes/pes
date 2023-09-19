<?php
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\Error\Warning;

use Pes\Text\Html;

use Pes\Logger\FileLogger;

/**
 *
 * @author pes2704
 */
class HtmlRadioTest extends TestCase {

    public function testRadio() {
        $radio = Html::radio('faze', ['první fáze'=>'raz', 'Druhá fáze'=>'dva', 'Třetí a poslední'=>'tři'], ['faze'=>'dva']);
$rad = <<<SELECT
<label><input type="radio" name="faze" value="raz" />první fáze</label>
<label><input type="radio" name="faze" value="dva" checked />Druhá fáze</label>
<label><input type="radio" name="faze" value="tři" />Třetí a poslední</label>
SELECT;

        $this->assertEquals($rad, $radio);
    }

}
