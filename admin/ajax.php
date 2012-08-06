<?php

require_once '../conf.php';
require_once 'lib/class.common.php';

global $con;
$objCommon = new common();

switch($_REQUEST['action']) {
	case 'get_data':
		$pages = $objCommon->getPages();
		$emails = $objCommon->getEmails();
		$menu = $objCommon->getMenu(intval($_REQUEST['menu_category']));
		
		$result = array('pages' => $pages, 'emails' => $emails, 'menu' => $menu);
		echo json_encode($result);
	break;
	
    case 'save_page':
        $data = array(	'page_content' => $_REQUEST['page_content'],
						'page_title' => $_REQUEST['page_title'],
						'page_id' => $_REQUEST['page_id'],
						'page_description' => $_REQUEST['page_description'],
						'page_keywords' => $_REQUEST['page_keywords'],
						'page_url' => $_REQUEST['page_url']);
		$result = $objCommon->savePage($data);		
		echo json_encode($result);
    break;		
			
	case 'delete_page':
		$objCommon->deletePage($_REQUEST['page_id']);
		echo json_encode(true);		
	break;
	
	case 'save_email':		
		if(intval($_REQUEST['unsubscribe'])==1) {
			$objCommon->softDeleteEmail($_REQUEST['email']);
			echo json_encode(array('result' => true));		
		}
		else {
			$result = $objCommon->saveEmail($_REQUEST['email'], $_REQUEST['description'], intval($_REQUEST['email_id']));
			echo json_encode($result);
		}
	
	break;
			
	case 'delete_email':
		$objCommon->deleteEmail($_REQUEST['email_id']);
		echo json_encode(true);		
	break;
	
	case 'save_menu':	
        $data = array(	'menu_name' => $_REQUEST['menu_name'],
						'menu_image' => $_REQUEST['menu_image'],
						'menu_order' => $_REQUEST['menu_order'],
						'menu_parent_id' => $_REQUEST['menu_parent_id'],
						'menu_page_id' => $_REQUEST['menu_page_id'],
						'menu_category_id' => $_REQUEST['menu_category_id'],
						'menu_id' => $_REQUEST['menu_id']);						 
		$result = $objCommon->saveMenu($data);		
		echo json_encode($result);
    break;
		
	case 'delete_menu':
		$objCommon->deleteMenu($_REQUEST['menu_id']);
		echo json_encode(true);		
	break;
	
	case 'get_categories_images':
		$files = $objCommon->getCategoriesImages();
		echo json_encode($files);		
	break;
	
	case 'save_settings':
		$objCommon->saveSettings(json_decode($_REQUEST['settings']));
		echo json_encode(true);
	break;
}

exit;
?>