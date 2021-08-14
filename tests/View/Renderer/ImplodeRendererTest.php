<?php
use PHPUnit\Framework\TestCase;

use Pes\View\Template\TemplateInterface;

use Pes\View\Renderer\ImplodeRenderer;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\Template\InterpolateTemplate;
use Pes\View\Renderer\Exception\UnsupportedTemplateException;

class UnsuportedTemplate implements TemplateInterface {
    public function getDefaultRendererService() {
        return 'bflmpsvz';
    }
}

/**
 * Test Pes\Type\Enum
 *
 * @author pes2704
 */
class ImplodeRendererTest extends TestCase {

    /**
     * data provider pro testGetTypeValue v bázové třídě testu
     * @return type
     */
//    public function valuesProvider() {
//        $type = new TestTypeEnum();
//        foreach ($type->getConstList() as $value) {
//            $data[] = array($type, $value);
//        }
//        return $data;
//    }

###############################

    /**
     * vyhození výjimky pro hodnotu, která není povoleného typu
     */
    public function testConstructorTypeError() {
        $this->expectException(TypeError::class);
        $renderer = new ImplodeRenderer();
        $renderer->setTemplate(new \stdClass());
    }

    /**
     * vyhození výjimky pro hodnotu, která není povoleného typu
     */
    public function testConstructorUnsupportedTemplateException() {
        $this->expectException(UnsupportedTemplateException::class);
        $renderer = new ImplodeRenderer();
        $renderer->setTemplate(new UnsuportedTemplate());
    }

    public function testRender() {
        $renderer = new ImplodeRenderer();
        $renderer->setTemplate(new ImplodeTemplate());
        $s = ImplodeTemplate::SEPARATOR;
        $str = $renderer->render(['a', 'b', 'c']);
        $this->assertEquals("a{$s}b{$s}c", $str);
    }

    public function testRenderRecursive() {
        $renderer = new ImplodeRenderer();
        $renderer->setTemplate(new ImplodeTemplate());
        $s = ImplodeTemplate::SEPARATOR;
        $str = $renderer->render(['a', 'b', ['a', 'b', 'c'], 'c']);
        $this->assertEquals("a{$s}b{$s}a{$s}b{$s}c{$s}c", $str);
    }
}
