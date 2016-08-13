//быстрая  регистрация
$(".qwik-link").toggle(function(){
	$(".qwik-form-wrp").show();
    $("#fast-login :input").attr("disabled", true);
	return false;
}, function() {
    $(".qwik-form-wrp").hide();
    $("#fast-login :input").attr("disabled", false);
	return false;
});

// Быстрая авторизация
$("#fastauth").click(function(){
    $.ajax({
		type:"POST",
		url:"/user/user/quickLogin",
		data:$(".login-field").serialize(),
		"beforeSend": function() {
			$(".errorMessage", "#fast-login").html("");
			$(".errorMessage", "#fast-login").hide();
		},
		"success":function(data) 
        {
            var response = jQuery.parseJSON(data);
            
            if (response.status == "ok") {
                alert("Вы успешно авторизовались и теперь можете создать лот");
                $(".auth_fast").remove();
                
                $(".prod_contact").html("<input value=\"" + response.email + "\" type=\"hidden\"><input value=\"" + response.telephone + "\" type=\"hidden\">");
                
            }
            else 
            {
				$.each(response.errors, function(key, value) {
					$("div #LoginForm_"+key+"_em_").show();
					$("div #LoginForm_"+key+"_em_").html(value);
				});
            }
		}
	});
});

// Быстрое восстановление пароля
$("#fastrecovery").click(function(){
    $.ajax({
		type:"POST",
		url:"/user/user/quickRecovery",
		data:$(".recovery-field").serialize(),
		"beforeSend": function() {
			$(".errorMessage", "#fast-recovery").html("");
			$(".errorMessage", "#fast-recovery").hide();
		},
		"success":function(data) 
        {
            var response = jQuery.parseJSON(data);
            
            if (response.status == "ok") {
                alert("Новый пароль доступа выслан на указанный электронный адрес");
                $("#fast-recovery").hide();
                $("#fast-login").show();
            }
            else 
            {
				$.each(response.errors, function(key, value) {
					$("div #RecoveryForm_"+key+"_em_").show();
					$("div #RecoveryForm_"+key+"_em_").html(value);
				});
            }
		}
	});
});

// Скрываем предложенные поиском категории и вновь выводим каталог категорий
$("#show_original_cat").on("click", function(){
    
    var id_cat = $("#hide_category_id").val();
    
    content = $("#content-options");
    
    if (id_cat > 0) 
    {
        $.ajax({
            url: "/creator/getOptions",
            type: "GET",
            dataType: "json",
            data: {
                "cat_id": id_cat
            },
            success: function(data) {
                if (data.isOptions) {
                    content.html(data.options).show();
                    $("#content-options-block").show();
                    $("#content-options").css({"background-color":"#f1f1f1", "padding":"0 5px"});
                } else {
                    content.empty().html("<p style=\"padding-top: 10px;\" class=\"ntf_not_cat\">Для этой категории не определены параметры, продолжайте заполнение данных.</p>").show();
                    $("#content-options-block").show();
                    $("#content-options").css({"background-color":"white", "padding":"0px"});
                }
            }
        });
    }
    else
    {
        content.empty().html("<p style=\"padding-top: 10px;\">Выберите категорию, чтобы указать параметры ↑</p>");
        $("#content-options").css({"background-color":"white", "padding":"0px"});
    }
    
    $("#info_get_cat").hide();
    $(".get_cats_hide").show();
});