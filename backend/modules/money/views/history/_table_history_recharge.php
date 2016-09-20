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
$this->widget(
        'ex-bootstrap.widgets.ETbExtendedGridView', array(
    'id' => 'table-history-order',
    'dataProvider' => $model->searchAdmin(),
    'filter' => $model,
    'summaryText' => 'История {start}—{end} из {count}.',
    'columns' => array(
        array(
            'header' => '#',
            'name' => 'id',
            'filter' => false,
            'headerHtmlOptions' => ['width' => '1%'],
        ),
        array(
            'header' => 'Пользователь',
            'name' => 'user_id',
            'type' => 'raw',
            'value' => '$data->getBuyerLink()',
            'headerHtmlOptions' => ['width' => '10%'],
        ),
        array(
            'header' => 'Сума',
            'name' => 'summa',
            'type' => 'raw',
            'value' => '$data->getSummaWithIcoType()',
            'headerHtmlOptions' => ['width' => '10%'],
        ),
        array(
            'header' => 'Тип операции',
            'name' => 'type',
            'filter' => BalanceHistory::getStatusList(),
            'type' => 'raw',
            'value' => '$data->getStatus()',
            'headerHtmlOptions' => ['width' => '10%'],
        ),
        array(
            'header' => 'Описание',
            'name' => 'description',
            'type' => 'raw',
            'value' => '$data->description',
            'headerHtmlOptions' => ['width' => '20%'],
        ),
        array(
            'header' => 'Дата',
            'filter' => false,
            'name' => 'created_on',
            'value' => 'Yii::app()->dateFormatter->format("dd MMMM HH:mm", $data->created_on)',
            'headerHtmlOptions' => ['width' => '8%'],
        ),
    )
        )
);
