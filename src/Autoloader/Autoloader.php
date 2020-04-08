<?php
namespace Pes\Autoloader;

/**
 * An example of a general-purpose implementation that includes the optional
 * functionality of allowing multiple base directories for a single namespace
 * prefix.
 *
 * EXAMPLE 1:
 *     /path/to/packages/foo-bar/
 *         src/
 *             Baz.php             # Foo\Bar\Baz
 *             Qux/
 *                 Quux.php        # Foo\Bar\Qux\Quux
 *         tests/
 *             BazTest.php         # Foo\Bar\BazTest
 *             Qux/
 *                 QuuxTest.php    # Foo\Bar\Qux\QuuxTest
 *
 * ...
 *
 *      $loader = new \Example\Psr4AutoloaderClass;
 *      $loader->register();
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
 *
 *
 *
 * EXAMPLE 2 - kombinace s Composer autoloaderem:
 * use Pes\Autoloader\Autoloader;
 * $pesAutoloader = new Autoloader();
 * $pesAutoloader->register();
 * $pesAutoloader->addNamespace('Pes', 'vendor/Pes/src/'); //autoload pro namespace Pes
 * $pesAutoloader->addNamespace('Menu', 'Menu/'); //autoload pro namespace
 * $pesAutoloader->addNamespace('Konverze', 'Konverze/');
 * $pesAutoloader->addNamespace('Database', 'Database/');
 * $pesAutoloader->addNamespace('Helper', 'Helper/');
 * // přidání autoloaderu vytvořeného Composerem:
 * include 'vendor/autoload.php';
 *
 */
class Autoloader
{

    //nefunguje správně DIRECTORY_SEPARATOR (PHP platform konstant) - na OS Windows mám linuxové PHP (případ XAMPP) a ds je '/'
    const DIRECTORY_SEPARATOR = '/';  // i pro Windows vždy '/'

    const FALLBACK_PREFIX = 'FALLBACK';  // string bez podtržítek

    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Register loader with SPL autoloader stack.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     * namespace.
     * @param bool $prepend If true, prepend the base directory to the stack
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // normalize namespace prefix
        if ($prefix) {    //k prázdnému řetězci nepřidávej \
            $prefix = trim($prefix, '\\') . '\\';
        }

        // normalize the base directory with a trailing separator
        if ($base_dir) {    //k prázdnému řetězci nepřidávej /
            $base_dir = str_replace('\\', self::DIRECTORY_SEPARATOR, $base_dir);  // i pro Windows vždy '/'
            $base_dir = rtrim($base_dir, self::DIRECTORY_SEPARATOR) . self::DIRECTORY_SEPARATOR;
        }
        // initialize the namespace prefix array
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
        return $this;
    }

    /**
     * For PEAR and PSR-0 like autoloading
     * @param type $prefix
     * @param type $base_dir
     * @param type $prepend
     * @return type
     */
    public function addPrefix($prefix, $base_dir, $prepend = false)
    {
        if (!$prefix) {
            $prefix = self::FALLBACK_PREFIX;
        }
        // normalize namespace prefix
        $prefix = str_replace(array('_', "\0"), array('\\', ''), $prefix);
        $this->addNamespace($prefix, $base_dir, $prepend);
        return $this;
    }

    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class)
    {
        // the current namespace prefix - initial is full class name including namespace
        if (strrpos($class, '_') !== FALSE) {
            $class = str_replace(array('_', "\0"), array('\\', ''), $class);
        }

        $prefix = $class;

        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);

            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);

            // try to load a mapped file for the prefix and relative class
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration of strrpos()
            $prefix = rtrim($prefix, '\\');
        }

        //part for PSR-0 and PEAR like
        $fallbackFile = $this->loadMappedFile(self::FALLBACK_PREFIX.'\\', $class);
        if($fallbackFile) {
            return $fallbackFile;
        }
        //end of part for PSR-0 and PEAR like

        // never found a mapped file
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     *
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        if (!isset($this->prefixes[$prefix])) {
            return false;
        }

        // look through base directories for this namespace prefix
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
                  . str_replace('\\', '/', $relative_class)
                  . '.php';

            // if the mapped file exists, require it
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }

        // never found it
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     *
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
