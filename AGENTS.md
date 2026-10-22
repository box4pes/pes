# AGENTS.md

## Cursor Cloud specific instructions

### Overview
This is the **Pes Framework** — a PSR-compliant PHP 8.4 library providing HTTP handling, routing, middleware, DI container, database abstraction (PDO), session management, security/crypto, and view/template rendering. It is a library, not a deployable web application.

### Required Services
- **PHP 8.4** with extensions: pdo, pdo_mysql, mbstring, xml, openssl
- **MariaDB** (or MySQL) on localhost:3306

### Database Setup
The test database must exist before running database tests:
```sql
CREATE DATABASE IF NOT EXISTS pes CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER IF NOT EXISTS 'pes_tester'@'localhost' IDENTIFIED BY 'pes_tester';
GRANT ALL PRIVILEGES ON pes.* TO 'pes_tester'@'localhost';
FLUSH PRIVILEGES;
USE pes;
CREATE TABLE IF NOT EXISTS person (number INT, name VARCHAR(255), surname VARCHAR(255));
```

### Starting MariaDB
```bash
sudo mkdir -p /run/mysqld && sudo chown mysql:mysql /run/mysqld
sudo mysqld_safe &
sleep 2
sudo chmod 755 /run/mysqld
```

### Running Tests
Tests **must** be run from the `tests/` directory (not from the workspace root) because log paths in test fixtures use `../Tests_logs/` relative to CWD:
```bash
cd /workspace/tests
../vendor/bin/phpunit --bootstrap ../vendor/autoload.php .
```

The `Tests_logs/` directory must exist at the workspace root for file-logger-dependent tests to pass.

### Running Lint (PHP-CS-Fixer)
```bash
cd /workspace
vendor/bin/php-cs-fixer fix --dry-run --diff src/
```
Note: running on the full `src/` tree can be slow. Target specific files/directories for faster feedback.

### Known Pre-existing Test Failures
Some tests (Container, Validator, Type) have pre-existing failures unrelated to the environment. Database tests pass fully when MariaDB is running and the test DB/user/table are set up.
