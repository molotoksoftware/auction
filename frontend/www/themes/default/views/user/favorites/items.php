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


Yii::app()->clientScript->registerScript('item-favorites', '
function removeItems($elements, url, table, type) {
    $.ajax({
        url: url,
        data: {data : JSON.stringify($elements), type:type},
        dataType: "json",
        type: "GET",
        success: function(data) {
           $.fn.yiiGridView.update(table);
        }
    });
}

//items
$("#btn-delete-fav").click(function(){
    var values = $(".favorite_items input:checkbox:checked");

    $elements = [];    
    $.each(values,function(i, val){
        $elements.push($(val).val());
    });
    removeItems($elements,"' . Yii::app()->createUrl('/user/favorites/delete') . '", "table-items", 1);
    return false;
}); 


', CClientScript::POS_READY); ?>
<h3><?= Yii::t('basic', 'Favorite items') ?> <?= UI::showQuantityTablHdr(CounterInfo::quantityFavItems()); ?></h3>

<?php
$this->renderPartial('_table_items', array(
    'limit' => Yii::app()->params['cabinetTablePageSize']
));
?>

<?php if (CounterInfo::quantityFavItems() > 0): ?>
    <div class="form-group">
        <label><?= Yii::t('basic', 'Actions with marked') ?>:</label>
        <button id="btn-delete-fav" class="btn btn-info"><?= Yii::t('basic', 'Remove from favorites') ?></button>
    </div>
<?php endif; ?>


