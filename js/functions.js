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