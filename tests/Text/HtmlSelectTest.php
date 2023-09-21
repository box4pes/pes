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
class HtmlSelectTest extends TestCase {

    /**
     *
     * @var Pes\Logger\FileLogger
     */
    private static $logger;


    public static function setUpBeforeClass(): void {

    }

    private function removeUniquids($str) {
        $repl = $this->replaceInnerText($str, 'for="', '"');
        return $this->replaceInnerText($repl, 'id="', '"');
    }

    private function replaceInnerText($str, $start, $end, $replacement='') {
        return preg_replace('/' . preg_quote($start) .
                          '.*?' .
                          preg_quote($end) . '/', $start.$replacement.$end, $str);
    }

#### empty #################################################

    public function testSelectEmptyArrays() {
            $select = Html::select("jmeno", "To je label:");
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select id="" name="jmeno"></select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    
#### numeric ###############################################    

    public function testSelectNumericArray() {
        $select = Html::select("jmeno", "To je label:", [], ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select id="" name="jmeno">
<option value=""></option>
<option value="Plzeň-město">Plzeň-město</option>
<option value="Plzeň-jih">Plzeň-jih</option>
<option value="Plzeň-sever">Plzeň-sever</option>
<option value="Klatovy">Klatovy</option>
<option value="Cheb">Cheb</option>
<option value="jiné">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    public function testSelectNumericArraySelected() {
        $select = Html::select("jmeno", "To je label:", ["jmeno"=>"Plzeň-sever"], ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select id="" name="jmeno">
<option value=""></option>
<option value="Plzeň-město">Plzeň-město</option>
<option value="Plzeň-jih">Plzeň-jih</option>
<option value="Plzeň-sever" selected>Plzeň-sever</option>
<option value="Klatovy">Klatovy</option>
<option value="Cheb">Cheb</option>
<option value="jiné">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }

    public function testSelectNumericRequiredSelected() {
        $select = Html::select("jmeno", "To je label:", ["jmeno"=>"Plzeň-sever"], ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"], ["required"=>true]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select required id="" name="jmeno">
<option value=""></option>
<option value="Plzeň-město">Plzeň-město</option>
<option value="Plzeň-jih">Plzeň-jih</option>
<option value="Plzeň-sever" selected>Plzeň-sever</option>
<option value="Klatovy">Klatovy</option>
<option value="Cheb">Cheb</option>
<option value="jiné">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    
    public function testSelectNumericRequiredPlaceholder() {
        $select = Html::select("jmeno", "To je label:", [], ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"], ["required"=>true], "");
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select required id="" name="jmeno">
<option value="" disabled selected></option>
<option value="Plzeň-město">Plzeň-město</option>
<option value="Plzeň-jih">Plzeň-jih</option>
<option value="Plzeň-sever">Plzeň-sever</option>
<option value="Klatovy">Klatovy</option>
<option value="Cheb">Cheb</option>
<option value="jiné">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }    
    
    public function testSelectNumericRequiredPlaceholderSelected() {
        $select = Html::select("jmeno", "To je label:", ["jmeno"=>"Plzeň-sever"], ["Placeholder", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"], ["required"=>true], "Placeholder");
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select required id="" name="jmeno">
<option value="Placeholder" disabled>Placeholder</option>
<option value="Plzeň-město">Plzeň-město</option>
<option value="Plzeň-jih">Plzeň-jih</option>
<option value="Plzeň-sever" selected>Plzeň-sever</option>
<option value="Klatovy">Klatovy</option>
<option value="Cheb">Cheb</option>
<option value="jiné">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    
#### assotiative ###############################################    

    public function testSelectAssocArray() {
        $select = Html::select("jmeno", "To je label:", [], [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select id="" name="jmeno">
<option value="1"></option>
<option value="2">Plzeň-město</option>
<option value="3">Plzeň-jih</option>
<option value="4">Plzeň-sever</option>
<option value="5">Klatovy</option>
<option value="6">Cheb</option>
<option value="7">jiné</option>
</select></span>
SELECT;
        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    public function testSelectAssocArraySelected() {
        $select = Html::select("jmeno", "To je label:", 
                ["jmeno"=>4], 
                [1=>"----", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select id="" name="jmeno">
<option value="1">----</option>
<option value="2">Plzeň-město</option>
<option value="3">Plzeň-jih</option>
<option value="4" selected>Plzeň-sever</option>
<option value="5">Klatovy</option>
<option value="6">Cheb</option>
<option value="7">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }

    public function testSelectAssocRequiredSelected() {
        $select = Html::select("jmeno", "To je label:", 
                ["jmeno"=>4], 
                [""=>"Vyberte hodnotu", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], 
                ["required"=>true]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select required id="" name="jmeno">
<option value="">Vyberte hodnotu</option>
<option value="2">Plzeň-město</option>
<option value="3">Plzeň-jih</option>
<option value="4" selected>Plzeň-sever</option>
<option value="5">Klatovy</option>
<option value="6">Cheb</option>
<option value="7">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    
    public function testSelectAssocRequiredPlaceholder() {
        $select = Html::select("jmeno", "To je label:", 
                [], 
                ["placeholder_key"=>"Vyberte hodnotu", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], 
                ["required"=>true], 
                "placeholder_key");
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select required id="" name="jmeno">
<option value="placeholder_key" disabled selected>Vyberte hodnotu</option>
<option value="2">Plzeň-město</option>
<option value="3">Plzeň-jih</option>
<option value="4">Plzeň-sever</option>
<option value="5">Klatovy</option>
<option value="6">Cheb</option>
<option value="7">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    
    public function testSelectAssocRequiredPlaceholderSelected() {
        $select = Html::select("jmeno", "To je label:", 
                ["jmeno"=>4], 
                [""=>"Vyberte hodnotu", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], 
                ["required"=>true], 
                "");
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select required id="" name="jmeno">
<option value="" disabled>Vyberte hodnotu</option>
<option value="2">Plzeň-město</option>
<option value="3">Plzeň-jih</option>
<option value="4" selected>Plzeň-sever</option>
<option value="5">Klatovy</option>
<option value="6">Cheb</option>
<option value="7">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    
    public function testSelectAssocitiveArrayWithNumericKeys() {
        $select = Html::select("jmeno", "To je label:", [], [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"]);
$sel = <<<SELECT
<label for="">To je label:</label>
<span><select id="" name="jmeno">
<option value="1"></option>
<option value="2">Plzeň-město</option>
<option value="3">Plzeň-jih</option>
<option value="4">Plzeň-sever</option>
<option value="5">Klatovy</option>
<option value="6">Cheb</option>
<option value="7">jiné</option>
</select></span>
SELECT;

        $repl = $this->removeUniquids($select);
        $this->assertEquals($sel, $repl);
    }
    public function testSelect2() {

    //neasociativní (číselné) pole
//        $select[] = Html::select("jmeno", "To je label:", ["", "Plzeň-město", "Plzeň-jih", "Plzeň-sever", "Klatovy", "Cheb", "jiné"], ["jmeno"=>"Plzeň-sever"], []);

        // asociatovní pole
        $select[] = Html::select("jmeno", "To je label:", [], [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], []);
        $select[] = Html::select("jmeno", "To je label:", ["jmeno"=>"4"], [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["data-testovaci"=>"testovaci_atribut", "required"=>true]);
        $select[] = Html::select("jmeno", "To je label:", ["jmeno"=>4], [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["data-testovaci"=>"testovaci_atribut"]);

        $select[] = Html::select("jmeno", "To je label:", ["jmeno"=>"nesmysl"], [1=>"", 2=>"Plzeň-město", 3=>"Plzeň-jih", 4=>"Plzeň-sever", 5=>"Klatovy", 6=>"Cheb", 7=>"jiné"], ["data-testovaci"=>"testovaci_atribut"]);
        $html = implode(PHP_EOL, $select);
        $this->assertIsArray($select);

    }
}
