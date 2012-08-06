<?php

class common {

    ##### Pages #####	
    public function savePage($params) {
		global $con;
		if(strlen($params['page_title'])==0) return array('result' => false, 'error' => 'EMPTY_PAGE_TITLE');
		if(strlen($params['page_url'])==0) return array('result' => false, 'error' => 'EMPTY_PAGE_URL');
		
		$query = "SELECT id FROM page WHERE url='{$params['page_url']}' AND id!=".intval($params['page_id']);
		$res = mysql_query($query, $con);		
		if(mysql_num_rows($res)>0)  return array('result' => false, 'error' => 'NOT_UNIQUE_URL');
		
		$created_date = date("Y-m-d", strtotime($this->convertString($params['created_date'])));
		
		if(intval($params['page_id'])==0)
			$query = "INSERT INTO page(title, content, page_description, meta_keywords, meta_description, url, page_category_id, created_date) 
					  VALUES('{$params['page_title']}', '{$params['page_content']}', '{$params['page_description']}', '{$params['page_meta_keywords']}', '{$params['page_meta_description']}', '{$params['page_url']}', {$params['page_category_id']}, '{$created_date}')";
		else
			$query = "UPDATE page
					  SET title='{$params['page_title']}',
						content='{$params['page_content']}', 
						page_description='{$params['page_description']}',
						meta_keywords='{$params['page_meta_keywords']}',
						meta_description='{$params['page_meta_description']}',
						url='{$params['page_url']}',
						page_category_id={$params['page_category_id']},
						created_date='{$created_date}'
					  WHERE id=".intval($params['page_id']);
		$res = mysql_query($query, $con);
		return array('result' => true, 'error' => '');
    } 

	public function getPageByURL($page_url) {
		global $con;
		$query = "SELECT * FROM page WHERE url='{$page_url}'";
		$res = mysql_query($query, $con);
		$row = mysql_fetch_object($res);
		return $row;
	}
	
	public function getPages() {
		global $con;
		$query = "SELECT * FROM page ORDER BY title";
		$res = mysql_query($query, $con);
		$rows = array();
		while($row = mysql_fetch_object($res)) $rows[$row->id] = $row;
		return $rows;
	}
	
	public function deletePage($page_id) {
		global $con;
		$query = "DELETE FROM page WHERE id=".intval($page_id);
		$res = mysql_query($query, $con);
		return $res ? true: false;
	}
		
	##### E-mails #####	
	public function saveEmail($email, $description='', $id=0) {
		global $con;
		if(strlen($email)==0) return array('result' => false, 'error' => 'EMPTY_EMAIL');		
		
		$query = "SELECT id FROM emails WHERE email='{$email}' AND id!={$id}";
		$res = mysql_query($query, $con);		
		if(mysql_num_rows($res)>0)  return array('result' => false, 'error' => 'NOT_UNIQUE_EMAIL');
				
		if($id > 0) {
			$query = "UPDATE emails
					  SET email='{$email}',
						description='{$description}'
					  WHERE id={$id}";		
		}
		else {
			$query = "INSERT INTO emails(email, description) VALUES('{$email}', '{$description}')";		
		}		
		$res = mysql_query($query, $con);
		return array('result' => true, 'error' => '');
    }
	
	public function getEmails() {
		global $con;
		$query = "SELECT * FROM emails ORDER BY is_deleted, email";
		$res = mysql_query($query, $con);
		$rows = array();
		while($row = mysql_fetch_object($res)) $rows[$row->id] = $row;
		return $rows;
	}
	
	public function softDeleteEmail($email) {
		global $con;
		$query = "UPDATE emails SET is_deleted=1 WHERE email='{$email}'";
		$res = mysql_query($query, $con);
		return $res ? true: false;
	}
	
	public function deleteEmail($email_id) {
		global $con;
		$query = "DELETE FROM emails WHERE id='{$email_id}'";
		$res = mysql_query($query, $con);
		return $res ? true: false;
	}
	
	##### Menu #####
	public function saveMenu($params) {
		global $con;
		if(strlen($params['menu_name'])==0) return array('result' => false, 'error' => 'EMPTY_MENU_NAME');
				
		$query = "SELECT id FROM menu WHERE name='{$params['menu_name']}' AND id!=".intval($params['menu_id']);
		$res = mysql_query($query, $con);		
		if(mysql_num_rows($res)>0)  return array('result' => false, 'error' => 'NOT_UNIQUE_MENU');
		
		if(intval($params['menu_id'])==0)
			$query = "INSERT INTO menu(`name`, `image`, `order`, `parent_id`, `page_id`, `menu_category_id`) 
					  VALUES('{$params['menu_name']}', '{$params['menu_image']}', {$params['menu_order']}, {$params['menu_parent_id']}, {$params['menu_page_id']}, {$params['menu_category_id']})";
		else
			$query = "UPDATE menu
					  SET `name`='{$params['menu_name']}',
						`image`='{$params['menu_image']}',
						`order`={$params['menu_order']}, 
						`parent_id`={$params['menu_parent_id']},
						`page_id`={$params['menu_page_id']},
						`menu_category_id`={$params['menu_category_id']}
					  WHERE id=".intval($params['menu_id']);		
		$res = mysql_query($query, $con);
		return array('result' => true, 'error' => '');
    } 
	
	public function getMenu($category=-1) {
		global $con;
		$where = in_array($category, array(0,1,2)) ? ' AND menu_category_id='.$category : '';
		$query = "SELECT menu.*, page.url
				  FROM menu LEFT JOIN page ON page.id=menu.page_id
				  WHERE parent_id=0 {$where} 
				  ORDER BY menu_category_id ASC, `order`, name";
		$res = mysql_query($query, $con);
		$rows = array();
		while($row = mysql_fetch_object($res))
		{
			$query = "SELECT * FROM menu WHERE parent_id={$row->id} {$where} ORDER BY menu_category_id ASC, `order`, name";
			$sub_res = mysql_query($query, $con);
			$sub_rows = array();
			while($sub_row = mysql_fetch_object($sub_res))
			{
				$sub_rows[$sub_row->id] = $sub_row;
			}
			$rows[$row->id] = array( 'data' => $row, 'children' => $sub_rows );
		}
		return $rows;
	}
	
	public function deleteMenu($menu_id) {
		global $con;
		$query = "DELETE FROM menu WHERE id=".intval($menu_id);
		$res = mysql_query($query, $con);
		return $res ? true: false;
	}
	
	##### Settings #####
	public function saveSettings($settings) {
		global $con;
						
		$query = "SELECT DISTINCT name FROM settings";
		$res = mysql_query($query, $con);
		$existed_fields = array();
		while($row = mysql_fetch_object($res)) {
			$existed_fields[] = $row->name;
		}
		
		$form_fields = array_keys(get_object_vars($settings));
		
		if(count($existed_fields)<16) {
			foreach($form_fields as $form_field) {
				if(!in_array($form_field, $existed_fields)) {
					$query = "INSERT INTO settings(name, value) VALUES('{$form_field}', '')";
					$res = mysql_query($query, $con);
				}
			}
		}
		
		foreach($settings as $name=>$value) {
			$query = "UPDATE settings SET value='{$value}' WHERE name='{$name}'";
			$res = mysql_query($query, $con);			
		}
		return true;
	}
	
	public function getSettings() {
		global $con;
						
		$query = "SELECT * FROM settings";
		$res = mysql_query($query, $con);
		$fields = array();
		while($row = mysql_fetch_object($res)) {
			$fields[$row->name] = $this->convertString($row->value);
		}
		return $fields;
	}
	
	##### Other #####
	public function getCategoriesImages() {
		$files = array();
		if ($handle = opendir(realpath('../images/categories'))) {
			while (false !== ($entry = readdir($handle))) {
				if(!in_array($entry, array('.', '..'))) $files[] = $entry;
			}
			closedir($handle);
		}
		return $files;
	}
	
	public function convertString($string)	{
		$string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $string);
		return rawurldecode(html_entity_decode($string, ENT_COMPAT, 'UTF-8'));
	}

}
?>