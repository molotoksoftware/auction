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



$csrfTokenName = Yii::app()->request->csrfTokenName;
$csrfToken = Yii::app()->request->csrfToken;
$csrf = "'$csrfTokenName':'$csrfToken'";



$this->widget('ex-bootstrap.widgets.ETbExtendedGridView', array(
    //'type' => 'striped bordered condensed',
    'id' => 'sales-list',
    'ajaxUrl' => array('/sales/sales/index'),
 //   'enableSorting' => true,
 //   'itemsCssClass' => 'table-normal table-hover-row',
    //'template' => "{items}",
    'dataProvider' => $model->search(),
    'filter' => $model,
  //  'pagerCssClass' => 'pagination pagination-centered',
    'summaryText' => 'Заявки {start}—{end} из {count}.',
    'columns' => array(
        [
          'header' => 'ID',
          'name' => 'sale_id',
          'value' => '$data->sale_id',
          'headerHtmlOptions' => ['width'=>'5%'],
        ],
        [
          'header' => 'Лот',
          'value' => '$data->item_id',
          'headerHtmlOptions' => ['width'=>'50%'],
        ],
        [

          'header' => 'Покупатель',
          'name' => 'buyer',
          'value' => '$data->buyer',
          'headerHtmlOptions' => ['width'=>'10%'],
        ],
        array(
            'header' => '->',
            'name' => 'review_my_about_saller',
            'value' => '$data->review_my_about_saller?"+":"-"',
            'filter' => false,
            'sortable' => false,
            'headerHtmlOptions' => ['width'=>'1%'],
        ),
        [
          'header' => 'Продавец',
          'name' => 'seller_id',
          'value' => '$data->seller_id',
          'headerHtmlOptions' => ['width'=>'10%'],
        ],
        [
            'header' => '<-',
            'name' => 'review_about_my_buyer',
            'value' => '$data->review_about_my_buyer?"+":"-"',
            'filter' => false,
            'sortable' => false,
            'headerHtmlOptions' => ['width'=>'1%'],
        ],
        

    )
));
