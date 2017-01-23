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


$tab = <<<EOD
    $('#btn-change-view a').click(function (e) {
        e.preventDefault();
        if ($(this).attr('href')=='#table') {
            $.fn.yiiGridView.update('table-category');
        }
        
        if ($(this).attr('href')=='#tree') {
            $('#Category-wrapper').jstree("refresh");
        }
        
        $(this).tab('show');
        
    });
EOD;
$search = <<<EOD
    function searchCategory(value) {
        if ($('#table').is('.active')){
            
            $.fn.yiiGridView.update('table-category', {
		data:{ 'Category[name]':value},
            });
            
        } else {
            $('#Category-wrapper').jstree('search', value);      
        }
    }
EOD;

Yii::app()->clientScript->registerScript('sortable-project', $tab, CClientScript::POS_READY);
Yii::app()->clientScript->registerScript('search-category', $search, CClientScript::POS_END);
?>

<?php
$this->pageTitle = 'Категории';
$this->header_info = array(
    'icon' => 'icon-inbox icon-2x',
    'title' => 'Категории',
);

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-folder-open',
        'label' => 'Каталог',
        'url' => array('/catalog/category/index'),
    ),
    array(
        'icon' => 'icon-inbox',
        'label' => 'Категории',
        'url' => '',
    ),
);
?>
<div class="container-fluid padded-mini">
    <div class="row-fluid">
        <div class="span3">
            <div class="btn-group" id="btn-change-view">
                <a href="#table" data-toggle="tooltip" title="Отображать как таблицу" class="active btn btn-default"><i class="icon-list-alt"></i></a>
                <a href="#tree" data-toggle="tooltip" title="Отображать как дерево" class="active btn btn-default"><i class="icon-folder-open-alt"></i></a>
            </div>
            <a href="<?=Yii::app()->createUrl('/catalog/category/create');?>" class="btn btn-blue">создать категорию</a>
        </div>
        <div class="span9">
            <div class="input-prepend search-category">
                <a href="#" class="add-on">
                    <i class="icon-search"></i>
                </a>
                <input onkeyup="searchCategory($(this).val());" type="text" placeholder="Поиск...">
            </div>

        </div>
    </div>
</div>

<div class="container-fluid padded">
    <div class="box">
        <div class="tab-content">
            <div class="tab-pane active" id="table">
                <?php 
                $this->renderPartial('_table_categories', array(
                    'model' => $model
                ));
                ?>
            </div>
            <div class="tab-pane" id="tree">
                <?php
                $this->renderPartial('_tree_categories');
                ?>
            </div>
        </div>
    </div>
</div>
