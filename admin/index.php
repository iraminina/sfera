<?php

require_once '../conf.php';
require_once 'lib/class.common.php';

$objCommon = new common();
$config = $objCommon->getSettings();

global $con, $config, $objCommon;

include 'html/index.phtml';

?>
