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
            <h2><?= Yii::t('basic', 'Your Control Panel')?></h2>
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
                                'label' => '<span class="glyphicon glyphicon-cog"></span> '.Yii::t('basic', 'General settings'),
                                'itemOptions' => ['class' => 'list-group-item head_li'],
                            ],
                                [
                                    'label' => Yii::t('basic', 'My Account'),
                                    'url' => ['/user/settings/common'],
                                    'itemOptions' => ['class' => 'list-group-item'],

                                ],
                                [
                                    'label' => Yii::t('basic', 'Payments'),
                                    'url' => ['/user/balance/index'],
                                    'itemOptions' => ['class' => 'list-group-item'],

                                ],
                                [
                                    'label' => Yii::t('basic', 'Notifications e-mail'),
                                    'url' => ['/user/settings/notifications'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],

                                [
                                    'label' => Yii::t('basic', 'Access control'),
                                    'url' => ['/user/settings/access'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],
                            [
                                'label' => '<span class="glyphicon glyphicon-user"></span> '. Yii::t('basic', 'Selling'),
                                'itemOptions' => ['class' => 'list-group-item head_li'],
                            ],
                                [
                                    'label' => Yii::t('basic', 'Verification'),
                                    'url' => ['/user/settings/certified'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],
                                [
                                    'label' => Yii::t('basic', 'PRO'),
                                    'url' => ['/user/pro/index'],
                                    'itemOptions' => ['class' => 'list-group-item'],

                                ],
                                [
                                    'label' => Yii::t('basic', 'About Me'),
                                    'url' => ['/user/settings/aboutMe'],
                                    'itemOptions' => ['class' => 'list-group-item'],
                                ],

                                [
                                    'label' => Yii::t('basic', 'Bulk updates'),
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