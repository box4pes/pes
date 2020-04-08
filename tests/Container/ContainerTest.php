<?php
use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerInterface;
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Exception;

use Pes\Database\Handler\Account;
use Pes\Database\Handler\ConnectionInfo;
use Pes\Database\Handler\DbTypeEnum;
use Pes\Database\Handler\DsnProvider\DsnProviderMysql;
use Pes\Database\Handler\OptionsProvider\OptionsProviderMysql;
use Pes\Database\Handler\AttributesProvider\AttributesProvider;
use Pes\Database\Handler\Handler;

use Psr\Log\NullLogger;
use Pes\Logger\FileLogger;

use Pes\Container\Container;

class ContainerDuplicateServiceKAliasFactory extends ContainerConfiguratorAbstract {
    public function getAliases() {return ['dbNick' => 'NICK'];
    }
    public function getServicesDefinitions() {return ['dbNick' => 'duplikátnínick'];
    }
    public function getFactoriesDefinitions() {return [];
    }
}

class ContainerDuplicateFactoryKAliasFactory extends ContainerConfiguratorAbstract {

    public function getAliases() {return ['dbType' => 'type', 'servisníobjekt' => function() { return new stdClass();} ];
    }
    public function getServicesDefinitions() {return [];
    }
    public function getFactoriesDefinitions() {return ['dbType' => function() { return new stdClass();} ];
    }
}

class ContainerDuplicateFactoryKServiceFactory extends ContainerConfiguratorAbstract {

    public function getAliases() {return [];
    }
    public function getServicesDefinitions() {return ['servisníobjekt' => function() { return new stdClass();} ];
    }
    public function getFactoriesDefinitions() {return ['servisníobjekt' => function() { return new stdClass();} ];
    }
}

class ContainerTestOuterConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [];
    }
    public function getFactoriesDefinitions() {
        return [
            "outerValue" => "outerString",
            "outerFactory" => function(ContainerInterface $c) {
                return new stdClass();
            }
        ];
    }
    public function getServicesDefinitions() {
        return [];
    }
}

class ContainerTestDelegateConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [];
    }
    public function getFactoriesDefinitions() {
        return [];
    }
    public function getServicesDefinitions() {
        return [
            "delegateValue" => "delegateString",
            "delegateService" => function(ContainerInterface $c) {
                return new stdClass();
            }
        ];
    }
}

class ContainerTestDefinitionsConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [];
    }
    public function getServicesDefinitions() {

        return [
            NullLogger::class => function(ContainerInterface $c) {
                return new NullLogger();
            },
            Account::class => function(ContainerInterface $c) {
                return new Account($c->get('USER'), $c->get('PASS'));
            },
            ConnectionInfo::class => function(ContainerInterface $c) {
                return new ConnectionInfo($c->get('DB_TYPE'), $c->get('DB_HOST'), $c->get('DB_NAME'), $c->get('CHARSET_UTF8'), $c->get('COLLATION_UTF8'), $c->get('DB_PORT'));
            },
            Handler::class => function(ContainerInterface $c) {
                $dsnProvider = new DsnProviderMysql();
                $optionsProvider = new OptionsProviderMysql();
                $attributesProviderDefault = new AttributesProvider($c->get(NullLogger::class));
                return new Handler($c->get(Account::class), $c->get(ConnectionInfo::class), $dsnProvider, $optionsProvider, $attributesProviderDefault, $c->get(NullLogger::class));
            }
        ];
    }
    public function getFactoriesDefinitions() {
        return [];
    }
}

class ContainerTestSettingsConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [];
    }
    public function getServicesDefinitions() {
    return [
        'DB_TYPE' => DbTypeEnum::MSSQL,
        'DB_NAME' => 'pes',
        'DB_HOST' => 'localhost',
        'DB_PORT' => '3306',
        'CHARSET_WINDOWS' => 'cp1250',
        'COLLATION_WINDOWS' => 'cp1250_czech_cs',
        'CHARSET_UTF8' => 'utf8',
        'COLLATION_UTF8' => 'utf8_czech_ci',
        'CHARSET_UTF8MB4' => 'utf8mb4',
        'COLLATION_UTF8MB4' => 'utf8mb4_czech_ci',

        'NICK' => 'tester',
        'USER' => 'pes_tester',
        'PASS' => 'pes_tester',

        'TESTOVACI_STRING' => "Cyrilekoěščřžýáíéúů",
        ];
    }
    public function getFactoriesDefinitions() {
        return [];
    }
}

/**
 * Description of StatementTest
 *
 * @author pes2704
 */
class ContainerTest extends TestCase {

    public function testDuplicateServiceDefinionException() {
        try {
            $c = (new ContainerDuplicateServiceKAliasFactory())->configure(new Container());
        } catch (Exception\ConfiguratorDuplicateServiceDefinionException $duplExc) {
            $this->assertStringStartsWith("Jméno alias, služby nebo factory lze použít pouze jednou. ", $duplExc->getMessage());
        }
        try {
            $c = (new ContainerDuplicateServiceKAliasFactory())->configure(new Container());
        } catch (Exception\ConfiguratorDuplicateServiceDefinionException $duplExc) {
            $this->assertStringStartsWith("Jméno alias, služby nebo factory lze použít pouze jednou. ", $duplExc->getMessage());
        }
        try {
            $c = (new ContainerDuplicateServiceKAliasFactory())->configure(new Container());
        } catch (Exception\ConfiguratorDuplicateServiceDefinionException $duplExc) {
            $this->assertStringStartsWith("Jméno alias, služby nebo factory lze použít pouze jednou. ", $duplExc->getMessage());
        }
    }

    /**
     * Testuje vytvoření vnořených kontejnerů a funkčnost has(), set() pro existující a neexistující služby
     */
    public function testDelegateHasAndGet() {
        $delegate = new Container();
        $outer = (new ContainerTestOuterConfigurator())->configure(new Container($delegate));
        $delegate = (new ContainerTestDelegateConfigurator())->configure($delegate);

        $this->assertTrue($delegate->has("delegateValue"));
        $this->assertTrue($delegate->has("delegateService"));
        $this->assertFalse($delegate->has("outerValue"));
        $this->assertFalse($delegate->has("outerFactory"));
        $this->assertTrue($outer->has("outerFactory"));
        $this->assertTrue($outer->has("outerValue"));
        $this->assertTrue($outer->has("delegateValue"));
        $this->assertTrue($outer->has("delegateService"));

        $this->assertEquals("delegateString", $delegate->get("delegateValue"));
        $this->assertTrue($delegate->get("delegateService") instanceof \stdClass);
        $ss = NULL;
        try {
            $ss = $delegate->get("outerFactory");  // vyhodí výjimku
            $ss = "ajtakrajta";
        } catch (Exception\NotFoundException $nfe) {
            $this->assertStringStartsWith('Volání služby kontejneru get(', $nfe->getMessage());
        }
        $this->assertNull($ss, "Nevyhozena výjimka při volání neexistující služby.");

        $this->assertEquals("outerString", $outer->get("outerValue"));
        $this->assertTrue($outer->get("outerFactory") instanceof \stdClass);
        $this->assertEquals("delegateString", $outer->get("delegateValue"));
        $this->assertTrue($outer->get("delegateService") instanceof \stdClass);

    }

    /**
     * Testuje vytvoření vytvoření hodnot při použití Closure a
     * testuje zda service vrací opakvaně identický objekt a factory pokaždé nový
     */
    public function testServiceSingletonBehavior() {
        $delegate = new Container();
        $outer = (new ContainerTestOuterConfigurator())->configure(new Container($delegate));
        $delegate = (new ContainerTestDelegateConfigurator())->configure($delegate);
        $factoryResult = $outer->get("outerFactory");
        $serviceResult = $outer->get("delegateService");
        $this->assertEquals(new stdClass(), $factoryResult);
        $this->assertEquals(new stdClass(), $serviceResult);
        $this->assertNotSame($factoryResult, $outer->get("outerFactory"));
        $this->assertSame($serviceResult, $delegate->get("delegateService"));
    }

    /**
     * Testuje příklad - vytvoření vnořených kontejnerů a vytvoření db Handler
     */
    public function testCreateDbHandler() {
        $c = (new ContainerTestDefinitionsConfigurator())->configure(
                (new ContainerTestSettingsConfigurator())->configure(new Container())
            );
        $h = $c->get(Handler::class);
        $this->assertTrue($c->get(Handler::class) instanceof Handler);
    }
}
