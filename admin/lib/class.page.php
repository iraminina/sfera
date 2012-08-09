<?php

class page {
    
    public function savePage($params) {
		global $con, $objCommon;
		if(strlen($params['page_title'])==0) return array('result' => false, 'error' => 'EMPTY_PAGE_TITLE');
		if(strlen($params['page_url'])==0) return array('result' => false, 'error' => 'EMPTY_PAGE_URL');
		
		$query = "SELECT id FROM page WHERE url='{$params['page_url']}' AND id!=".intval($params['page_id']);
		$res = mysql_query($query, $con);		
		if(mysql_num_rows($res)>0)  return array('result' => false, 'error' => 'NOT_UNIQUE_URL');
		
		$created_date = date("Y-m-d", strtotime($objCommon->convertString($params['created_date'])));
		
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
		$query = "SELECT page.*, menu.id menu_id, menu.menu_category_id, menu.clients_logo
				  FROM page LEFT JOIN menu ON menu.page_id=page.id 
				  WHERE url='{$page_url}'";
		$res = mysql_query($query, $con);
		$row = mysql_fetch_object($res);
		return $row;
	}
	
	public function getPages($category=-1) {
		global $con;
		$where = in_array($category, array(0,1,2,3,4)) ? 'WHERE page_category_id='.$category : '';
		$query = "SELECT * FROM page {$where} ORDER BY title";		
		$res = mysql_query($query, $con);
		$rows = array();
		while($row = mysql_fetch_object($res)) $rows[$row->id] = $row;
		return $rows;
	}
	
	public function getNews($max_count=-1, $menu_id=-1) {
		global $con;		
		$limit = $max_count!=-1 ? "LIMIT 0,{$max_count}" : "";
		$from = $menu_id!=-1 ? "INNER JOIN visibility_setup vs ON (vs.item_id=p.id AND vs.item_type='news' AND vs.menu_id={$menu_id})" : "";
		
		$query = "SELECT * FROM page p {$from} WHERE p.page_category_id=3 ORDER BY p.created_date DESC, p.title {$limit}";
		$res = mysql_query($query, $con);
		$rows = array();
		while($row = mysql_fetch_object($res)) $rows[$row->id] = $row;
		return $rows;
	}
	
	public function getArticles($max_count=-1, $menu_id=-1) {
		global $con;
		$limit = $max_count!=-1 ? "LIMIT 0,{$max_count}" : "";
		$from = $menu_id!=-1 ? "INNER JOIN visibility_setup vs ON ( vs.item_id=page.id AND vs.item_type='articles' AND vs.menu_id={$menu_id})" : "";
		
		$query = "SELECT * FROM page {$from} WHERE page.page_category_id=4 ORDER BY page.created_date DESC, page.title {$limit}";		
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
}
?>