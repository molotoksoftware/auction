var defaults = {
    csrfToken: null,
    csrfTokenName: null,
    dynamicCategoriesUrl: '',
    downloadOptionsUrl: '',
    cat_1: '',
    cat_2: '',
    cat_3: '',
    cat_4: '',
    cat_5: '',
    type: '',
    select_cat_t: '',
    no_param: '',
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
            content.empty().html(opts.select_cat_t + ' ↑');
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
                        content.empty().html(opts.no_param).show();
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
                $("#Cat4").parent('.cat-list-block').hide();
                $("#Cat5").parent('.cat-list-block').hide();
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
            "level": 3,
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
                $("#Cat4").parent('.cat-list-block').hide();
                $("#Cat5").parent('.cat-list-block').hide();
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
    $(opts.cat_3).change(function() {
        var data = {
            "cat_id": $(this).find("option:selected").val(),
            "level": 4,
            "where_show": opts.where_show
        };

        $.ajax({
            type: 'GET',
            url: opts.dynamicCategoriesUrl,
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $("#Cat4").parent('.cat-list-block').hide();
                $("#Cat4").hide();
                $("#Cat5").parent('.cat-list-block').hide();
                $("#" + opts.type + "_category_id").val("");
                main.hideOptions();
            },
            success: function(data) {
                if (data.isSubCategories) {
                    $("#Cat4").html(data.options);
                    $("#Cat4").show();
                    $("#Cat4").parent('.cat-list-block').show();

                    $("#hide_category_id").val("");
                } else {
                    cat_id = $("#Cat3").find("option:selected").val();

                    $("#hide_category_id").val(cat_id);

                    $("#" + opts.type + "_category_id").val(cat_id);
                    main.downloadOptions(cat_id);
                }
            }
        });
    });

    $(opts.cat_4).change(function() {
        var data = {
            "cat_id": $(this).find("option:selected").val(),
            "level": 5,
            "where_show": opts.where_show
        };

        $.ajax({
            type: 'GET',
            url: opts.dynamicCategoriesUrl,
            data: data,
            dataType: 'json',
            beforeSend: function() {
                $("#Cat5").parent('.cat-list-block').hide();
                $("#Cat5").hide();
                $("#" + opts.type + "_category_id").val("");
                main.hideOptions();
            },
            success: function(data) {
                if (data.isSubCategories) {
                    $("#Cat5").html(data.options);
                    $("#Cat5").show();
                    $("#Cat5").parent('.cat-list-block').show();

                    $("#hide_category_id").val("");
                } else {
                    cat_id = $("#Cat4").find("option:selected").val();

                    $("#hide_category_id").val(cat_id);

                    $("#" + opts.type + "_category_id").val(cat_id);
                    main.downloadOptions(cat_id);
                }
            }
        });
    });


    //cat4
    $(opts.cat_5).change(function() {
        cat_id = $("#Cat5").find("option:selected").val();

        $("#hide_category_id").val(cat_id);

        $("#" + opts.type + "_category_id").val(cat_id);
        main.downloadOptions(cat_id);
    });
}