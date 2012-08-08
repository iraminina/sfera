$(document).ready(function() {
	$.sfera.loadContent();
	$.sfera.init();
});

(function($) {
    /************************ Public functions - begin ************************/
	$.sfera = {
		loadContent: function() {						
			$("ul.left_menu_items li").hover(
				function(){
					$(this).addClass("active");
				},
				function(){
					$(this).removeClass("active");
				}
			);							
		},
		
		init: function() {
			__initDOM();
			$("div#subscribe").hide();
			var current_page = '';
			$("div.menu_item").each(function(key, value) {				
				current_page = $(this).find("a").attr("href").replace('/','');				
				if(current_page.length>0 && $.inArray(current_page, location.href.split('/'))>-1) {					
					if(key==0) { //first item
						$($("div.menu_item")[key]).addClass("menu_item_active_first");
					} else if(key==1) { //second item
						$($("div.menu_item")[key-1]).addClass("menu_item_first_active_prev");
						$($("div.menu_item")[key]).addClass("menu_item_active");
					} else if(key==$("div.menu_item").size()-1) { //last item
						$($("div.menu_item")[key-1]).addClass("menu_item_active_prev");
						$($("div.menu_item")[key]).addClass("menu_item_last_active");
					} else { //all other
						$($("div.menu_item")[key-1]).addClass("menu_item_active_prev");
						$($("div.menu_item")[key]).addClass("menu_item_active");
					}										
				}				
			});
			$("ul.left_menu_items li").each(function(key, value) {				
				current_page = $(this).find("a").attr("href").replace('/','');				
				if(current_page.length>0 && $.inArray(current_page, location.href.split('/'))>-1) {					
					$(this).addClass("current");					
				}				
			});

			(function($){	
			  $(function(){
				$('#all-news-slider').bxSlider({
				  mode: 'vertical',
				  pager: true,	  
				  pagerLocation: 'top',
				  displaySlideQty: 3,
				  moveSlideQty: 3,
				  pagerSelector: 'div.all-news-pager-block',
				  infiniteLoop: false,
				  controls: false
				});
			  });	
			}(jQuery))
		}
	};   
    /**************************** Private functions ****************************/ 
	function __initDOM() {
		$('input[placeholder], textarea[placeholder]').placeholder();
	
		$("#news").delegate("#subscribe_btn", "click", function(){
			$("p#email_message, #email_message_hr").remove();
			$("#email").val('');
			$("div#subscribe").show("slow");
		});
		$("div#subscribe").delegate("#unsubscribe_btn", "click", function(){
			$("p#email_message, #email_message_hr").remove();
			var email = $("#email").val();
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			
			if (!filter.test(email)) {
				$("#email").parent().prepend('<p id="email_message" class="red">Введите правильный e-mail</p>');				
			}
			else {
				$.ajax({
					url: "admin/ajax.php?action=save_email",
					type: "POST",
					dataType: "json",
					data: {	email: email, unsubscribe: ($("input#unsubscribe").is(":checked") ? 1 : 0), description: '' }				  
				})
				.done(function ( data ) {
					if(data.result) {
						if($("input#unsubscribe").is(":checked")) {
							$("#news").append('<p id="email_message" class="green">Вы удалены из подписки</p><div class="hr" id="email_message_hr"></div>');				
						}
						else {
							$("#news").append('<p id="email_message" class="green">Вы добавлены в подписку</p><div class="hr" id="email_message_hr"></div>');				
						}						
						$("div#subscribe").hide("slow");
					}
					else {
						if(data.error == 'NOT_UNIQUE_EMAIL')
							$("#email").parent().prepend('<p id="email_message" class="red">Данный e-mail уже добавлен</p>');				
					}
				});				
			}			
		});
	}
    
 })(jQuery);