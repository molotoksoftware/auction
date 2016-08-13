<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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

<?php if ($model->isNewRecord) : ?>
<h3><?php echo Yii::t('global', 'Create') ?> <?php echo Yii::t('global', $modelClassName) ?></h3>
<?php elseif (!$model->isNewRecord): ?>
<h3><?php echo Yii::t('global', 'Update') ?> <?php echo Yii::t('global', $modelClassName) ?></h3>
<?php endif; ?>

<p> <h2><?php //echo $model->name;?></h2><p>

<?php      $val_error_msg = Yii::t('global', "Error.$modelClassName  was not saved.");
                   $val_success_message = ($model->isNewRecord) ?
                   Yii::t('global', "$modelClassName has been created successfully.") :
                    Yii::t('global', "$modelClassName  has been updated successfully.");
?>

<div id="success-note" class="alert alert-success"
     style="display:none;">
    <?php   echo $val_success_message;  ?>
</div>

<div id="error-note" class="alert alert-error"
     style="display:none;">
    <?php   echo $val_error_msg;  ?>
</div>

<div id="ajax-form" class='form'>
    <?php
    $formId = "$modelClassName-form";

    $actionUrl=($model->isNewRecord)?
     (! isset($_POST['create_root'])?CController::createUrl($this->id.'/createnode'):CController::createUrl($this->id.'/createRoot')):
    CController::createUrl($this->id.'/updatenode');

    $form = $this->beginWidget('CActiveForm', 
            array(
               'id' => $formId,
               //  'htmlOptions' => array('enctype' => 'multipart/form-data'),
               'action' => $actionUrl,
               // 'enableAjaxValidation'=>true,
               'enableClientValidation' => true,
               'focus' => array($model, 'name'),
               'errorMessageCssClass' => 'alert alert-error',
               'clientOptions' => array(
                   'validateOnSubmit' => true,
                   'validateOnType' => false,
                   'inputContainer' => '.control-group',
                   'errorCssClass' => 'error',
                   'successCssClass' => 'success',
                   'afterValidate' => 'js:function(form,data,hasError){$.js_afterValidate(form,data,hasError);  }',
               ),
            ));
    ?>

    <?php
         echo $form->errorSummary($model,
            '<div style="font-weight:bold">Please correct these errors:</div>',
             NULL,
             array('class' => 'alert alert-error'));
    ?>
    <p class="note">Fields with <span class="required">*</span> are required.</p>
    <fieldset>

        <div class="control-group">
            <?php echo $form->labelEx($model, 'name', array('class' => 'control-label')); ?>
            <div class="controls">
                <?php  $name=(!$model->isNewRecord)?$model->name:''  ?>
                <?php echo $form->textField($model, 'name', array('value'=>$name,'class' => 'span4', 'size' => 60, 'maxlength' => 128)); ?>
                <p class="help-block"><?php echo $form->error($model, 'name'); ?></p>
            </div>
        </div>

        <div class="control-group">
            <?php echo $form->labelEx($model, 'description', array('class' => 'control-label')); ?>
            <div class="controls">
                <?php echo $form->textArea($model, 'description', array('class' => 'span4', 'rows' => 5, 'cols' => 50)); ?>
                <p class="help-block"><?php echo $form->error($model, 'description'); ?></p>
            </div>
        </div>

        <input type="hidden" name="YII_CSRF_TOKEN"
               value="<?php echo Yii::app()->request->csrfToken; ?>"/>
        <input type="hidden" name= "parent_id" value="<?php echo isset($_POST['parent_id'])?$_POST['parent_id']:''; ?>"  />

        <?php  if (!$model->isNewRecord): ?>
        <input type="hidden" name="update_id"
               value="<?php echo $model->id; ?>"/>
        <?php endif; ?>
        <div class="control-group">
            <?php   echo CHtml::submitButton($model->isNewRecord ? Yii::t('global', 'Submit')
                                                     : Yii::t('global', 'Save'),
                                             array('class' => 'btn btn-large pull-right')); ?>
        </div>
</fieldset>
        <?php  $this->endWidget(); ?>
</div>
<!-- form -->


