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

Yii::app()->clientScript->registerScript(
        'user-edit-scripts', '

    $(".update-my-lots-address").click(function() {
        if(confirm("'.Yii::t('basic', 'Update for all items?').'")) {
            if ($(".city-selector .city-select").val()) {
                $.ajax({
                    url: "/editor/setAuctionCity/id_city/" + $(".city-selector .city-select").val(),
                    complete: function () {
                        alert("'.Yii::t('basic', 'Succeessfully updated').'")
                    }
                });
            } else {
                alert("'.Yii::t('basic', 'Select a city').'");
            }
        }
    })

    $("#usl_peredachi_r").click(function()
    {
        if (confirm("'.Yii::t('basic', 'Update for all items?').'")) 
        {
            var inform = $("#EditUserForm_terms_delivery").val();

            $.ajax({
    			type: "GET",
                data: {info: inform},
    			url: "/user/settings/update_info",
    			"success":function() 
                {
                    alert("'.Yii::t('basic', 'Succeessfully updated').'");
    			}
    		});
        }
    });
   ', CClientScript::POS_END
);
?>


<h3><?= Yii::t('basic', 'My Account') ?></h3>


<?php if (Yii::app()->user->hasFlash('success-edit-profile')): ?>
    <div class="alert alert-success">
        <?= Yii::app()->user->getFlash('success-edit-profile'); ?>
    </div>
<?php endif; ?>

<?php
$this->widget(
        'ext.imageSelect.ImageSelect', array(
    'path' => $user->uploadedFile->getImage('avatar'),
    'alt' => $user->login,
    'raiting' => $user->rating,
    'uploadUrl' => '/user/settings/uploadAvatar/',
    'htmlOptions' => array('class' => 'img-thumbnail', 'style' => 'width:150px;')
        )
);
?>

<?php
$form = $this->beginWidget(
        'CActiveForm', array(
    'id' => 'form-edit-user',
    //'action' => $this->createUrl('/creator/lot'),
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'errorMessageCssClass' => 'error',
    'clientOptions' => array(
        'errorCssClass' => 'error-row',
        'successCssClass' => 'success-row',
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnType' => false,
    ),
    'focus' => array($model, 'firstname'),
    'htmlOptions' => array(
        'autocomplete' => 'off',
        'class' => 'form-horizontal',
    ),
        )
);
?>

<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('basic', 'User\'s data') ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="inputLogin" class="col-sm-2 control-label"><?= Yii::t('basic', 'Username') ?>:</label>
            <div class="col-sm-10 control-label">
                <?= $user->login; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputRating" class="col-sm-2 control-label"><?= Yii::t('basic', 'Rating') ?>:</label>
            <div class="col-sm-10 control-label">
                <?= $user->rating; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputCreatetime" class="col-sm-2 control-label"><?= Yii::t('basic', 'Registration') ?>:</label>
            <div class="col-sm-10 control-label">
                <?= $user->createtime; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputNick" class="col-sm-2 control-label"><?= Yii::t('basic', 'Display name') ?>:</label>
            <div class="col-sm-10 control-label">
                <? if(!$model->nick) { ?>
                <?php echo $form->error($model, 'nick'); ?>
                <?php echo $form->textField($model, 'nick', array('class' => 'form-control', 'style' => 'width:200px')); ?>
                <? } else { ?>
                <?= CHtml::encode($model->nick); ?>
                <? } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputFirstname" class="col-sm-2 control-label"><?= Yii::t('basic', 'First name') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php echo $form->textField($model, 'firstname', array('class' => 'form-control', 'style' => 'width:200px')); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputLastname" class="col-sm-2 control-label"><?= Yii::t('basic', 'Last name') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php echo $form->textField($model, 'lastname', array('class' => 'form-control', 'style' => 'width:200px')); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPlace" class="col-sm-2 control-label"><?= Yii::t('basic', 'Location') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php
                $this->widget('frontend.widgets.citySelector.CitySelectorWidget', array(
                    'model' => $model
                ));
                ?>
                <small><a href="javascript:void(0)" class="update-my-lots-address"><?= Yii::t('basic', 'Update for all lots') ?></a></small>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail" class="col-sm-2 control-label"><?= Yii::t('basic', 'E-mail') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php echo $form->error($model, 'email'); ?>
                <?php echo $form->textField($model, 'email', array('class' => 'form-control', 'style' => 'width:200px')); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputTelephone" class="col-sm-2 control-label"><?= Yii::t('basic', 'Telephone') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php echo $form->error($model, 'telephone'); ?>
                <?php echo $form->textField($model, 'telephone', array('class' => 'form-control', 'style' => 'width:200px')); ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" name="name" class="btn btn-default" value="<?= Yii::t('basic', 'Save change') ?>">
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('basic', 'Additional tools') ?></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="inputAdd_contact_info" class="col-sm-2 control-label"><?= Yii::t('basic', 'Additional contact information') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php echo $form->textArea($model, 'add_contact_info', ['class' => 'form-control', 'style' => 'height: 150px;']); ?>
                <small><?= Yii::t('basic', 'Additional telephone numbers, e-mail, Skype, Telegram.') ?></small> 
            </div>
        </div>
        <div class="form-group">
            <label for="inputTerms_delivery" class="col-sm-2 control-label"><?= Yii::t('basic', 'Shipping terms') ?>:</label>
            <div class="col-sm-10 control-label">
                <?php echo $form->textArea($model, 'terms_delivery', ['class' => 'form-control', 'style' => 'height: 150px;']); ?>
                <small><?= Yii::t('basic', 'Shipping terms for your items.') ?></small> 
                <a id="usl_peredachi_r" href="#">
                    <small><?= Yii::t('basic', 'Update for all lots') ?></small>
                </a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input type="submit" name="name" class="btn btn-default" value="<?= Yii::t('basic', 'Save change') ?>">
            </div>
        </div>
    </div>
</div>

<?php $this->endWidget(); ?>




