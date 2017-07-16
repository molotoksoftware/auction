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

/** @var User $user */

Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/user/settings/common.js');
?>

<h3><?= Yii::t('basic', 'About me')?></h3>

<?php if (Yii::app()->user->hasFlash('success-edit-profile')): ?>
    <div class="alert alert-success">
        <?= Yii::app()->user->getFlash('success-edit-profile'); ?>
    </div>
<?php endif; ?>

<p>
    <?= Yii::t('basic', 'This information will publish in your profile page')?>.
    <a href="/user/user/about_me/login/<?php echo $user->login; ?>">
        <?= Yii::t('basic', 'Go to')?>
    </a>

</p>


<?php
/** @var CActiveForm $form */
$form = $this->beginWidget(
    'CActiveForm',
    array(
        'errorMessageCssClass' => 'error',
        'clientOptions' => array(
            'errorCssClass' => 'error-row',
            'successCssClass' => 'success-row',
        ),
    )
);
?>

<?php
Yii::import('backend.extensions.imperaviRedactor.ImperaviRedactorWidget');
$this->widget('ImperaviRedactorWidget', array(
    'model' => $user,
    'attribute' => 'about',
    'options' => array(
        'lang' => 'ru',
        'convertVideoLinks' => 'true',
         'buttons' => array('formatting', '|', 'bold', 'italic', 'deleted', 'underline', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'fontcolor', 'backcolor', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'link', 'image', 'video', 'horizontalrule'),
        'iframe' => true,
        'minHeight' => 250
    ),
    'plugins' => array(
        'fontsize' => array(
            'js' => array('fontsize.js'),
        ),
    )
));
?>

<?= $form->error($user, 'about') ?>

                <input type="submit" name="name" class="btn btn-default margint_top_30" value="<?= Yii::t('basic', 'Save')?>"/>

<?php $this->endWidget(); ?>
