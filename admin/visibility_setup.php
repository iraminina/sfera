<?php

require_once '../conf.php';
require_once 'lib/class.common.php';

$objCommon = new common();

global $con, $objCommon;

$config = $objCommon->getSettings();
$menu = $objCommon->getMenu();
$news = $objCommon->getNews();
$articles = $objCommon->getArticles();

include 'html/news_articles_visibility.phtml';

?>
