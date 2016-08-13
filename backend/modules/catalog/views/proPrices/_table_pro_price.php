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
    'id' => 'table-advert-rates',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'ajaxUrl' => array('/catalog/advertRates/index'),
    'columns' => array(
        array(
            'type' => 'raw',
            'name' => 'name',
        ),
        array(
            'header' => 'Период',
            'type' => 'raw',
            'name' => 'duration',
            'value'=>'$data->getDuration()'
        ),
        array(
            'type' => 'raw',
            'name' => 'price',
            'value' => '$data->getPrice()'
        ),
        array(
            'htmlOptions' => array('nowrap' => 'nowrap'),
            'header' => '',
            'class' => 'ex-bootstrap.widgets.ETbButtonColumn',
            'template' => '{update}',
        ),
    )
));
