var defaults = {
    csrfToken: null,
    csrfTokenName: null,
    dynamicCategoriesUrl: '',
    downloadOptionsUrl: '',
    cat_1: '',
    cat_2: '',
    cat_3: '',
    type: '',
    where_show: 0,
    getOptions: true
};

var Creator = function(options) {
    var opts = $.extend({}, defaults, options);
    var main = this;

    this.init = function() {
    },
            this.showErrors = function(errors) {
                alert(errors.message);
            },
            this.hideOptions = function() {
                content = $('#content-options');
                content.empty().html('Выберите категорию, чтобы указать параметры ↑');
                $('#content-options').css({'background-color':'white', 'padding':'0px'});
            },
            this.downloadOptions = function(id) {
                if (opts.getOptions == false) {
                    return true;
                }

                content = $('#content-options');
                $.ajax({
                    url: opts.downloadOptionsUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        'cat_id': id
                    },
                    success: function(data) {
                        
                        
                        if (data.isOptions) {
                            content.html(data.options).show();
                            $('#content-options-block').show();
                            $('#content-options').css({'background-color':'#f1f1f1', 'padding':'0 5px'});
                        } else {
                            content.empty().html('Для этой категории не определены параметры, продолжайте заполнение данных.').show();
                            $('#content-options-block').show();
                            $('#content-options').css({'background-color':'white', 'padding':'0px'});
                        }
                    },
                    beforeSend: function() {
                        main.hideOptions();
                    }
                });

            }


    //cat1
    $(opts.cat_1).change(function() {
        var data = {
            "cat_id": $(this).find("option:selected").val(),
            "level": 2,
            "where_show": opts.where_show
        };

        $.ajax({
            type: 'GET',
            url: opts.dynamicCategoriesUrl,
            data: data,
            dataType: 'json',
            beforeSend: function() {
	            $("#Cat2").parent('.cat-list-block').hide();
	            $("#Cat3").parent('.cat-list-block').hide();
	            $("#" + opts.type + "_category_id").val("");
                main.hideOptions();
            },
            success: function(data) {
                if (data.isSubCategories) {
                    $("#Cat2").html(data.options);
                    $("#Cat2").show();
                    $("#Cat2").attr("disabled", false);
                    
                    $("#hide_category_id").val("");

	                $("#Cat2").parent('.cat-list-block').show();
                } else {
                    var cat_id = $("#Cat1").find("option:selected").val();
                    
                    $("#hide_category_id").val(cat_id);
                    
                    $("#" + opts.type + "_category_id").val(cat_id);
                    main.downloadOptions(cat_id);
                }
            }
        });
    });

    //cat2
    $(opts.cat_2).change(function() {
        var data = {
            "cat_id": $(this).find("option:selected").val(),
            "level": 2,
            "where_show": opts.where_show
        };

        $.ajax({
            type: 'GET',
            url: opts.dynamicCategoriesUrl,
            data: data,
            dataType: 'json',
            beforeSend: function() {
	            $("#Cat3").parent('.cat-list-block').hide();
                $("#Cat3").hide();
                $("#" + opts.type + "_category_id").val("");
                main.hideOptions();
            },
            success: function(data) {
                if (data.isSubCategories) {
                    $("#Cat3").html(data.options);
                    $("#Cat3").show();
	                $("#Cat3").parent('.cat-list-block').show();
                    
                    $("#hide_category_id").val("");
                } else {
                    cat_id = $("#Cat2").find("option:selected").val();
                    
                    $("#hide_category_id").val(cat_id);
                    
                    $("#" + opts.type + "_category_id").val(cat_id);
                    main.downloadOptions(cat_id);
                }
            }
        });
    });

    //cat3
    $(opts.cat_3).change(function() {
        cat_id = $("#Cat3").find("option:selected").val();
        
        $("#hide_category_id").val(cat_id);
        
        $("#" + opts.type + "_category_id").val(cat_id);
        main.downloadOptions(cat_id);
    });
}