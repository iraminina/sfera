<?php

define('SITE_TITLE', 'Сфера');

define('DB_NAME', 'sfera');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost'); 

define('SITE_URL', 'http://test.local');

global $con;

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(DB_NAME);
mysql_set_charset('cp1251');

function __autoload($className) {
	$filename = $_SERVER['DOCUMENT_ROOT'] . '/admin/lib/class.' . $className . ".php";
	if (is_readable($filename)) {
		require_once $filename;
	}
}

$objCommon = new common();
$objPage = new page();

global $objCommon, $objPage;
?>