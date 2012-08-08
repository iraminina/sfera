<?php

error_reporting(1);

require_once 'conf.php';
$config = $objCommon->getSettings();

$page_name = (isset($_REQUEST['action']) && strlen($_REQUEST['action'])>0) ? trim($_REQUEST['action']) : 'home';
$page = $objPage->getPageByURL($page_name);

switch($page_name) {
	case str_replace('/','',$config['news_all_url']):
		$content = '[ALL_NEWS]';
		break;
	default:
		$content = $page ? $objCommon->convertString($page->content) : file_get_contents('html/404.phtml');
		break;
}
if($content == '') $content = '&nbsp;';

$title = $page ? $objCommon->convertString($page->title) : $config['general_title'];
$keywords = $page ? $objCommon->convertString($page->meta_keywords) : '';
$description = $page ? $objCommon->convertString($page->meta_description) : '';
$menu = $objCommon->getMenu();

$current_menu_id = ($page->menu_id && $page->menu_category_id && in_array($page->menu_category_id, array(1,2))) ? intval($page->menu_id) : -1;
$news = $objPage->getNews(intval($config['news_count']), $current_menu_id);
$articles = $objPage->getArticles(intval($config['articles_count']), $current_menu_id);

include 'html/index.phtml';
?>
