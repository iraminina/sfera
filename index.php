<?php

require_once 'conf.php';
require_once 'admin/lib/class.common.php';

global $con, $objCommon;
$objCommon = new common();
$page_name = (isset($_REQUEST['action']) && strlen($_REQUEST['action'])>0) ? trim($_REQUEST['action']) : 'home';
$page = $objCommon->getPageByURL($page_name);

$content = $page ? $objCommon->convertString($page->content) : file_get_contents('html/404.phtml');
if($content == '') $content = '&nbsp;';

$config = $objCommon->getSettings();
$title = $page ? $objCommon->convertString($page->title) : $config['general_title'];
$keywords = $page ? $objCommon->convertString($page->meta_keywords) : '';
$description = $page ? $objCommon->convertString($page->meta_description) : '';
$menu = $objCommon->getMenu();

include 'html/index.phtml';
?>
