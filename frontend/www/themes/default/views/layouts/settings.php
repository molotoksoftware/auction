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



?>
    
    
<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
<div class="row auction">
        <div class="col-xs-9">
            <h2>Настройки и управление аккаунтом</h2>
        </div>
</div>

<hr class="top10 horizontal_line">
<div class="row">
    <div class="col-xs-3 nav_cabinet">
         <?php 
                $this->widget(
                    'zii.widgets.CMenu',
                    array(
                        'items' => array(
                            [
                                'label' => '<span class="glyphicon glyphicon-cog"></span> Общие настройки',
                                'itemOptions' => ['class' => 'list-group-item head_li'],
                            ],
                                [
                                    'label' => 'Основные настройки',
                                    'url' => ['/user/settings/common'],
                                    'itemOptions' => ['class' => 'list-group-item'],

                                ],
                                [
                                    'label' => 'Личный счет',
                                    'url' => ['/user/balance/index'],
                                    'itemOptions' => ['class' => 'list-group-item'],

                                ],
                                [
                                    'label' => 'Уведомления E-mail',
                                    'url' => ['/user/settings/notifications'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],

                                [
                                    'label' => 'Доступ',
                                    'url' => ['/user/settings/access'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],
                            [
                                'label' => '<span class="glyphicon glyphicon-user"></span> Продавцу товаров',
                                'itemOptions' => ['class' => 'list-group-item head_li'],
                            ],
                                [
                                    'label' => 'Верификация аккаунта',
                                    'url' => ['/user/settings/certified'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],
                                [
                                    'label' => 'ПРО-аккаунт',
                                    'url' => ['/user/pro/index'],
                                    'itemOptions' => ['class' => 'list-group-item'],

                                ],
                                [
                                    'label' => 'Страница "Обо мне"',
                                    'url' => ['/user/settings/aboutMe'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],

                                [
                                    'label' => 'Массовые действия с лотами',
                                    'url' => ['/user/settings/bulkUpdates'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],

                        ),

                        'encodeLabel' => false,
                        'htmlOptions' =>['class' => 'list-group'],
                   //     'linkLabelWrapper' => 'span',
                   //     'itemTemplate' => '{menu}',
                    )
                );
                 ?>
    </div>
    <div class="col-xs-9 content_cabinet">
        <?php echo $content; ?>
    </div>
</div>

</div>


<?php $this->endContent(); ?>