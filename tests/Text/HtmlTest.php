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
class HtmlTest extends TestCase {

    /**
     *
     * @var Pes\Logger\FileLogger
     */
    private static $logger;


    public static function setUpBeforeClass(): void {

    }

    public function testSelect() {
        $select = Html::select("jmeno", "To je label:", ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"], [], []);

        $select = Html::select("jmeno", "To je label:", [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], [], []);
        $select = Html::select("jmeno", "To je label:", [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["jmeno"=>"Plzeň-sever"], ["data-testovaci"=>"testovaci_atribut"]);

        $select = Html::select("jmeno", "To je label:", [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["jmeno"=>"nesmysl"], ["data-testovaci"=>"testovaci_atribut"]);

    }


}
