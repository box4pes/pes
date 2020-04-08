<?php

namespace Pes\Autoloader;

use PHPUnit\Framework\TestCase;

class MockAutoloader extends Autoloader
{
    protected $files = array();

    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    protected function requireFile($file)
    {
        return in_array($file, $this->files);
    }
}

class AutoloaderTest extends TestCase {
    protected $loader;

    public function setUp(): void {
        $this->loader = new MockAutoloader;

        $this->loader->setFiles(array(
            '/vendor/foo.bar/src/ClassName.php',
            '/vendor/foo.bar/src/DoomClassName.php',
            '/vendor/foo.bar/tests/ClassNameTest.php',
            '/vendor/foo.bardoom/src/ClassName.php',
            '/vendor/foo.bar.baz.dib/src/ClassName.php',
            '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php',
            '/Pes/Pes/src/Database/Handler/OptionsProvider/OptionsProvider.php',
            '/Pes/Pes/src/Database/Handler/OptionsProvider/OptionsProviderInterface.php',
            '/p2_1_4_nasazeny_20170317/Projektor2/Model/Db/Kancelar.php'

        ));

        $this->loader->addNamespace(
            'Foo\Bar',
            '/vendor/foo.bar/src'
        );

        $this->loader->addNamespace(
            'Foo\Bar',
            '/vendor/foo.bar/tests'
        );

        $this->loader->addNamespace(
            'Foo\BarDoom',
            '/vendor/foo.bardoom/src'
        );

        $this->loader->addNamespace(
            'Foo\Bar\Baz\Dib',
            '/vendor/foo.bar.baz.dib/src'
        );

        $this->loader->addNamespace(
            'Foo\Bar\Baz\Dib\Zim\Gir',
            '/vendor/foo.bar.baz.dib.zim.gir/src'
        );

        $this->loader->addNamespace(
            'Pes\Database\Handler\OptionsProvider',
            '\Pes\Pes\src\Database\Handler\OptionsProvider'
        );

        $this->loader->addNamespace(
            'Pes\Database\Handler\OptionsProvider',
            '/Pes/Pes/src/Database/Handler/OptionsProvider'
        );

        $this->loader->addPrefix(
                'Pes',
                '/Pes/Pes/src'
                );

//        $this->loader->addPrefix(
//                'Pes_Database_Handler_OptionsProvider',
//                '/Pes/Pes/src/Database/Handler/OptionsProvider'
//                );
//        $this->loader->addPrefix(
//                'Pes_Database_Handler_OptionsProvider',
//                '/Pes/Pes/src/Database/Handler/OptionsProvider'
//                );
        $this->loader->addPrefix(
                'Projektor2',
                '/p2_1_4_nasazeny_20170317/Projektor2'
                );
    }


    public function testExistingFile()
    {
        // loadClass - vpřípadě úspěchu vrací cestu k souboru s obyčejnými lomítky (ne obrácenými)
        // pozor při kopírování 'expect' řetězce
        $actual = $this->loader->loadClass('Foo\Bar\ClassName');
        $expect = '/vendor/foo.bar/src/ClassName.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->loadClass('Foo\Bar\ClassNameTest');
        $expect = '/vendor/foo.bar/tests/ClassNameTest.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->loadClass('Pes\Database\Handler\OptionsProvider\OptionsProviderInterface');
        $expect = '/Pes/Pes/src/Database/Handler/OptionsProvider/OptionsProviderInterface.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->loadClass('Pes_Database_Handler_OptionsProvider_OptionsProviderInterface');
        $expect = '/Pes/Pes/src/Database/Handler/OptionsProvider/OptionsProviderInterface.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->loadClass('Projektor2_Model_Db_Kancelar');
        $expect = '/p2_1_4_nasazeny_20170317/Projektor2/Model/Db/Kancelar.php';
        $this->assertSame($expect, $actual);
    }

    public function testMissingFile()
    {
        $actual = $this->loader->loadClass('No_Vendor\No_Package\NoClass');
        $this->assertFalse($actual);
    }

    public function testDeepFile()
    {
        $actual = $this->loader->loadClass('Foo\Bar\Baz\Dib\Zim\Gir\ClassName');
        $expect = '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';
        $this->assertSame($expect, $actual);
    }

    public function testConfusion()
    {
        $actual = $this->loader->loadClass('Foo\Bar\DoomClassName');
        $expect = '/vendor/foo.bar/src/DoomClassName.php';
        $this->assertSame($expect, $actual);

        $actual = $this->loader->loadClass('Foo\BarDoom\ClassName');
        $expect = '/vendor/foo.bardoom/src/ClassName.php';
        $this->assertSame($expect, $actual);
    }
}