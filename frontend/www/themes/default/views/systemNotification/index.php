<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
 * @copyright 2016 MolotokSoftware
 * @license GNU General Public License, version 3
 */

/**
 * 
 * This file is part of MolotokSoftware.
 *
 * MolotokSoftware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MolotokSoftware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with MolotokSoftware.  If not, see <http://www.gnu.org/licenses/>.
 */

cs()->registerScriptFile(bu() . '/js/jquery.tmpl.min.js', CClientScript::POS_END);

$csrfTokenName = Yii::app()->request->csrfTokenName;
$csrfToken = Yii::app()->request->csrfToken;
$csrf = "'$csrfTokenName':'$csrfToken' ";

Yii::app()->clientScript->registerScript(
    'scroll-content',
    "
    $(window).scrollTop(0);

     currentPage = " . $pagination->getCurrentPage() . "
     pageCount = " . $pagination->getPageCount() . "
     c_p = 1;
     lock = false;//fix double content
     var container = $('.notification_container');
     

    function load_page() {

          if (pageCount<=0) {
            //fix for one show messages
            if ($('.error-message').size() <= 0) {
                $(container).append('<div class=\"error-message\">".Yii::t('basic', 'No notifications')."</div>');
                return;
            }
          }


        if (c_p<=pageCount){
        $.ajax({
            url:window.location.href,
            data: {
                'p' : c_p,
                'get-content' : 'scroll',
                $csrf,
                },
            dataType: 'json',
            type: 'POST',
            beforeSend: function(){
                $(container).find('.error-message').remove();
                //before load content
                //preload_ajax('.product_list');
            },
            success:function(data) {
                if (data.data.length<=0){
                     $(container).append('<div class=\"error-message\">".Yii::t('basic', 'No notifications')."</div>');
                } else {
                    it = 10;              
                    $.each(data.data, function(i, item){
                       content = $('#item-notif').tmpl(item);
                       $(container).append(content);
                       it++;    
                    });
                    //removePreload_ajax('.product_list');
                    c_p++;//curent page+1
                    lock = true;//fix double content
               }
        }//end success
        });//end ajax
        }
    }//end function

    load_page();

    //handler scroll
    $(window).scroll(function() {
        if (($(window).scrollTop()+100)>=$(document).height()-$(window).height() && lock==true) {
            lock=false;
            load_page();
        }
    });
    
    
//==============================================================================

//checkbox oll------------------------------------------------------------------
$('#items_checkbox_oll').change(function(){
    if ($(this).prop('checked')) {
        $('.checkbox_one').prop('checked', true);
    } else {
        $('.checkbox_one').prop('checked', false);
    }
});

$('.checkbox_one').attr('checked', true);

function actions(action, values) {
    \$elements = [];
    $.each(values,function(i, val){
        \$elements.push($(val).val());
    });

    if (\$elements.length<=0)
        return false;

    $.ajax({
        url: '" . Yii::app()->createUrl('/systemNotification/manager') . "',
        data: {
            action : action  ,
            elements : JSON.stringify(\$elements)
        },
        dataType: 'json',
        type: 'GET',
        success: function(data) {
            $('#items_checkbox_oll').attr('checked', false);
            c_p=1;
            $(container).empty();
            load_page();
        }
    });
}


$('.btn-action').click(function() {
    var values = $('.notification_container input:checkbox:checked');
    var action = $(this).data('action');
    actions(action, values);
    return false;
});

",CClientScript::POS_END);
?>
<!-- begin templates item dialog -->
<script id="item-notif" type="text/x-jquery-tmpl">
    <li data-id="${id}" class="notf_item list-group-item">
        <div class="info">
            <input type="checkbox" class="checkbox_one" name="item" value="${id}">

            {{if read==true}}
                <span class='label label-default'><span class='glyphicon glyphicon-ok'></span> <?= Yii::t('basic', 'Read')?></span>
            {{else}}
                <span class='label label-success'><?= Yii::t('basic', 'New')?></span>
            {{/if}}

            <span class="small">${date}</span>
            <div class="clear"></div>
        </div>
        <div class="content">
            <p>{{html text}}</p>
        </div>
    </li>
</script>

<h3><?= Yii::t('basic', 'Notifications')?> <?=UI::showQuantityTablHdr(SystemNotification::model()->byUserId(Yii::app()->user->id)->getByStatus(0)->count()); ?></h3>

<form id="form-notifications">
<div class="form_head form-inline">
    <div class="checkbox uniq_check">
        <label>
              <input id="items_checkbox_oll" type="checkbox"> <?= Yii::t('basic', 'Select all')?>
        </label>
    </div>
    <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-default btn-action" data-action="removes" id="btn-delete-fav"><?= Yii::t('basic', 'Delete')?></button>
        <div class="btn-group btn-group-sm">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
              <?= Yii::t('basic', 'Mark as')?>
              <span class="caret"></span>
            </button>
          <ul class="dropdown-menu">
            <li><a class='btn-action' data-action='read' id="btn-open-fav" href="#"><?= Yii::t('basic', 'Read')?></a></li>
            <li><a class='btn-action' data-action='unread' id="btn-close-fav" href="#"><?= Yii::t('basic', 'Unread')?></a></li>
          </ul>
        </div>
    </div>
</div>

<ul class="notification_container list-group margint_top_30">
</ul>
</form>