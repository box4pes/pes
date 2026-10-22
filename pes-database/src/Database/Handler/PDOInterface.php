<?php
/**
 *
 * @author pes2704
 */
namespace Pes\Database\Handler;

use Pes\Database\Statement\StatementInterface;

interface PDOInterface {
    // Tato návratová hodnota se liší od PDO
    /**
     * @return StatementInterface
     */
    // původní PDO handler interface:
//    public function prepare($statement , array $driver_options = array() );
//	 *  - PDO:  @return PDOStatement If the database server successfully prepares the statement,
//	 * <b>PDO::prepare</b> returns a
//	 * <b>PDOStatement</b> object.
//	 * If the database server cannot successfully prepare the statement,
//	 * <b>PDO::prepare</b> returns <b>FALSE</b> or emits
//	 * <b>PDOException</b> (depending on error handling).

		/**
		 * Initiates a transaction
		 * <p>Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO object instance are not committed until you end the transaction by calling <code>PDO::commit()</code>. Calling <code>PDO::rollBack()</code> will roll back all changes to the database and return the connection to autocommit mode.</p><p>Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit COMMIT will prevent you from rolling back any other changes within the transaction boundary.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdo.begintransaction.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function beginTransaction(): bool;

		/**
		 * Commits a transaction
		 * <p>Commits a transaction, returning the database connection to autocommit mode until the next call to <code>PDO::beginTransaction()</code> starts a new transaction.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdo.commit.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function commit(): bool;

		/**
		 * Connect to a database and return a PDO subclass for drivers that support it
		 * <p>Creates an instance of a <code>PDO</code> subclass for the database being connection if it exists, otherwise return a generic <code>PDO</code> instance.</p>
		 * @param string $dsn <p>The Data Source Name, or DSN, contains the information required to connect to the database.</p> <p>In general, a DSN consists of the PDO driver name, followed by a colon, followed by the PDO driver-specific connection syntax. Further information is available from the PDO driver-specific documentation.</p> <p>The <code>dsn</code> parameter supports three different methods of specifying the arguments required to create a database connection:</p> <p></p> Driver invocation  <p><code>dsn</code> contains the full DSN.</p>  URI invocation  <p><code>dsn</code> consists of <b><code>uri:</code></b> followed by a URI that defines the location of a file containing the DSN string. The URI can specify a local file or a remote URL.</p> <p><b><code>uri:file:///path/to/dsnfile</code></b></p>  Aliasing  <p><code>dsn</code> consists of a name <code>name</code> that maps to <code>pdo.dsn.<code>name</code></code> in php.ini defining the DSN string.</p> <p><b>Note</b>:</p><p>The alias must be defined in php.ini, and not .htaccess or httpd.conf</p>
		 * @param ?string $username <p>The user name for the DSN string. This parameter is optional for some PDO drivers.</p>
		 * @param ?string $password <p>The password for the DSN string. This parameter is optional for some PDO drivers.</p>
		 * @param ?array $options <p>A key=&gt;value array of driver-specific connection options.</p>
		 * @return static <p>Returns an instance of a <code>PDO</code> subclass for the corresponding PDO driver if it exists, or a generic <code>PDO</code> instance.</p>
		 * @link https://php.net/manual/en/pdo.connect.php
		 * @since PHP 8 >= 8.4.0
		 */
		public static function connect(string $dsn, ?string $username = null, #[\SensitiveParameter] ?string $password = null, ?array $options = null): static;

		/**
		 * Fetch the SQLSTATE associated with the last operation on the database handle
		 * @return ?string <p>Returns an SQLSTATE, a five characters alphanumeric identifier defined in the ANSI SQL-92 standard. Briefly, an SQLSTATE consists of a two characters class value followed by a three characters subclass value. A class value of 01 indicates a warning and is accompanied by a return code of SQL_SUCCESS_WITH_INFO. Class values other than '01', except for the class 'IM', indicate an error. The class 'IM' is specific to warnings and errors that derive from the implementation of PDO (or perhaps ODBC, if you're using the ODBC driver) itself. The subclass value '000' in any class indicates that there is no subclass for that SQLSTATE.</p><p><b>PDO::errorCode()</b> only retrieves error codes for operations performed directly on the database handle. If you create a PDOStatement object through <code>PDO::prepare()</code> or <code>PDO::query()</code> and invoke an error on the statement handle, <b>PDO::errorCode()</b> will not reflect that error. You must call <code>PDOStatement::errorCode()</code> to return the error code for an operation performed on a particular statement handle.</p><p>Returns <b><code>null</code></b> if no operation has been run on the database handle.</p>
		 * @link https://php.net/manual/en/pdo.errorcode.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function errorCode(): ?string;

		/**
		 * Fetch extended error information associated with the last operation on the database handle
		 * @return array <p><b>PDO::errorInfo()</b> returns an array of error information about the last operation performed by this database handle. The array consists of at least the following fields:</p>   Element Information     0 SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).   1 Driver-specific error code.   2 Driver-specific error message.   <p><b>Note</b>:</p><p>If the SQLSTATE error code is not set or there is no driver-specific error, the elements following element 0 will be set to <b><code>null</code></b>.</p> <p><b>PDO::errorInfo()</b> only retrieves error information for operations performed directly on the database handle. If you create a PDOStatement object through <code>PDO::prepare()</code> or <code>PDO::query()</code> and invoke an error on the statement handle, <b>PDO::errorInfo()</b> will not reflect the error from the statement handle. You must call <code>PDOStatement::errorInfo()</code> to return the error information for an operation performed on a particular statement handle.</p>
		 * @link https://php.net/manual/en/pdo.errorinfo.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function errorInfo(): array;

		/**
		 * Execute an SQL statement and return the number of affected rows
		 * <p><b>PDO::exec()</b> executes an SQL statement in a single function call, returning the number of rows affected by the statement.</p><p><b>PDO::exec()</b> does not return results from a SELECT statement. For a SELECT statement that you only need to issue once during your program, consider issuing <code>PDO::query()</code>. For a statement that you need to issue multiple times, prepare a PDOStatement object with <code>PDO::prepare()</code> and issue the statement with <code>PDOStatement::execute()</code>.</p>
		 * @param string $statement <p>The SQL statement to prepare and execute.</p> <p>Data inside the query should be properly escaped.</p>
		 * @return int|false <p><b>PDO::exec()</b> returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, <b>PDO::exec()</b> returns <code>0</code>.</p><p><b>Warning</b></p><p>This function may return Boolean <b><code>false</code></b>, but may also return a non-Boolean value which evaluates to <b><code>false</code></b>. Please read the section on Booleans for more information. Use the === operator for testing the return value of this function.</p><p>The following example incorrectly relies on the return value of <b>PDO::exec()</b>, wherein a statement that affected 0 rows results in a call to <code>die()</code>:</p> <code>&lt;&#63;php<br>$db-&gt;exec() or die(print_r($db-&gt;errorInfo(), true)); // incorrect<br>&#63;&gt;</code>
		 * @link https://php.net/manual/en/pdo.exec.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function exec(string $statement): int|false;

		/**
		 * Retrieve a database connection attribute
		 * <p>This function returns the value of a database connection attribute. To retrieve PDOStatement attributes, refer to <code>PDOStatement::getAttribute()</code>.</p><p>Note that some database/driver combinations may not support all of the database connection attributes.</p>
		 * @param int $attribute <p>One of the <code>PDO::ATTR_&#42;</code> constants. The generic attributes that apply to database connections are as follows:</p><ul> <li><code>PDO::ATTR_AUTOCOMMIT</code></li> <li><code>PDO::ATTR_CASE</code></li> <li><code>PDO::ATTR_CLIENT_VERSION</code></li> <li><code>PDO::ATTR_CONNECTION_STATUS</code></li> <li><code>PDO::ATTR_DRIVER_NAME</code></li> <li><code>PDO::ATTR_ERRMODE</code></li> <li><code>PDO::ATTR_ORACLE_NULLS</code></li> <li><code>PDO::ATTR_PERSISTENT</code></li> <li><code>PDO::ATTR_PREFETCH</code></li> <li><code>PDO::ATTR_SERVER_INFO</code></li> <li><code>PDO::ATTR_SERVER_VERSION</code></li> <li><code>PDO::ATTR_TIMEOUT</code></li> </ul>  Some drivers may make use of additional driver specific attributes. Note that driver specific attributes <i>must not</i> be used with other drivers.
		 * @return mixed <p>A successful call returns the value of the requested PDO attribute. An unsuccessful call returns <code>null</code>.</p>
		 * @link https://php.net/manual/en/pdo.getattribute.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function getAttribute(int $attribute): mixed;

		/**
		 * Return an array of available PDO drivers
		 * <p>This function returns all currently available PDO drivers which can be used in <code>DSN</code> parameter of <code>PDO::__construct()</code>.</p>
		 * @return array <p><b>PDO::getAvailableDrivers()</b> returns an array of PDO driver names. If no drivers are available, it returns an empty array.</p>
		 * @link https://php.net/manual/en/pdo.getavailabledrivers.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 1.0.3
		 */
		public static function getAvailableDrivers(): array;

		/**
		 * Checks if inside a transaction
		 * <p>Checks if a transaction is currently active within the driver. This method only works for database drivers that support transactions.</p>
		 * @return bool <p>Returns <b><code>true</code></b> if a transaction is currently active, and <b><code>false</code></b> if not.</p>
		 * @link https://php.net/manual/en/pdo.intransaction.php
		 * @since PHP 5 >= 5.3.3, Bundled pdo_pgsql, PHP 7, PHP 8
		 */
		public function inTransaction(): bool;

		/**
		 * Returns the ID of the last inserted row or sequence value
		 * <p>Returns the ID of the last inserted row, or the last value from a sequence object, depending on the underlying driver. For example, PDO_PGSQL allows the name of any sequence object to be specified for the <code>name</code> parameter.</p><p><b>Note</b>:</p><p>This method may not return a meaningful or consistent result across different PDO drivers, because the underlying database may not even support the notion of auto-increment fields or sequences.</p>
		 * @param ?string $name <p>Name of the sequence object from which the ID should be returned.</p>
		 * @return string|false <p>If a sequence name was not specified for the <code>name</code> parameter, <b>PDO::lastInsertId()</b> returns a string representing the row ID of the last row that was inserted into the database.</p><p>If a sequence name was specified for the <code>name</code> parameter, <b>PDO::lastInsertId()</b> returns a string representing the last value retrieved from the specified sequence object.</p><p>If the PDO driver does not support this capability, <b>PDO::lastInsertId()</b> triggers an <code>IM001</code> SQLSTATE.</p>
		 * @link https://php.net/manual/en/pdo.lastinsertid.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function lastInsertId(?string $name = null): string|false;

		/**
		 * Prepares a statement for execution and returns a statement object
		 * <p>Prepares an SQL statement to be executed by the <code>PDOStatement::execute()</code> method. The statement template can contain zero or more named (:name) or question mark (&#63;) parameter markers for which real values will be substituted when the statement is executed. Both named and question mark parameter markers cannot be used within the same statement template; only one or the other parameter style. Use these parameters to bind any user-input, do not include the user-input directly in the query.</p><p>You must include a unique parameter marker for each value you wish to pass in to the statement when you call <code>PDOStatement::execute()</code>. You cannot use a named parameter marker of the same name more than once in a prepared statement, unless emulation mode is on.</p><p><b>Note</b>:</p><p>Parameter markers can represent a complete data literal only. Neither part of literal, nor keyword, nor identifier, nor whatever arbitrary query part can be bound using parameters. For example, you cannot bind multiple values to a single parameter in the IN() clause of an SQL statement.</p><p>Calling <b>PDO::prepare()</b> and <code>PDOStatement::execute()</code> for statements that will be issued multiple times with different parameter values optimizes the performance of your application by allowing the driver to negotiate client and/or server side caching of the query plan and meta information. Also, calling <b>PDO::prepare()</b> and <code>PDOStatement::execute()</code> helps to prevent SQL injection attacks by eliminating the need to manually quote and escape the parameters.</p><p>PDO will emulate prepared statements/bound parameters for drivers that do not natively support them, and can also rewrite named or question mark style parameter markers to something more appropriate, if the driver supports one style but not the other.</p><p><b>Note</b>:  The parser used for emulated prepared statements and for rewriting named or question mark style parameters supports the non standard backslash escapes for single- and double quotes. That means that terminating quotes immediately preceeded by a backslash are not recognized as such, which may result in wrong detection of parameters causing the prepared statement to fail when it is executed. A work-around is to not use emulated prepares for such SQL queries, and to avoid rewriting of parameters by using a parameter style which is natively supported by the driver. </p><p>As of PHP 7.4.0, question marks can be escaped by doubling them. That means that the <code>&#63;&#63;</code> string will be translated to <code>&#63;</code> when sending the query to the database.</p>
		 * @param string $query <p>This must be a valid SQL statement template for the target database server.</p>
		 * @param array $options <p>This array holds one or more key=&gt;value pairs to set attribute values for the PDOStatement object that this method returns. You would most commonly use this to set the <code>PDO::ATTR_CURSOR</code> value to <code>PDO::CURSOR_SCROLL</code> to request a scrollable cursor. Some drivers have driver-specific options that may be set at prepare-time.</p>
		 * @return PDOStatement|false <p>If the database server successfully prepares the statement, <b>PDO::prepare()</b> returns a <code>PDOStatement</code> object. If the database server cannot successfully prepare the statement, <b>PDO::prepare()</b> returns <b><code>false</code></b> or emits <code>PDOException</code> (depending on error handling).</p><p><b>Note</b>:</p><p>Emulated prepared statements does not communicate with the database server so <b>PDO::prepare()</b> does not check the statement.</p>
		 * @link https://php.net/manual/en/pdo.prepare.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PHP 8,PECL pdo >= 0.1.0
		 */
		public function prepare(string $query, array $options = []): \PDOStatement|false;

		/**
		 * Prepares and executes an SQL statement without placeholders
		 * <p><b>PDO::query()</b> prepares and executes an SQL statement in a single function call, returning the statement as a <code>PDOStatement</code> object.</p><p>For a query that you need to issue multiple times, you will realize better performance if you prepare a <code>PDOStatement</code> object using <code>PDO::prepare()</code> and issue the statement with multiple calls to <code>PDOStatement::execute()</code>.</p><p>If you do not fetch all of the data in a result set before issuing your next call to <b>PDO::query()</b>, your call may fail. Call <code>PDOStatement::closeCursor()</code> to release the database resources associated with the <code>PDOStatement</code> object before issuing your next call to <b>PDO::query()</b>.</p><p><b>Note</b>:</p><p>If the <code>query</code> contains placeholders, the statement must be prepared and executed separately using <code>PDO::prepare()</code> and <code>PDOStatement::execute()</code> methods.</p>
		 * @param string $query <p>The SQL statement to prepare and execute.</p> <p>If the SQL contains placeholders, <code>PDO::prepare()</code> and <code>PDOStatement::execute()</code> must be used instead. Alternatively, the SQL can be prepared manually before calling <b>PDO::query()</b>, with the data properly formatted using <code>PDO::quote()</code> if the driver supports it.</p>
		 * @param ?int $fetchMode <p>The default fetch mode for the returned <code>PDOStatement</code>. It must be one of the <code>PDO::FETCH_&#42;</code> constants.</p> <p>If this argument is passed to the function, the remaining arguments will be treated as though <code>PDOStatement::setFetchMode()</code> was called on the resultant statement object. The subsequent arguments vary depending on the selected fetch mode.</p>
		 * @return PDOStatement|false <p>Returns a <code>PDOStatement</code> object or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdo.query.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.0
		 */
		public function query(string $query, ?int $fetchMode = null): \PDOStatement|false;

		/**
		 * Quotes a string for use in a query
		 * <p><b>PDO::quote()</b> places quotes around the input string (if required) and escapes special characters within the input string, using a quoting style appropriate to the underlying driver.</p><p>If you are using this function to build SQL statements, you are <i>strongly</i> recommended to use <code>PDO::prepare()</code> to prepare SQL statements with bound parameters instead of using <b>PDO::quote()</b> to interpolate user input into an SQL statement. Prepared statements with bound parameters are not only more portable, more convenient, immune to SQL injection, but are often much faster to execute than interpolated queries, as both the server and client side can cache a compiled form of the query.</p><p>Not all PDO drivers implement this method (notably PDO_ODBC). Consider using prepared statements instead.</p><p>The character set must be set either on the server level, or within the database connection itself (depending on the driver) for it to affect <b>PDO::quote()</b>. See the driver-specific documentation for more information.</p>
		 * @param string $string <p>The string to be quoted.</p>
		 * @param int $type <p>Provides a hint to the type of data for drivers that have alternate quoting styles. For example <b><code>PDO_PARAM_LOB</code></b> will tell the driver to escape binary data.</p>
		 * @return string|false <p>Returns a quoted string that is theoretically safe to pass into an SQL statement. Returns <b><code>false</code></b> if the driver does not support quoting in this way.</p>
		 * @link https://php.net/manual/en/pdo.quote.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.2.1
		 */
		public function quote(string $string, int $type = PDO::PARAM_STR): string|false;

		/**
		 * Rolls back a transaction
		 * <p>Rolls back the current transaction, as initiated by <code>PDO::beginTransaction()</code>.</p><p>If the database was set to autocommit mode, this function will restore autocommit mode after it has rolled back the transaction.</p><p>Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language (DDL) statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit COMMIT will prevent you from rolling back any other changes within the transaction boundary.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdo.rollback.php
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function rollBack(): bool;

		/**
		 * Set an attribute
		 * <p>Sets an attribute on the database handle. Some available generic attributes are listed below; some drivers may make use of additional driver specific attributes. Note that driver specific attributes <i>must not</i> be used with other drivers.</p><p>Force column names to a specific case. Can take one of the following values:</p><p>Error reporting mode of PDO. Can take one of the following values:</p><p><b>Note</b>:  This attribute is available with all drivers, not just Oracle. </p><p>Determines if and how <b><code>null</code></b> and empty strings should be converted. Can take one of the following values:</p><p>Controls whether fetched values (except <b><code>null</code></b>) are converted to strings. Takes a value of type <code>bool</code>: <b><code>true</code></b> to enable and <b><code>false</code></b> to disable (default). <b><code>null</code></b> values remain unchanged unless <b><code>PDO::ATTR_ORACLE_NULLS</code></b> is set to <b><code>PDO::NULL_TO_STRING</code></b>.</p><p>Set user-supplied statement class derived from PDOStatement. Requires <code>array(string classname, array(mixed constructor_args))</code>.</p><p>Cannot be used with persistent PDO instances.</p><p>Specifies the timeout duration in seconds. Takes a value of type <code>int</code>.</p><p><b>Note</b>:</p><p>Not all drivers support this option, and its meaning may differ from driver to driver. For example, SQLite will wait for up to this time value before giving up on obtaining a writable lock, but other drivers may interpret this as a connection or a read timeout interval.</p><p><b>Note</b>:  Only available for the OCI, Firebird, and MySQL drivers. </p><p>Whether to autocommit every single statement. Takes a value of type <code>bool</code>: <b><code>true</code></b> to enable and <b><code>false</code></b> to disable. By default, <b><code>true</code></b>.</p><p><b>Note</b>:  Only available for the OCI, Firebird, and MySQL drivers. </p><p>Whether enable or disable emulation of prepared statements. Some drivers do not support prepared statements natively or have limited support for them. If set to <b><code>true</code></b> PDO will always emulate prepared statements, otherwise PDO will attempt to use native prepared statements. In case the driver cannot successfully prepare the current query, PDO will always fall back to emulating the prepared statement.</p><p><b>Note</b>:  Only available for the MySQL driver. </p><p>Whether to use buffered queries. Takes a value of type <code>bool</code>: <b><code>true</code></b> to enable and <b><code>false</code></b> to disable. By default, <b><code>true</code></b>.</p><p>Set the default fetch mode. A description of the modes and how to use them is available in the <code>PDOStatement::fetch()</code> documentation.</p>
		 * @param int $attribute <p>The attribute to modify.</p>
		 * @param mixed $value <p>The value to set the <code>attribute</code>, might require a specific type depending on the attribute.</p>
		 * @return bool <p>Returns <b><code>true</code></b> on success or <b><code>false</code></b> on failure.</p>
		 * @link https://php.net/manual/en/pdo.setattribute.php
		 * @see PDO::getAttribute(), PDOStatement::getAttribute(), PDOStatement::setAttribute()
		 * @since PHP 5 >= 5.1.0, PHP 7, PHP 8, PECL pdo >= 0.1.0
		 */
		public function setAttribute(int $attribute, mixed $value): bool;

}
