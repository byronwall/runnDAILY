<?php
DEFINE("TEST_ROOT", dirname(__FILE__));
DEFINE("TEST_DATA", TEST_ROOT."/data");
DEFINE("TEST_LOC", TEST_ROOT."/class");
DEFINE("SYSTEM_ROOT", dirname(__FILE__)."/../source/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");
DEFINE("LIB_ROOT", dirname(__FILE__)."/../lib");

require_once TEST_ROOT.'/runnDAILY_Tests.php';
require_once SYSTEM_ROOT."/config.php";

// prepare the DB to be tested
// TODO: use a test DB instead of the production DB
Database::getDB()->autocommit(false);

$test_suite = new runnDAILY_Suite();
$test_suite->run(new HtmlReporter());

Database::getDB()->rollback();
?>