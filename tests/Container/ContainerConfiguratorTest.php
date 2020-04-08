<?php
use \PHPUnit\Framework\TestCase;

use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Psr\Container\ContainerInterface;

class ContainerConfiguratorTestOuterConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {return [];
    }
    public function getFactoriesDefinitions() {return ["factory" => "factory"];
    }
    public function getServicesDefinitions() {return [];
    }
}

class ContainerConfiguratorTestDelegateConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {return [];
    }
    public function getFactoriesDefinitions() {return [];
    }
    public function getServicesDefinitions() {return ["service" => "service"];
    }
}

/**
 * Description of ContainerFactoryTest
 *
 * @author pes2704
 */
class ContainerConfiguratorTest extends TestCase {
    public function testCreate() {
        $delegate = new Container();
        $outer = new Container($delegate);
        $outer = (new ContainerConfiguratorTestOuterConfigurator())->configure($outer);
        $delegate = (new ContainerConfiguratorTestDelegateConfigurator())->configure($delegate);
        $this->assertTrue($delegate instanceof ContainerInterface);
        $this->assertTrue($outer instanceof ContainerInterface);
        $this->assertEquals("factory", $outer->get("factory"));
        $this->assertEquals("service", $delegate->get("service"));
        $this->assertEquals("service", $outer->get("service"));

    }
}
