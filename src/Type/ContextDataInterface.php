<?php

namespace Pes\Type;

use Psr\Log\LoggerInterface;

/**
 *
 * @author pes2704
 */
interface ContextDataInterface extends  \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
    /**
     *
     */
    public function setDebugMode();

    /**
     *
     */
    public function getDebugMode();

    /**
     *
     */
    public function getStatus();

    /**
     * Metoda přidá data z pole nebo \ArrayObject zadaného jako parametr.
     * @param mixed $appendedData array nebo \ArrayObject
     * @return \ContextDataInterface
     */
    public function exchangeData($data): \ContextDataInterface;

    /**
     * Metoda přidá data z pole nebo \ArrayObject zadaného jako parametr.
     * @param mixed $appendedData array nebo \ArrayObject
     * @return \ContextDataInterface
     */
    public function appendData($appendedData): \ContextDataInterface ;

    /**
     *
     * @param type $name
     * @param type $value
     */
    public function assign($name, $value);

    #### metody ArrayObject, které nejsou součástí tozhranné uvedených v deklaraci extends. ArrayObject nemá interface, uvedení těchto metod slouží k našeptávání při použití ContextDataInterface.


		/**
		 * Appends the value
		 * <p>Appends a new value as the last element.</p><p><b>Note</b>:</p><p>This method cannot be called when the ArrayObject was constructed from an object. Use <code>ArrayObject::offsetSet()</code> instead.</p>
		 * @param mixed $value <p>The value being appended.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.append.php
		 * @since PHP 5, PHP 7
		 */
		public function append($value): void;

		/**
		 * Sort the entries by value
		 * <p>Sorts the entries such that the keys maintain their correlation with the entries they are associated with. This is used mainly when sorting associative arrays where the actual element order is significant.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.asort.php
		 * @since PHP 5 >= 5.2.0, PHP 7
		 */
		public function asort(): void;

		/**
		 * Exchange the array for another one
		 * <p>Exchange the current <code>array</code> with another <code>array</code> or <code>object</code>.</p>
		 * @param mixed $input <p>The new <code>array</code> or <code>object</code> to exchange with the current array.</p>
		 * @return array <p>Returns the old <code>array</code>.</p>
		 * @link http://php.net/manual/en/arrayobject.exchangearray.php
		 * @since PHP 5 >= 5.1.0, PHP 7
		 */
		public function exchangeArray($input): array;

		/**
		 * Creates a copy of the ArrayObject
		 * <p>Exports the ArrayObject to an array.</p>
		 * @return array <p>Returns a copy of the array. When the ArrayObject refers to an object, an array of the public properties of that object will be returned.</p>
		 * @link http://php.net/manual/en/arrayobject.getarraycopy.php
		 * @since PHP 5, PHP 7
		 */
		public function getArrayCopy(): array;

		/**
		 * Gets the behavior flags
		 * <p>Gets the behavior flags of the ArrayObject. See the ArrayObject::setFlags method for a list of the available flags.</p>
		 * @return int <p>Returns the behavior flags of the ArrayObject.</p>
		 * @link http://php.net/manual/en/arrayobject.getflags.php
		 * @since PHP 5 >= 5.1.0, PHP 7
		 */
		public function getFlags(): int;

		/**
		 * Gets the iterator classname for the ArrayObject
		 * <p>Gets the class name of the array iterator that is used by ArrayObject::getIterator().</p>
		 * @return string <p>Returns the iterator class name that is used to iterate over this object.</p>
		 * @link http://php.net/manual/en/arrayobject.getiteratorclass.php
		 * @since PHP 5 >= 5.1.0, PHP 7
		 */
		public function getIteratorClass(): string;

		/**
		 * Sort the entries by key
		 * <p>Sorts the entries by key, maintaining key to entry correlations. This is useful mainly for associative arrays.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.ksort.php
		 * @since PHP 5 >= 5.2.0, PHP 7
		 */
		public function ksort(): void;

		/**
		 * Sort an array using a case insensitive "natural order" algorithm
		 * <p>This method is a case insensitive version of ArrayObject::natsort.</p><p>This method implements a sort algorithm that orders alphanumeric strings in the way a human being would while maintaining key/value associations. This is described as a "natural ordering".</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.natcasesort.php
		 * @since PHP 5 >= 5.2.0, PHP 7
		 */
		public function natcasesort(): void;

		/**
		 * Sort entries using a "natural order" algorithm
		 * <p>This method implements a sort algorithm that orders alphanumeric strings in the way a human being would while maintaining key/value associations. This is described as a "natural ordering". An example of the difference between this algorithm and the regular computer string sorting algorithms (used in ArrayObject::asort) method can be seen in the example below.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.natsort.php
		 * @since PHP 5 >= 5.2.0, PHP 7
		 */
		public function natsort(): void;

		/**
		 * Sets the behavior flags
		 * <p>Set the flags that change the behavior of the ArrayObject.</p>
		 * @param int $flags <p>The new ArrayObject behavior. It takes on either a bitmask, or named constants. Using named constants is strongly encouraged to ensure compatibility for future versions.</p> <p>The available behavior flags are listed below. The actual meanings of these flags are described in the predefined constants.</p> <b>ArrayObject behavior flags</b>   value constant     1  ArrayObject::STD_PROP_LIST    2  ArrayObject::ARRAY_AS_PROPS
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.setflags.php
		 * @since PHP 5 >= 5.1.0, PHP 7
		 */
		public function setFlags(int $flags): void;

		/**
		 * Sets the iterator classname for the ArrayObject
		 * <p>Sets the classname of the array iterator that is used by ArrayObject::getIterator().</p>
		 * @param string $iterator_class <p>The classname of the array iterator to use when iterating over this object.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.setiteratorclass.php
		 * @since PHP 5 >= 5.1.0, PHP 7
		 */
		public function setIteratorClass(string $iterator_class): void;

		/**
		 * Sort the entries with a user-defined comparison function and maintain key association
		 * <p>This function sorts the entries such that keys maintain their correlation with the entry that they are associated with, using a user-defined comparison function.</p><p>This is used mainly when sorting associative arrays where the actual element order is significant.</p>
		 * @param callable $cmp_function <p>Function <code>cmp_function</code> should accept two parameters which will be filled by pairs of entries. The comparison function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.uasort.php
		 * @since PHP 5 >= 5.2.0, PHP 7
		 */
		public function uasort(callable $cmp_function): void;

		/**
		 * Sort the entries by keys using a user-defined comparison function
		 * <p>This function sorts the keys of the entries using a user-supplied comparison function. The key to entry correlations will be maintained.</p>
		 * @param callable $cmp_function <p>The callback comparison function.</p> <p>Function <code>cmp_function</code> should accept two parameters which will be filled by pairs of entry keys. The comparison function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.</p>
		 * @return void <p>No value is returned.</p>
		 * @link http://php.net/manual/en/arrayobject.uksort.php
		 * @since PHP 5 >= 5.2.0, PHP 7
		 */
		public function uksort(callable $cmp_function): void;

}
