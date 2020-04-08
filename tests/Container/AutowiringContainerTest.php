<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use PHPUnit\Framework\TestCase;

use Psr\Container\ContainerInterface;
use Pes\Container\ContainerConfiguratorAbstract;
use Pes\Container\Exception;


class AutowiringContainerTestSettingsConfigurator extends ContainerConfiguratorAbstract {
    public function getAliases() {
        return [];
    }
    public function getServicesDefinitions() {
        return [
            'DB_TYPE' => Pes\Database\Handler\DbTypeEnum::MySQL,
            'DB_NAME' => 'pes',
            'DB_HOST' => 'localhost',
            'DB_PORT' => '3306',
//          'CHARSET' => 'cp1250',
//          'COLLATION' => 'cp1250_czech_cs',
            'CHARSET' => 'utf8',
            'COLLATION' => 'utf8_czech_ci',
//          'CHARSET' => 'utf8mb4',
//          'COLLATION' => 'utf8mb4_czech_ci',

            'NICK' => 'tester',
            'NAME' => 'pes_tester',
            'PASSWORD' => 'pes_tester',
        ];
    }
    public function getFactoriesDefinitions() {
        return [];
    }
}

class AutowiringContainerTestDefinitionsConfigurator extends ContainerConfiguratorAbstract {

    public function getAliases() {
        return [
            'dbNick' => 'NICK',
            'dbType' => 'DB_TYPE',
            'dbHost' => 'DB_HOST',
            'name' => 'NAME',
            'password' => 'PASSWORD',

            \Psr\Log\LoggerInterface::class => \Psr\Log\NullLogger::class,
            // není třeba alias - převede se automaticky jméno interface na class
//            \Pes\Database\Handler\UserInterface::class => \Pes\Database\Handler\User::class,
//            \Pes\Database\Handler\ConnectionInfoInterface::class => \Pes\Database\Handler\ConnectionInfo::class,
            \Pes\Database\Handler\DsnProvider\DsnProviderInterface::class => \Pes\Database\Handler\DsnProvider\DsnProviderMysql::class,
            \Pes\Database\Handler\OptionsProvider\OptionsProviderInterface::class => \Pes\Database\Handler\OptionsProvider\OptionsProviderMysql::class,
//            \Pes\Database\Handler\AttributesProvider\AttributesProviderInterface::class => \Pes\Database\Handler\AttributesProvider\AttributesProvider::class,

        ];
    }
    public function getServicesDefinitions() {
        return [];
    }
    public function getFactoriesDefinitions() {
        return [];
    }
}

/**
 * Description of AutowiringContainerTest
 *
 * @author pes2704
 */
class AutowiringContainerTest extends TestCase {

    public function testCreateDbHandler() {
        $awContainer = new Pes\Container\AutowiringContainer();
        $awContainer->throwExceptions(\TRUE);
        $c = (new AutowiringContainerTestDefinitionsConfigurator())->configure(
                (new AutowiringContainerTestSettingsConfigurator())->configure($awContainer)
            );
        /* @var $h Pes\Database\Handler\Handler */
        $h = $c->get(Pes\Database\Handler\Handler::class);
        $this->assertTrue($h instanceof Pes\Database\Handler\Handler);
    }

    public function testAutowireDependencyResolvingException() {
        $awContainer = new Pes\Container\AutowiringContainer();
        $awContainer->throwExceptions(\TRUE);
        $c = (new AutowiringContainerTestDefinitionsConfigurator())->configure(
                (new AutowiringContainerTestSettingsConfigurator())->configure($awContainer)
            );

        try {
            $h = $c->get(Pes\Middleware\RequestHandler::class);  // parametr callable
        } catch (Exception\AutowireDependencyResolvingException $adrlExc) {
            $this->assertStringStartsWith("Nepodařilo se vytvořit parametr, proměnnou", $adrlExc->getMessage());
        }
    }

}
