﻿$.sfera_admin = {
	pagesData: null,
	emailsData: null,
	menuData: null,
	categoriesImagesData: null,
	
	init: function() {
		$( "#tabs" ).tabs();
		this.initPagesData();
		this.initEmailsData();
		this.initMenuData();
		this.initCategoriesImagesData();
		this.initDOM();
	},
	
	initCategoriesImagesData: function() {
		$.getJSON('ajax.php?action=get_categories_images', function(data) {
			$.sfera_admin.categoriesImagesData = data;			
		});
	},
	
	initPagesData: function() {
		$.getJSON('ajax.php?action=get_pages', function(data) {
			$.sfera_admin.pagesData = data;
			$.sfera_admin.drawPages();			
		});		
	},
	
	initEmailsData: function() {		
		$.getJSON('ajax.php?action=get_emails', function(data) {
			$.sfera_admin.emailsData = data;
			$.sfera_admin.drawEmails();			
		});
	},
	
	initMenuData: function() {		
		$.getJSON('ajax.php?action=get_menu', function(data) {
			$.sfera_admin.menuData = data;
			$.sfera_admin.drawMenu();			
		});
	},
	
	setupPageDialodWindow: function() {
		$( "#edit_page" ).dialog({
			autoOpen: false,
            height: 600,
			width: 1000,
			modal: true,
            title: "Редактировать страницу",			
            buttons: {
				"Сохранить": function() {
					$.ajax({
					  url: "ajax.php?action=save_page",
					  type: "POST",
					  dataType: "json",
					  data: {	page_id: escape($("#page_id").val()),
								page_title: escape($("#page_title").val()),
								page_content: escape(CKEDITOR.instances['page_content'].getData()),		
								page_url: escape($("#page_url").val()),								
								page_description: escape($("#page_description").val()),
								page_keywords: escape($("#page_keywords").val()) }				  
					})
					.done(function ( data ) {
						$("p#page_success, p#page_error").html('');
						
						if(data.result) {
							$.sfera_admin.initPagesData();		
							$("p#page_success").html("Данные сохранены.");							
						}
						else {							
							switch(data.error) {
								case 'EMPTY_PAGE_TITLE':
									$("p#page_error").html("Введите Заголовок"); break;
									
								case 'EMPTY_PAGE_URL':
									$("p#page_error").html("Введите URL"); break;
									
								case 'NOT_UNIQUE_URL':
									$("p#page_error").html("Страница с таким URL уже существует."); break;							
									
								default:
									$("p#page_error").html("Внимание! Произошла ошибка, данные не сохранены."); break;
							}
						}
					});					
                },
                "Отмена": function() {                    
                    $( this ).dialog( "close" );
                }
            },
            open: function() {                
            },
            close: function() {}
		});
	},
	
	setupEmailDialodWindow: function() {
		$( "#edit_email" ).dialog({
			autoOpen: false,
            height: 190,
			width: 650,
			modal: true,
            title: "Редактировать подписку",			
            buttons: {
				"Сохранить": function() {
					$.ajax({
					  url: "ajax.php?action=save_email",
					  type: "POST",
					  dataType: "json",
					  data: {	email_id: escape($("#email_id").val()),
								email: escape($("#email").val()),
								unsubscribe: 0,
								description: escape($("#email_description").val())								
							}
					})
					.done(function ( data ) {
						$("p#email_success, p#email_error").html('');
						
						if(data.result) {
							$("p#email_success").html("Данные сохранены.");
							$.sfera_admin.initEmailsData();	
						}
						else {							
							switch(data.error) {
								case 'NOT_UNIQUE_EMAIL':
									$("p#email_error").html("Подписка с таким E-amil уже существует."); break;							
									
								default:
									$("p#email_error").html("Внимание! Произошла ошибка, данные не сохранены."); break;
							}
						}
					});					
                },
				"Удалить": function() {
					if(confirm("Вы уверены, что хотите удалить подписчика?")) {
						var email_id = $("#email_id").val();
						$.ajax({
							url: "ajax.php?action=delete_email",
							type: "POST",
							dataType: "json",
							data: {	email_id: email_id }				  
						})
						.done(function ( data ) {														
							$.sfera_admin.initEmailsData();				
							$( "#edit_email" ).dialog( "close" );						
						});
					}                
                },
				
                "Отмена": function() {	
					$( this ).dialog( "close" );
                }
            },
            open: function() {                
            },
            close: function() {										
			}
		});
	},
	
	setupMenuDialodWindow: function() {
		$( "div#edit_menu" ).dialog({
			autoOpen: false,
            height: 260,
			width: 480,
			modal: true,
            title: "Редактировать пункт меню",			
            buttons: {
				"Сохранить": function() {
					$.ajax({
					  url: "ajax.php?action=save_menu",
					  type: "POST",
					  dataType: "json",
					  data: {	menu_id: escape($("#menu_id").val()),
								menu_name: escape($("#menu_name").val()),
								menu_image: escape($("#image").val()),
								menu_order: escape($("#menu_order").val()),
								menu_parent_id: escape($("#menu_parent_id").val()),
								menu_page_id: escape($("#menu_page_id").val()),
								menu_category_id: escape($("#menu_category_id").val())
							}
					})
					.done(function ( data ) {
						$("p#menu_success, p#menu_error").html('');
						
						if(data.result) {
							$("p#menu_success").html("Данные сохранены.");
							$.sfera_admin.initMenuData();	
						}
						else {							
							switch(data.error) {
								case 'NOT_UNIQUE_MENU':
									$("p#menu_error").html("Пункт меню с таким названием уже существует."); break;							
									
								default:
									$("p#menu_error").html("Внимание! Произошла ошибка, данные не сохранены."); break;
							}
						}
					});					
                },
				"Удалить": function() {
					if(confirm("Вы уверены, что хотите удалить пункт меню?")) {
						var menu_id = $("#menu_id").val();
						$.ajax({
							url: "ajax.php?action=delete_menu",
							type: "POST",
							dataType: "json",
							data: {	menu_id: menu_id }				  
						})
						.done(function ( data ) {														
							$.sfera_admin.initMenuData();				
							$( "div#edit_menu" ).dialog( "close" );						
						});
					}                
                },
				
                "Отмена": function() {	
					$( this ).dialog( "close" );
                }
            },
            open: function() {                
            },
            close: function() {										
			}
		});
	},
	
	initDOM: function() {
		this.setupPageDialodWindow();
		this.setupEmailDialodWindow();
		this.setupMenuDialodWindow();
		
			
		$("#get_subscribers").live('click', function() {
			var result = [];
			$("input.emails_chb").each(function() {
				if($(this).is(":checked"))
					result.push($("tr#tr_email_"+$(this).val()+" td.td_email").html());
			});
			if(result.length > 0 ) $("p#subscribers_list").html(result.join(', ')).show('slow');
			else $("p#subscribers_list").html('').hide('slow');
		});
		
		$( "a.edit_email" ).live('click', function() {			
			var email_id = $(this).attr('rel');
			$("#email_id").val(unescape($.sfera_admin.emailsData[email_id].id));
			$("#email").val(unescape($.sfera_admin.emailsData[email_id].email));
			$("#email_description").val(unescape($.sfera_admin.emailsData[email_id].description));			
			$( "#edit_email" ).dialog( "open" );
			return false;
		});
		
		$( "a.edit_page" ).live('click', function() {
			var page_id = $(this).attr('rel');
			if(page_id==0) return;
			$("p#page_success, p#page_error").html('');			
			$("#page_id").val(unescape($.sfera_admin.pagesData[page_id].id));
			$("#page_title").val(unescape($.sfera_admin.pagesData[page_id].title));
			CKEDITOR.instances['page_content'].setData(unescape($.sfera_admin.pagesData[page_id].content));
			$("#page_url").val(unescape($.sfera_admin.pagesData[page_id].url));
			$("#page_description").val(unescape($.sfera_admin.pagesData[page_id].meta_description));
			$("#page_keywords").val(unescape($.sfera_admin.pagesData[page_id].meta_keywords));
			$( "#edit_page" ).dialog( "open" );
			return false;
		});	
		
		$( "a.delete_page" ).live('click', function() {
			if(confirm("Вы уверены, что хотите удалить страницу?")) {
				var page_id = $(this).attr('rel');
				$.ajax({
					url: "ajax.php?action=delete_page",
					type: "POST",
					dataType: "json",
					data: {	page_id: page_id }				  
				})
				.done(function ( data ) {
					location.reload();
				});
			}
			return false;
		});
		
		$( "#new_page" ).click(function() {
			$("p#page_success, p#page_error").html('');
			$("#page_id").val(0);
			$("#page_title, #page_url, #page_description, #page_keywords").val('');
			CKEDITOR.instances['page_content'].setData('');			
			$( "#edit_page" ).dialog( "open" );
			return false;
		});
		
		$( "#new_menu" ).click(function() {
			var pages_select = $.sfera_admin.getEditMenuPagesSelect();			
			var parents_select = $.sfera_admin.getEditMenuParentsSelect();
			var images_select = $.sfera_admin.getEditMenuImagesSelect();
			
			$("p#menu_success, p#menu_error").html('');			
			$("#menu_page_id").html(pages_select).val(0);			
			$("#menu_parent_id").html(parents_select).val(0);			
			$("#image").html(images_select).val('');
			$("#menu_id, #menu_order, #menu_category_id").val(0);
			$("#menu_name").val('');
			$("#edit_page_link").hide();			
			$( "div#edit_menu" ).dialog( "open" );
			return false;
		});
		
		$( "a.edit_menu" ).live('click', function() {
			$("p#menu_success, p#menu_error").html('');
			var menu_id = $(this).attr('rel');
			var pages_select = $.sfera_admin.getEditMenuPagesSelect();
			var parents_select = $.sfera_admin.getEditMenuParentsSelect();			
			var images_select = $.sfera_admin.getEditMenuImagesSelect();
			
			//find menu data
			var menu_data = null;
			$.each($.sfera_admin.menuData, function(key, item) {
				if(item.data.id == menu_id) {
					menu_data = item.data;
					return;
				}
				else {
					$.each(item.children, function(key, menu) {				
						if(menu.id == menu_id) {
							menu_data = menu;
							return;
						}
					});
				}			
			});			
			$("p#menu_success, p#menu_error").html('');			
			$("#menu_page_id").html(pages_select).val(unescape(menu_data.page_id));
			$("#menu_parent_id").html(parents_select).val(unescape(menu_data.parent_id));			
			$("#image").html(images_select).val(unescape(menu_data.image));
			$("#menu_id").val(unescape(menu_data.id));
			$("#menu_order").val(unescape(menu_data.order));
			$("#menu_category_id").val(unescape(menu_data.menu_category_id));		
			$("#menu_name").val(unescape(menu_data.name));
			$("#image").change();
			$("#edit_page_link").attr("rel", menu_data.page_id).show();			
			if(menu_data.page_id==0) $("#edit_page_link").hide();
			$( "div#edit_menu" ).dialog( "open" );
			return false;
		});	
		
		$("div#edit_menu").delegate("select#image", "change", function(){
			if($(this).val()=='') $("#image_preview img").attr('src', 'images/no_photo.png');
			else $("#image_preview img").attr('src', '../images/categories/'+$(this).val());			
		});
		
		$("div#edit_menu").delegate("select#menu_page_id", "change", function(){
			if($(this).val()==0) $("#edit_page_link").hide();
			else $("#edit_page_link").attr("rel", $(this).val()).show();	
		});			
	},
	
	getEditMenuPagesSelect: function() {
		var pages_select = '';
		pages_select += '<option value="0">Не задана</option>';
		$.each($.sfera_admin.pagesData, function(key, page) {
			pages_select += '<option value="' + page.id + '">' + page.id + ': ' + unescape(page.title) + '</option>';
		});
		return pages_select;
	},
	
	getEditMenuParentsSelect: function() {
		var parents_select = '';
		parents_select += '<option value="0">Нет</option>';
		$.each($.sfera_admin.menuData, function(key, menu) {
			parents_select += '<option value="' + menu.data.id + '">' + menu.data.id + ': ' + unescape(menu.data.name) + '</option>';
		});
		return parents_select;
	},
	
	getEditMenuImagesSelect: function() {	
		var images_select = '';
		images_select += '<option value="">Нет</option>';
		$.each($.sfera_admin.categoriesImagesData, function(key, image) {
			images_select += '<option value="' + image + '">' + image + '</option>';
		});
		return images_select;
	},
	
	drawPages: function() {
		var html = '';
		var count = 0;		
		$.each($.sfera_admin.pagesData, function(key, page) {
			html += '<tr>' +
						'<td>' +
							'<a href="#" class="blue edit_page" rel="' + unescape(page.id) + '">Редактировать</a>' +
							'&nbsp;|&nbsp;' +
							'<a href="#" class="red delete_page" rel="' + unescape(page.id) + '">Удалить</a>' +
						'</td>' + 
						'<td width="50" align="center">' + unescape(page.id) + '</td>' +
						'<td width="300">' + unescape(page.title) + '</td>' +
						'<td width="200"><a target="_blank" href="/' + unescape(page.url) + '">' + unescape(page.url) + '</a></td>' +
					'</tr>';
			count++;
		});
		html += '<tr><td colspan="4">Найдено страниц: <b>' + count + '</b></td></tr>';
		$("#pages_tbl").html(html);
	},
	
	drawEmails: function() {	
		var html = '';
		var count = 0;		
		$.each($.sfera_admin.emailsData, function(key, email) {
			html += '<tr id="tr_email_' + unescape(email.id) + '">' +
						'<td>' +
							'<a href="#" class="blue edit_email" rel="' + unescape(email.id) + '">Редактировать</a>' +							
						'</td>' + 
						'<td width="50" align="center"><input type="checkbox" name="emails[]" class="emails_chb" value="' + unescape(email.id) + '"/></td>' +
						'<td width="200" class="td_email ' + (email.is_deleted==1 ? 'red' : '')+ '">' + unescape(email.email) + '</td>' +
						'<td width="120" align="center">' + unescape(email.date) + '</td>' +
						'<td width="500">' + unescape(email.description) + '</td>' +
					'</tr>';
			count++;
		});
		html += '<tr><td colspan="5">Найдено адресов: <b>' + count + '</b></td></tr>';
		$("#emails_tbl").html(html);
	},
	
	drawMenu: function() {	
		var html = '';		
		var count = 0;		
		var category = '';
		$.each($.sfera_admin.menuData, function(key, item) {
			menu = item.data;
			switch(parseInt(menu.menu_category_id)) {
				case 1: category = 'Верхнее меню'; break;
				case 2: category = 'Меню слева'; break;
				default: category = 'Спрятано'; break;
			}
			html += '<tr id="tr_menu_' + unescape(menu.id) + '">' +
						'<td>' +
							'<a href="#" class="blue edit_menu" rel="' + unescape(menu.id) + '">Редактировать</a>' +							
						'</td>' + 
						'<td width="50" align="center">' + unescape(menu.id) + '</td>' +
						'<td width="200">' + unescape(menu.name) + '</td>' +
						'<td width="70" align="center">' + unescape(menu.order) + '</td>' +
						'<td width="200">' + (menu.parent_id==0 ? '' : unescape($.sfera_admin.menuData[menu.parent_id].data.name)) + '</td>' +
						'<td width="200">' + 
							(menu.page_id==0 ? '' : '<a target="_blank" href="/' + unescape($.sfera_admin.pagesData[menu.page_id].url) + '">' + unescape($.sfera_admin.pagesData[menu.page_id].title) + '</a>') +
						'</td>' +
						'<td width="100">' + category + '</td>' +
					'</tr>';
			count++;
			$.each(item.children, function(key, menu) {				
				switch(parseInt(menu.menu_category_id)) {
					case 1: category = 'Верхнее меню'; break;
					case 2: category = 'Меню слева'; break;
					default: category = 'Спрятано'; break;
				}
				html += '<tr class="sub_menu" id="tr_menu_' + unescape(menu.id) + '">' +
							'<td>' +
								'<a href="#" class="blue edit_menu" rel="' + unescape(menu.id) + '">Редактировать</a>' +							
							'</td>' + 
							'<td width="50" align="center">' + unescape(menu.id) + '</td>' +
							'<td width="200">' + unescape(menu.name) + '</td>' +
							'<td width="70" align="center">' + unescape(menu.order) + '</td>' +
							'<td width="200">' + (menu.parent_id==0 ? '' : unescape($.sfera_admin.menuData[menu.parent_id].data.name)) + '</td>' +
							'<td width="200">' + 
								(menu.page_id==0 ? '' : '<a target="_blank" href="/' + unescape($.sfera_admin.pagesData[menu.page_id].url) + '">' + unescape($.sfera_admin.pagesData[menu.page_id].title) + '</a>') +
							'</td>' +
							'<td width="100">' + category + '</td>' +
						'</tr>';
				count++;
			});			
		});
		html += '<tr><td colspan="7">Найдено элементов: <b>' + count + '</b></td></tr>';
		$("#menu_tbl").html(html);
	}
};

$(document).ready(function() {
	$.sfera_admin.init();
});    