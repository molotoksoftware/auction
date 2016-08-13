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


$this->pageTitle = "Новость: " . $model->title;

$this->header_info = array(
    'icon' => 'icon-leaf icon-2x',
    'title' => "Новость: " . $model->title,
    'description' => 'опубликовано ' . Yii::app()->dateFormatter->format('d MMMM yyyy', $model->date)
);

cs()->registerScript('comment', '
    var form = $(".comment-reply-form");
    $(".btn-reply-link").toggle(function() {
        $(this).parent().parent().append(form.show());
        var id_news = $(this).data("idNews");
        var id_comment = $(this).data("idComment");
        $(form).find("input[name=\"id_news\"]").val(id_news);
        $(form).find("input[name=\"id_comment\"]").val(id_comment);      

        return false; 
    }, function(){
        $(this).parent().parent().find(".comment-reply-form").remove();
        return false; 
        
    });
', CClientScript::POS_LOAD);
?>

<?php
Yii::app()->clientScript->registerScript('comment-check', '
$("#comment-form").submit(function(){
var value = $("#context-comment").val();
    if (value=="") {
        alert("Для создания комментария необходимо заполнить текст");
        return false;
    } else {
            var reg=/<\/?[^>]+>/g;
        var result=reg.test(value);
        if (result) {
            alert("Отзыв не может содержать html теги");    
            return false;
            }
    }
});
', CClientScript::POS_LOAD);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <?php echo CHtml::link('← Вернуться к новостям', array('/news/news/index'), array('class' => 'reply-news')); ?>
            <p><?php echo $model->content; ?></p>
        </div>
    </div><!-- row-fluid-->
</div><!--container-fluid-->