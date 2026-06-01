<?php
use \PHPUnit\Framework\TestCase;

use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Container;
use Pes\Container\Exception\LockedContainerException;

use Psr\Container\ContainerInterface;

class ContainerConfiguratorTestOuterConfigurator extends ContainerConfiguratorAbstract {
    public function getParams(): iterable {
        return [];
    }
    public function getAliases(): iterable {return [];
    }
    public function getFactoriesDefinitions(): iterable {return ["factory" => "factory"];
    }
    public function getServicesDefinitions(): iterable {return [];
    }
    public function getServicesOverrideDefinitions(): iterable {return [];
    }
}

class ContainerConfiguratorTestDelegateConfigurator extends ContainerConfiguratorAbstract {
    public function getParams(): iterable {
        return [];
    }
    public function getAliases(): iterable {return [];
    }
    public function getFactoriesDefinitions(): iterable {return [];
    }
    public function getServicesDefinitions(): iterable {return ["service" => "service"];
    }
    public function getServicesOverrideDefinitions(): iterable {return [];
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
        $delegate = (new ContainerConfiguratorTestDelegateConfigurator())->configure($delegate);
        $outer = new Container($delegate);
        $outer = (new ContainerConfiguratorTestOuterConfigurator())->configure($outer);
        $this->assertTrue($delegate instanceof ContainerInterface);
        $this->assertTrue($outer instanceof ContainerInterface);
        $this->assertEquals("factory", $outer->get("factory"));
        $this->assertEquals("service", $delegate->get("service"));
        $this->assertEquals("service", $outer->get("service"));

    }

    /**
     * @expectedException Pes\Container\Exception\LockedContainerException
     */
    public function testLockedContainerException() {
        $delegate = new Container();
        $outer = new Container($delegate);
        $outer = (new ContainerConfiguratorTestOuterConfigurator())->configure($outer);
        $outer->get("factory");
        $delegate = (new ContainerConfiguratorTestDelegateConfigurator())->configure($delegate);
        $delegate->get("service");
        $outer->set("new_service", "new_service");

    }
}
