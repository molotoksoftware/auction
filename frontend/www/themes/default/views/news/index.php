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


$items = $data->getData();
?>

<div class="row auction">
        <div class="col-xs-12">
            <h2><?= Yii::t('basic', 'News')?></h2>
        </div>
</div>
<hr class="top10 horizontal_line">


<div class="news_list_column">
<?php foreach ($items as $item): ?>
    <?php $this->renderPartial('_item', array('item' => $item)); ?>
<?php endforeach; ?>
</div>


    <?php
    $this->widget('CLinkPager', array(
        'pages' => $data->getPagination(),
        'maxButtonCount' => 5,
        'firstPageLabel' => '',
        'lastPageLabel' => '',
        'selectedPageCssClass' => 'active',
        'prevPageLabel' => '&lt; <span>'.Yii::t('basic', 'Previous').'</span>',
        'nextPageLabel' => '<span>'.Yii::t('basic', 'Next').'</span> &gt;',
        'header' => '',
        'footer' => '',
        'cssFile' => false,
        'htmlOptions' => array(
            'class' => 'pagination'
        )
    ));
    ?>

