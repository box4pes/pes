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
class HtmlCheckboxTest extends TestCase {

    public function testCheckbox() {
        $checkbox = Html::checkbox(
                                [
                                    "Piju kafe"=>["kafe"=>"Piji kávu aspoň občas"],
                                    "Piju čaj"=>["caj"=>"Piji čajíček"]
                                ],
                                ["kafe"=>"Piji kávu aspoň občas"]
                            );
$che = <<<SELECT
<label><input type="checkbox" name="kafe" value="Piji kávu aspoň občas" checked />Piju kafe</label>
<label><input type="checkbox" name="caj" value="Piji čajíček" />Piju čaj</label>
SELECT;

        $this->assertEquals($che, $checkbox);
    }
}
