<?php

class common {
		
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
			$query = "INSERT INTO menu(`name`, `image`, `clients_logo`, `order`, `parent_id`, `page_id`, `menu_category_id`) 
					  VALUES('{$params['menu_name']}', '{$params['menu_image']}', '{$params['menu_clients_logo']}', {$params['menu_order']}, {$params['menu_parent_id']}, {$params['menu_page_id']}, {$params['menu_category_id']})";
		else
			$query = "UPDATE menu
					  SET `name`='{$params['menu_name']}',
						`image`='{$params['menu_image']}',
						`clients_logo`='{$params['menu_clients_logo']}',
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
		$query = "SELECT menu.*, page.url, page.title page_title
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
	
	##### Visibility #####
	public function saveVisibility($visibility) {
		global $con;
						
		$query = "DELETE FROM visibility_setup";
		$res = mysql_query($query, $con);
		
		foreach($visibility as $item_type=>$items) {
			foreach($items as $item) {
				list($item_id, $menu_id) = explode('_', $item);
				$query = "INSERT INTO visibility_setup(menu_id, item_id, item_type) VALUES({$menu_id}, {$item_id}, '{$item_type}')";				
				$res = mysql_query($query, $con);
				
				//link same item_id to submenues
				$query = "SELECT id FROM menu WHERE parent_id={$menu_id}";
				$sub_res = mysql_query($query, $con);				
				while($sub_row = mysql_fetch_object($sub_res))
				{
					$query = "INSERT INTO visibility_setup(menu_id, item_id, item_type) VALUES({$sub_row->id}, {$item_id}, '{$item_type}')";				
					$res = mysql_query($query, $con);					
				}
			}
		}				
		return true;
	}
	
	public function getVisibilities() {
		global $con;		
		$query = "SELECT * FROM visibility_setup";
		$res = mysql_query($query, $con);
		$rows = array();
		while($row = mysql_fetch_object($res))
		{
			$rows[$row->item_type][$row->item_id.'_'.$row->menu_id] = $row;
		}
		return $rows;
	}
	
	public function isVisible($menu_id, $item_id, $item_type) {
		global $con;		
		$query = "SELECT id FROM visibility_setup WHERE menu_id={$menu_id} AND item_id={$item_id} AND item_type='{$item_type}'";
		$res = mysql_query($query, $con);
		return mysql_num_rows($res)>0 ? true : false;
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
		$categories = $clients = array();
		if ($handle = opendir(realpath('../images/categories'))) {
			while (false !== ($entry = readdir($handle))) {
				if(!in_array($entry, array('.', '..'))) $categories[] = $entry;
			}
			closedir($handle);
		}
		if ($handle = opendir(realpath('../images/clients'))) {
			while (false !== ($entry = readdir($handle))) {
				if(!in_array($entry, array('.', '..'))) $clients[] = $entry;
			}
			closedir($handle);
		}
		return array('categories' => $categories, 'clients' => $clients );
	}
	
	public function convertString($string)	{
		$string = preg_replace('/%u([0-9A-F]+)/', '&#x$1;', $string);
		return rawurldecode(html_entity_decode($string, ENT_COMPAT, 'UTF-8'));
	}
	
	public function convertDate($date)	{
		$month_translations = array('January' => 'января', 'February' => 'февраля', 'March' => 'марта', 'April' => 'апреля', 
									'May' => 'мая', 'June' => 'июня', 'July' => 'июля', 'August' => 'августа', 
									'September' => 'сентября', 'October' => 'октября', 'November' => 'ноября', 'December' => 'декабря'	);
		$result = date("d F Y", strtotime($date));
		$month = date("F", strtotime($date));
		$result = preg_replace('/([a-zA-Z]+)/', $month_translations[$month], $result);
		return $result;
	}

}
?>