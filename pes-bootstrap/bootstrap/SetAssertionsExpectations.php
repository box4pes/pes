<?php

######### ASSERTIONS AND EXPECTATIONS ########################
/*
 * Option 	INI Setting 	Default value 	Description
 * ASSERT_ACTIVE 	assert.active 	1 	enable assert() evaluation
 * ASSERT_WARNING 	assert.warning 	1 	issue a PHP warning for each failed assertion
 * ASSERT_BAIL 	assert.bail 	0 	terminate execution on failed assertions
 * ASSERT_QUIET_EVAL 	assert.quiet_eval 	0 	disable error_reporting during assertion expression evaluation
 * ASSERT_CALLBACK 	assert.callback 	(NULL) 	Callback to call on failed assertions
 */

/**
 * Directive 	Default value 	Possible values
 * zend.assertions 	1
 *     1: generate and execute code (development mode)
 *     0: generate code but jump around it at runtime
 *     -1: do not generate code (production mode)
 * assert.exception 	0
 *     1: throw when the assertion fails, either by throwing the object provided as the exception or by throwing a new AssertionError object if exception wasn't provided
 *     0: use or generate a Throwable as described above, but only generate a warning based on that object rather than throwing it (compatible with PHP 5 behaviour)
 */


if (PES_PRODUCTION) {
    //PRODUCTION:
    ini_set('zend.assertions', 0);
} elseif (PES_DEVELOPMENT) {
    //DEVELOPMENT:
    // generuje assertion kód, vykonává jej a vždy vyhazuje AssertionError (Throwable), tak pokud není odchycená způsobí Fatal error
    ini_set('zend.assertions', 1);
    ini_set('assert.exception', 1);
} else {
    //DEFAULT:
    // generuje assertion kód, ale vyhazuje jen E_WARNING - další chování je pak dáno případným error handlerem a nastavením úrovně chyb
    ini_set('zend.assertions', 1);
    ini_set('assert.exception', 0);
}

