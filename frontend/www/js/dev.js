$('document').ready(function () {

    $('ul.main_nav li span.str').click(function () {
        $(this).closest('li').find('ul:first').slideToggle();
        $(this).closest('li').toggleClass('active');
    });

    $('ul.main_nav.js_profile_cat_tree a').click(function () {
        /*$('ul.main_nav.js_profile_cat_tree li.active').removeClass('active');*/
        $('ul.main_nav.js_profile_cat_tree a.active').removeClass('active');
        $(this).addClass('active');
        var $parentLi = $(this).closest('li');
        $parentLi.addClass('active');
        $parentLi.find('ul').eq(0).show();
        $('html, body').animate({
            scrollTop: $("#lots-grid").offset().top
        }, 200);
    });
    $('.main_nav.js_profile_cat_tree a.all-cat-item').addClass('active');

    var angle_right = $("ul.main_nav ul").find('li').find('span.str');
    if ($(angle_right).length) {
        $(angle_right).closest('li').find('span.count:first').css('right', '26px');
    }
    var ul_li_act = $('ul.main_nav').find('li.active');
    if ($(ul_li_act).length) {
        $(ul_li_act).find('ul:first').css('display', 'block');
        $(ul_li_act).parents('ul').css('display', 'block');
        $(ul_li_act).parents('ul').closest('li').addClass('active');
    }

    $('a.select_cat span').click(function () {
        $('.search_cat').slideToggle();
        $(this).closest('a').toggleClass('active');
        jQuery('.scroll-pane').jScrollPane();
    });

    $('a.link_select_1 span').click(function () {
        $('div.create_lot_right_cat_list_2').slideToggle();
    });
    $('div.create_lot_right_cat_list_2 ul').find('li').click(function () {
        $('a.link_select_1').html($(this).text() + '<span></span><div class="clear"></div>');
        $('div.create_lot_right_cat_list_2').slideToggle();
        $('a.link_select_1 span').click(function () {
            $('div.create_lot_right_cat_list_2').slideToggle();
        });
    });
    $('a.link_select_2 span').click(function () {
        $('div.create_lot_right_cat_list_1').slideToggle();
    });
    $('div.create_lot_right_cat_list_1 ul').find('li').click(function () {
        $('a.link_select_2').html($(this).text() + '<span></span><div class="clear"></div>');
        $('div.create_lot_right_cat_list_1').slideToggle();
        $('a.link_select_2 span').click(function () {
            $('div.create_lot_right_cat_list_1').slideToggle();
        });
    });
    $('a.link_select_3 span').click(function () {
        $('div.create_lot_right_cat_list_3').slideToggle();
    });
    $('div.create_lot_right_cat_list_3 ul').find('li').click(function () {
        $('a.link_select_3').html($(this).text() + '<span></span><div class="clear"></div>');
        $('div.create_lot_right_cat_list_3').slideToggle();
        $('a.link_select_3 span').click(function () {
            $('div.create_lot_right_cat_list_3').slideToggle();
        });
    });
    $('a.link_select_4 span').click(function () {
        $('div.create_lot_right_cat_list_4').slideToggle();
    });
    $('div.create_lot_right_cat_list_4 ul').find('li').click(function () {
        $('a.link_select_4').html($(this).text() + '<span></span><div class="clear"></div>');
        $('div.create_lot_right_cat_list_4').slideToggle();
        $('a.link_select_4 span').click(function () {
            $('div.create_lot_right_cat_list_4').slideToggle();
        });
    });


    $('.add_photo_sub').mouseover(function () {
        $('.add_img a').css('background-position', '0 -53px');
    });
    $('.add_photo_sub').click(function () {
        $('.add_img a').css('background-position', '0 -106px');
    });


    $('.star_ver').click(function () {
        location.href = '/pages/rating';
    });


    // Checkbox "Check all" for grids
    var $customGridSelectAllChk = $('input:checkbox[name="grid_select_all"]');
    if ($customGridSelectAllChk.size()) {
        var $grid = $('#' + $customGridSelectAllChk.data('target-grid'));
        var $gridRowCheckboxes = $grid.find('tbody .checkbox-column input:checkbox');
        var $gridSelectAllChk = $grid.find('thead .checkbox-column input:checkbox');

        $customGridSelectAllChk.on('click', function () {
            var $grid = $('#' + $customGridSelectAllChk.data('target-grid'));
            var $gridRowCheckboxes = $grid.find('tbody .checkbox-column input:checkbox');
            var $gridSelectAllChk = $grid.find('thead .checkbox-column input:checkbox');

            $gridRowCheckboxes.attr('checked', $(this).is(':checked'));
            $gridSelectAllChk.attr('checked', $(this).is(':checked'));
        });

        $gridRowCheckboxes.on('click', function () {
            var $grid = $('#' + $customGridSelectAllChk.data('target-grid'));
            var $gridRowCheckboxes = $grid.find('tbody .checkbox-column input:checkbox');

            var total = $gridRowCheckboxes.size();
            var totalChecked = $gridRowCheckboxes.filter(':checked').size();
            $customGridSelectAllChk.attr('checked', total === totalChecked);
        });

        $gridSelectAllChk.on('change', function () {
            $customGridSelectAllChk.attr('checked', $(this).is(':checked'));
        });
    }

});

function appendUrlParam(url, parameterName, parameterValue, atStart) {
    var replaceDuplicates = true;
    var urlhash, sourceUrl;
    if (url.indexOf('#') > 0) {
        var cl = url.indexOf('#');
        urlhash = url.substring(url.indexOf('#'), url.length);
    } else {
        urlhash = '';
        cl = url.length;
    }
    sourceUrl = url.substring(0, cl);

    var urlParts = sourceUrl.split("?");
    var newQueryString = "";

    if (urlParts.length > 1) {
        var parameters = urlParts[1].split("&");
        for (var i = 0; (i < parameters.length); i++) {
            var parameterParts = parameters[i].split("=");
            if (!(replaceDuplicates && parameterParts[0] == parameterName)) {
                if (newQueryString == "")
                    newQueryString = "?";
                else
                    newQueryString += "&";
                newQueryString += parameterParts[0] + "=" + (parameterParts[1] ? parameterParts[1] : '');
            }
        }
    }
    if (newQueryString == "")
        newQueryString = "?";

    if (atStart) {
        newQueryString = '?' + parameterName + "=" + parameterValue + (newQueryString.length > 1 ? '&' + newQueryString.substring(1) : '');
    } else {
        if (newQueryString !== "" && newQueryString != '?')
            newQueryString += "&";
        newQueryString += parameterName + "=" + (parameterValue !== '' ? parameterValue : '');
    }
    return urlParts[0] + newQueryString + urlhash;
}

function getJsonFromUrl() {
    var query = location.search.substr(1);
    var result = {};
    if (query.indexOf("&") !== -1) {
        query.split("&").forEach(function (part) {
            var item = part.split("=");
            result[item[0]] = decodeURIComponent(item[1]);
        });
    }
    return result;
}

function resetGridSelectAllChk() {
    $('input:checkbox[name="grid_select_all"]').attr('checked', false);
}

function updateWindowUrl(url) {
    window.history.pushState({}, "", url);
}

/**
 *
 * @param name
 * @returns {string}
 */
function getUrlParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

/**
 *
 * @param paramName
 * @param url
 * @returns {string}
 */
function getParamValueFromUrlString(paramName, url) {
    var queryString = url.substring(url.indexOf('?'));
    var name = paramName.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(queryString);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

/**
 * @param obj
 * @returns {number}
 */
Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

/**
 * @param param
 * @param value
 */
function setParamToPageUrl(param, value) {
    var pageUrl = window.location.href;
    pageUrl = appendUrlParam(pageUrl, param, value);
    updateWindowUrl(pageUrl);
}