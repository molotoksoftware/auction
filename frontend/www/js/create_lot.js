
$(".qwik-link").toggle(function(){
	$(".qwik-form-wrp").show();
    $("#fast-login :input").attr("disabled", true);
	return false;
}, function() {
    $(".qwik-form-wrp").hide();
    $("#fast-login :input").attr("disabled", false);
	return false;
});

