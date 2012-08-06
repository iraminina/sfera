<?php

require_once '../conf.php';
require_once '../admin/lib/class.common.php';

$objCommon = new common();
$config = $objCommon->getSettings();

global $con, $config;

include 'html/index.phtml';

?>
