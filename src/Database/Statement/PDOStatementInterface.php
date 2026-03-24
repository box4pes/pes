<?php
/**
 * Interface pro třídy vytvářející abstrakci nad databází. Vychází z předpokladu, že PHP PDO + PDOStatement je dobrá abstrakce
 * nad databází a tento interface jen určuje povinně implementované metody
 */
namespace Pes\Database\Statement;

interface PDOStatementInterface {


		/**
		 * Bind a column to a PHP variable
		 * <p><b>PDOStatement::bindColumn()</b> arranges to have a particular variable bound to a given column in the result-set from a query. Each call to <code>PDOStatement::fetch()</code> or <code>PDOStatement::fetchAll()</code> will update all the variables that are bound to columns.</p><p><b>Note</b>:</p><p>Since information about the columns is not always available to PDO until the statement is executed, portable applications should call this function <i>after</i> <code>PDOStatement::execute()</code>.</p><p>However, to be able to bind a LOB column as a stream when using the <i>PgSQL driver</i>, applications should call this method <i>before</i> calling <code>PDOStatement::execute()</code>, otherwise the large object OID will be returned as an integer.</p>
		 * @param string|int $column <p>Number of the column (1-indexed) or name of the column in the result set. If using the column name, be aware that the name should match the case of the column, as returned by the driver.</p>
		 * @param mixed $var <p>Name of the PHP variable to which the column will be bound.</p>
		 * @param int $type <p>Data type of the parameter, specified by the <code>PDO::PARAM_&#42;</code> constants.</p>
		 * @param int $maxLength <p>A hint for pre-allocation.</p>
		 * @param mixed $driverOptions <p>Optional parameter(s) for the driver.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.bindcolumn.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function bindColumn(string|int $column, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool;

		/**
		 * Binds a parameter to the specified variable name
		 * <p>Binds a PHP variable to a corresponding named or question mark placeholder in the SQL statement that was used to prepare the statement. Unlike <code>PDOStatement::bindValue()</code>, the variable is bound as a reference and will only be evaluated at the time that <code>PDOStatement::execute()</code> is called.</p><p>Most parameters are input parameters, that is, parameters that are used in a read-only fashion to build up the query (but may nonetheless be cast according to <code>type</code>). Some drivers support the invocation of stored procedures that return data as output parameters, and some also as input/output parameters that both send in data and are updated to receive it.</p>
		 * @param string|int $param <p>Parameter identifier. For a prepared statement using named placeholders, this will be a parameter name of the form :name. For a prepared statement using question mark placeholders, this will be the 1-indexed position of the parameter.</p>
		 * @param mixed $var <p>Name of the PHP variable to bind to the SQL statement parameter.</p>
		 * @param int $type <p>Explicit data type for the parameter using the <code>PDO::PARAM_&#42;</code> constants. To return an INOUT parameter from a stored procedure, use the bitwise OR operator to set the <b><code>PDO::PARAM_INPUT_OUTPUT</code></b> bits for the <code>type</code> parameter.</p>
		 * @param int $maxLength <p>Length of the data type. To indicate that a parameter is an OUT parameter from a stored procedure, you must explicitly set the length. Meaningful only when <code>type</code> parameter is <b><code>PDO::PARAM_INPUT_OUTPUT</code></b>.</p>
		 * @param mixed $driverOptions
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.bindparam.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function bindParam(string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool;

		/**
		 * Binds a value to a parameter
		 * <p>Binds a value to a corresponding named or question mark placeholder in the SQL statement that was used to prepare the statement.</p>
		 * @param string|int $param <p>Parameter identifier. For a prepared statement using named placeholders, this will be a parameter name of the form :name. For a prepared statement using question mark placeholders, this will be the 1-indexed position of the parameter.</p>
		 * @param mixed $value <p>The value to bind to the parameter.</p>
		 * @param int $type <p>Explicit data type for the parameter using the <code>PDO::PARAM_&#42;</code> constants.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.bindvalue.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 1.0.0
		 */
		public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool;

		/**
		 * Closes the cursor, enabling the statement to be executed again
		 * <p><b>PDOStatement::closeCursor()</b> frees up the connection to the server so that other SQL statements may be issued, but leaves the statement in a state that enables it to be executed again.</p><p>This method is useful for database drivers that do not support executing a PDOStatement object when a previously executed PDOStatement object still has unfetched rows. If your database driver suffers from this limitation, the problem may manifest itself in an out-of-sequence error.</p><p><b>PDOStatement::closeCursor()</b> is implemented either as an optional driver specific method (allowing for maximum efficiency), or as the generic PDO fallback if no driver specific function is installed. The PDO generic fallback is semantically the same as writing the following code in your PHP script:</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.closecursor.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.9.0
		 */
		public function closeCursor(): bool;

		/**
		 * Returns the number of columns in the result set
		 * <p>Use <b>PDOStatement::columnCount()</b> to return the number of columns in the result set represented by the PDOStatement object.</p><p>If the PDOStatement object was returned from <code>PDO::query()</code>, the column count is immediately available.</p><p>If the PDOStatement object was returned from <code>PDO::prepare()</code>, an accurate column count will not be available until you invoke <code>PDOStatement::execute()</code>.</p>
		 * @return int <p>Returns the number of columns in the result set represented by the PDOStatement object, even if the result set is empty. If there is no result set, <b>PDOStatement::columnCount()</b> returns <code>0</code>.</p>
		 * @link https://php.net/manual/en/pdostatement.columncount.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function columnCount(): int;

		/**
		 * Dump an SQL prepared command
		 * <p>Dumps the information contained by a prepared statement directly on the output. It will provide the <code>SQL</code> query in use, the number of parameters used (<code>Params</code>), the list of parameters with their key name or position, their name, their position in the query (if this is supported by the PDO driver, otherwise, it will be -1), type (<code>param_type</code>) as an integer, and a boolean value <code>is_param</code>.</p><p>This is a debug function, which dumps the data directly to the normal output.</p><p>As with anything that outputs its result directly to the browser, the output-control functions can be used to capture the output of this function, and save it in a <code>string</code> (for example).</p><p>This will only dump the parameters in the statement at the moment of the dump. Extra parameters are not stored in the statement, and not displayed.</p>
		 * @return ?bool <p>Returns <b><code>null</code></b>, or <b><code>false</code></b> in case of an error.</p>
		 * @link https://php.net/manual/en/pdostatement.debugdumpparams.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.9.0
		 */
		public function debugDumpParams(): ?bool;

		/**
		 * Fetch the SQLSTATE associated with the last operation on the statement handle
		 * @return ?string <p>Identical to <code>PDO::errorCode()</code>, except that <b>PDOStatement::errorCode()</b> only retrieves error codes for operations performed with PDOStatement objects.</p>
		 * @link https://php.net/manual/en/pdostatement.errorcode.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function errorCode(): ?string;

		/**
		 * Fetch extended error information associated with the last operation on the statement handle
		 * @return array <p><b>PDOStatement::errorInfo()</b> returns an array of error information about the last operation performed by this statement handle. The array consists of at least the following fields:</p>   Element Information     0 SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).   1 Driver specific error code.   2 Driver specific error message.
		 * @link https://php.net/manual/en/pdostatement.errorinfo.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function errorInfo(): array;

		/**
		 * Executes a prepared statement
		 * <p>Execute the prepared statement. If the prepared statement included parameter markers, either:</p><p><code>PDOStatement::bindParam()</code> and/or <code>PDOStatement::bindValue()</code> has to be called to bind either variables or values (respectively) to the parameter markers. Bound variables pass their value as input and receive the output value, if any, of their associated parameter markers</p><p>or an array of input-only parameter values has to be passed</p>
		 * @param ?array $params <p>An array of values with as many elements as there are bound parameters in the SQL statement being executed. All values are treated as <b><code>PDO::PARAM_STR</code></b>.</p> <p>Multiple values cannot be bound to a single parameter; for example, it is not allowed to bind two values to a single named parameter in an IN() clause.</p> <p>Binding more values than specified is not possible; if more keys exist in <code>params</code> than in the SQL specified in the <code>PDO::prepare()</code>, then the statement will fail and an error is emitted.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.execute.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function execute(?array $params = null): bool;

		/**
		 * Fetches the next row from a result set
		 * <p>Fetches a row from a result set associated with a PDOStatement object. The <code>mode</code> parameter determines how PDO returns the row.</p>
		 * @param int $mode <p>Controls how the next row will be returned to the caller. This value must be one of the <code>PDO::FETCH_&#42;</code> constants, defaulting to value of <code>PDO::ATTR_DEFAULT_FETCH_MODE</code> (which defaults to <code>PDO::FETCH_BOTH</code>).</p><ul> <li><p><code>PDO::FETCH_ASSOC</code>: returns an array indexed by column name as returned in your result set</p></li> <li><p><code>PDO::FETCH_BOTH</code> (default): returns an array indexed by both column name and 0-indexed column number as returned in your result set</p></li> <li><p><code>PDO::FETCH_BOUND</code>: returns <b><code>true</code></b> and assigns the values of the columns in your result set to the PHP variables to which they were bound with the <code>PDOStatement::bindColumn()</code> method</p></li> <li><p><code>PDO::FETCH_CLASS</code>: returns a new instance of the requested class, mapping the columns of the result set to named properties in the class, and calling the constructor afterwards, unless <code>PDO::FETCH_PROPS_LATE</code> is also given. If <code>mode</code> includes PDO::FETCH_CLASSTYPE (e.g. <code>PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE</code>) then the name of the class is determined from a value of the first column.</p></li> <li><p><code>PDO::FETCH_INTO</code>: updates an existing instance of the requested class, mapping the columns of the result set to named properties in the class</p></li> <li><p><code>PDO::FETCH_LAZY</code>: combines <code>PDO::FETCH_BOTH</code> and <code>PDO::FETCH_OBJ</code>, and is returning a <code>PDORow</code> object which is creating the object property names as they are accessed.</p></li> <li><p><code>PDO::FETCH_NAMED</code>: returns an array with the same form as <code>PDO::FETCH_ASSOC</code>, except that if there are multiple columns with the same name, the value referred to by that key will be an array of all the values in the row that had that column name</p></li> <li><p><code>PDO::FETCH_NUM</code>: returns an array indexed by column number as returned in your result set, starting at column 0</p></li> <li><p><code>PDO::FETCH_OBJ</code>: returns an anonymous object with property names that correspond to the column names returned in your result set</p></li> <li><p><code>PDO::FETCH_PROPS_LATE</code>: when used with <code>PDO::FETCH_CLASS</code>, the constructor of the class is called before the properties are assigned from the respective column values.</p></li> </ul>
		 * @param int $cursorOrientation <p>For a PDOStatement object representing a scrollable cursor, this value determines which row will be returned to the caller. This value must be one of the <code>PDO::FETCH_ORI_&#42;</code> constants, defaulting to <code>PDO::FETCH_ORI_NEXT</code>. To request a scrollable cursor for your PDOStatement object, you must set the <code>PDO::ATTR_CURSOR</code> attribute to <code>PDO::CURSOR_SCROLL</code> when you prepare the SQL statement with <code>PDO::prepare()</code>.</p>
		 * @param int $cursorOffset <p>For a PDOStatement object representing a scrollable cursor for which the <code>cursorOrientation</code> parameter is set to <code>PDO::FETCH_ORI_ABS</code>, this value specifies the absolute number of the row in the result set that shall be fetched.</p> <p>For a PDOStatement object representing a scrollable cursor for which the <code>cursorOrientation</code> parameter is set to <code>PDO::FETCH_ORI_REL</code>, this value specifies the row to fetch relative to the cursor position before <b>PDOStatement::fetch()</b> was called.</p>
		 * @return mixed <p>The return value of this function on success depends on the fetch type. In all cases, <b><code>false</code></b> is returned on failure or if there are no more rows.</p>
		 * @link https://php.net/manual/en/pdostatement.fetch.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed;

		/**
		 * Fetches the remaining rows from a result set
		 * @param int $mode <p>Controls the contents of the returned array as documented in <code>PDOStatement::fetch()</code>. Defaults to value of <b><code>PDO::ATTR_DEFAULT_FETCH_MODE</code></b> (which defaults to <b><code>PDO::FETCH_BOTH</code></b>)</p> <p>To return an array consisting of all values of a single column from the result set, specify <b><code>PDO::FETCH_COLUMN</code></b>. You can specify which column you want with the <code>column</code> parameter.</p> <p>To index the resulting array by a certain column's value (instead of consecutive numbers), put this column's name first in the column list in SQL, and use <b><code>PDO::FETCH_UNIQUE</code></b>. This column must contain only unique values or some data will be lost.</p> <p>To group results in the form of a 3-dimensional array indexed by values of a specified column, put this column's name first in the column list in SQL and use <b><code>PDO::FETCH_GROUP</code></b>.</p> <p>To group results in the form of a 2-dimensional array use bitwise-OR <b><code>PDO::FETCH_GROUP</code></b> with <b><code>PDO::FETCH_COLUMN</code></b>. The results will be grouped by the first column, with the array element's value being a list array of the corresponding entries from the second column.</p>
		 * @return array <p><b>PDOStatement::fetchAll()</b> returns an array containing all of the remaining rows in the result set. The array represents each row as either an array of column values or an object with properties corresponding to each column name. An empty array is returned if there are zero results to fetch.</p><p>Using this method to fetch large result sets will result in a heavy demand on system and possibly network resources. Rather than retrieving all of the data and manipulating it in PHP, consider using the database server to manipulate the result sets. For example, use the WHERE and ORDER BY clauses in SQL to restrict results before retrieving and processing them with PHP.</p>
		 * @link https://php.net/manual/en/pdostatement.fetchall.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function fetchAll(int $mode = PDO::FETCH_DEFAULT): array;

		/**
		 * Returns a single column from the next row of a result set
		 * <p>Returns a single column from the next row of a result set or <b><code>false</code></b> if there are no more rows.</p><p><b>Note</b>:</p><p><b>PDOStatement::fetchColumn()</b> should not be used to retrieve boolean columns, as it is impossible to distinguish a value of <b><code>false</code></b> from there being no more rows to retrieve. Use <code>PDOStatement::fetch()</code> instead.</p>
		 * @param int $column <p>0-indexed number of the column you wish to retrieve from the row. If no value is supplied, <b>PDOStatement::fetchColumn()</b> fetches the first column.</p>
		 * @return mixed <p><b>PDOStatement::fetchColumn()</b> returns a single column from the next row of a result set or <b><code>false</code></b> if there are no more rows.</p><p><b>Warning</b></p> <p>There is no way to return another column from the same row if you use <b>PDOStatement::fetchColumn()</b> to retrieve data.</p>
		 * @link https://php.net/manual/en/pdostatement.fetchcolumn.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.9.0
		 */
		public function fetchColumn(int $column = 0): mixed;

		/**
		 * Fetches the next row and returns it as an object
		 * <p>Fetches the next row and returns it as an object. This function is an alternative to <code>PDOStatement::fetch()</code> with <b><code>PDO::FETCH_CLASS</code></b> or <b><code>PDO::FETCH_OBJ</code></b> style.</p><p>When an object is fetched, its properties are assigned from respective column values, and afterwards its constructor is invoked.</p>
		 * @param ?string $class <p>Name of the created class.</p>
		 * @param array $constructorArgs <p>Elements of this array are passed to the constructor.</p>
		 * @return object|false <p>Returns an instance of the required class with property names that correspond to the column names or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.fetchobject.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.4
		 */
		public function fetchObject(?string $class = "stdClass", array $constructorArgs = []): object|false;

		/**
		 * Retrieve a statement attribute
		 * <p>Gets an attribute of the statement. Currently, no generic attributes exist but only driver specific:</p><p><code>PDO::ATTR_CURSOR_NAME</code> (Firebird and ODBC specific): Get the name of cursor for <code>UPDATE ... WHERE CURRENT OF</code>.</p>
		 * @param int $name <p>The attribute to query.</p>
		 * @return mixed <p>Returns the attribute value.</p>
		 * @link https://php.net/manual/en/pdostatement.getattribute.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function getAttribute(int $name): mixed;

		/**
		 * Returns metadata for a column in a result set
		 * <p>Retrieves the metadata for a 0-indexed column in a result set as an associative array.</p><p>Some drivers may not implement <b>PDOStatement::getColumnMeta()</b>, as it is optional. However, all PDO drivers documented in the manual implement this function.</p>
		 * @param int $column <p>The 0-indexed column in the result set.</p>
		 * @return array|false <p>Returns an associative array containing the following values representing the metadata for a single column:</p> <b>Column metadata</b>     Name Value     <code>native_type</code> The PHP native type used to represent the column value.   <code>driver:decl_type</code> The SQL type used to represent the column value in the database. If the column in the result set is the result of a function, this value is not returned by <b>PDOStatement::getColumnMeta()</b>.    <code>flags</code> Any flags set for this column.   <code>name</code> The name of this column as returned by the database.   <code>table</code> The name of this column's table as returned by the database.   <code>len</code> The length of this column. Normally <code>-1</code> for types other than floating point decimals.   <code>precision</code> The numeric precision of this column. Normally <code>0</code> for types other than floating point decimals.   <code>pdo_type</code> The type of this column as represented by the <code>PDO::PARAM_&#42;</code> constants.   <p>Returns <b><code>false</code></b> if the requested column does not exist in the result set, or if no result set exists.</p>
		 * @link https://php.net/manual/en/pdostatement.getcolumnmeta.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function getColumnMeta(int $column): array|false;

		/**
		 * Gets result set iterator
		 * <p></p><p>This function is currently not documented; only its argument list is available.</p>
		 * @return Iterator
		 * @link https://php.net/manual/en/pdostatement.getiterator.php
		 * @since PHP 8
		 */
		public function getIterator(): \Iterator;

		/**
		 * Advances to the next rowset in a multi-rowset statement handle
		 * <p>Some database servers support stored procedures that return more than one rowset (also known as a result set). <b>PDOStatement::nextRowset()</b> enables you to access the second and subsequent rowsets associated with a PDOStatement object. Each rowset can have a different set of columns from the preceding rowset.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.nextrowset.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function nextRowset(): bool;

		/**
		 * Returns the number of rows affected by the last SQL statement
		 * <p><b>PDOStatement::rowCount()</b> returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement executed by the corresponding <code>PDOStatement</code> object.</p><p>For statements that produce result sets, such as <code>SELECT</code>, the behavior is undefined and can be different for each driver. Some databases may return the number of rows produced by that statement (e.g. MySQL in buffered mode), but this behaviour is not guaranteed for all databases and should not be relied on for portable applications.</p><p><b>Note</b>:</p><p>This method returns "0" (zero) with the SQLite driver at all times, and with the PostgreSQL driver only when setting the <b><code>PDO::ATTR_CURSOR</code></b> statement attribute to <b><code>PDO::CURSOR_SCROLL</code></b>.</p>
		 * @return int <p>Returns the number of rows.</p>
		 * @link https://php.net/manual/en/pdostatement.rowcount.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function rowCount(): int;

		/**
		 * Set a statement attribute
		 * <p>Sets an attribute on the statement. Currently, no generic attributes are set but only driver specific:</p><p><code>PDO::ATTR_CURSOR_NAME</code> (Firebird and ODBC specific): Set the name of cursor for <code>UPDATE ... WHERE CURRENT OF</code>.</p>
		 * @param int $attribute <p>The attribute to modify.</p>
		 * @param mixed $value <p>The value to set the <code>attribute</code>, might require a specific type depending on the attribute.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.setattribute.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function setAttribute(int $attribute, mixed $value): bool;

		/**
		 * Set the default fetch mode for this statement
		 * @param int $mode <p>The fetch mode must be one of the <b><code>PDO::FETCH_&#42;</code></b> constants.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdostatement.setfetchmode.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function setFetchMode(int $mode): bool;

}