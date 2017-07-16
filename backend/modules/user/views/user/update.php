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


$this->pageTitle = 'Пользователи';

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-user',
        'label' => 'Пользователи',
        'url' => array('/user/user/index'),
    ),
    array(
        'icon' => 'icon-plus',
        'label' => 'Редактировани',
        'url' => '',
    )
);
/**
 * @var $model User
 */
?>

<?php
Yii::app()->clientScript->registerScript(
    'eventEditBalance',"

            $('.edit_balance').on('click', '#editBalance', function(){

                var tog = $('#i_change_balance');
                var tog_val = tog.val();

                if (tog_val == 0) {
                    var input =  $('#User_changeBalance');
                    input.removeClass('lock-input');
                    input.attr('readonly',false);
                    $('#comment_change_balance').show();
                    tog.val(1); tog_val = 1;
                } else if (tog_val == 1) {
                    var input =  $('#User_changeBalance');
                    input.addClass('lock-input');
                    input.attr('readonly',true);
                    $('#comment_change_balance').hide();
                    tog.val(0); tog_val = 0;

                }
                return false; 
            });


 


   ",
    CClientScript::POS_READY
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">

            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-plus"></i> Редактирование</span>
                    <ul class="box-toolbar">
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться"
                               href="<?= Yii::app()->createUrl('/user/user/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget(
                        'bootstrap.widgets.TbActiveForm',
                        array(
                            'id' => 'form-user',
                            'type' => 'horizontal',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => true,
                            'clientOptions' => array(
                                'validateOnSubmit' => true,
                                'validateOnChange' => false,
                                'validateOnType' => false,
                            ),
                            'focus' => array($model, 'firstname'),
                            'htmlOptions' => array(
                                'enctype' => 'multipart/form-data'
                            ),
                        )
                    );
                    ?>
                    <pre>
                    </pre>
                    <div class="padded">
                        <?php echo $form->textFieldRow($model, 'firstname'); ?>
                        <?php echo $form->textFieldRow($model, 'lastname'); ?>
                        <?php echo $form->textFieldRow($model, 'email'); ?>
                        <?php echo $form->textFieldRow($model, 'login'); ?>
                        <?php echo $form->textFieldRow($model, 'nick'); ?>
                        <?php echo $form->textFieldRow($model, 'password'); ?>
                        <?php echo $form->textFieldRow($model, 'telephone'); ?>
                        <?php echo $form->textFieldRow($model, 'rating'); ?>
                        <?php echo $form->textFieldRow($model, 'last_ip_addr'); ?>

                        <?php
                        $this->widget(
                            'backend.extensions.simpleImageUpload.SimpleImageUploadWidget',
                            array(
                                'model' => $model,
                                'form' => $form,
                                'attribute' => 'avatar',
                                'versionName' => 'preview'
                            )
                        );
                        ?>

                        <?php echo $form->toggleButtonRow($model, 'certified'); ?>
                        <?php echo $form->toggleButtonRow($model, 'ban'); ?>

                        <div class="control-group ">
                            <label for="Participant_current_balanc" class="control-label">Текущий баланс.</label>
                            <div class="controls">
                                <p>
                                    <div class="label"><?php echo PriceHelper::formate($model->getBalance()); ?></div>
                                </p>
                            </div>
                        </div>


                        <!-- CHANGE BALANCE -->
                        <?php echo CHtml::activeHiddenField(
                            $model,
                            'is_change_balance',
                            array('value' => 0, 'id' => 'i_change_balance')
                        ); ?>

                        <div class="control-group ">
                            <div class="note large">
                                <i class="icon-exclamation-sign"></i> Эта сумма будет прибавлена к балансу агента (в
                                случае отрицательного числа - вычтена)
                            </div>
                            <label for="Participant_changeBalance" class="control-label">Изменить баланс</label>

                            <div class="controls edit_balance">
                                <?php echo $form->textField(
                                    $model,
                                    'changeBalance',
                                    array('class' => 'lock-input', 'readonly' => 'readonly')
                                ); ?>
                                <a rel="tooltip" data-original-title="Изменить баланс" href="#" id="editBalance">
                                    <i class="icon-pencil"></i></a>
                                <?php echo $form->error($model, 'changeBalance'); ?>
                            </div>

                            <div id="comment_change_balance" style="display: none;">
                                <br/>

                                <div class="controls">
                                    <?php echo $form->textArea(
                                        $model,
                                        'balance_comment',
                                        array('placeholder' => 'коментарий')
                                    ); ?>
                                    <?php echo $form->error($model, 'balance_comment'); ?>
                                </div>
                            </div>

                        </div>
                        <!-- END CHANGE BALANCE -->


                        <div class="control-group ">
                            <?php echo CHtml::activeLabel($model, 'birthday', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?php
                                $date = date('d-m-Y', strtotime($model->birthday));
                                $this->widget(
                                    'zii.widgets.jui.CJuiDatePicker',
                                    array(
                                        'name' => CHtml::activeName($model, 'birthday'),
                                        'options' => array(
                                            // 'showAnim'=>'fold',
                                            'dateFormat' => 'dd-mm-yy',
                                        ),
                                        'language' => 'ru',
                                        'value' => $date,
                                        'htmlOptions' => array(
                                            'style' => 'width:130px;',
                                            'autocomplete' => "off"
                                        )
                                    )
                                );
                                ?>
                            </div>
                        </div>


                    </div>
                    <!--end paped -->
                    <div class="form-actions">


                        <div class="pull-right">
                            <?php
                            echo CHtml::link(
                                '<span class="icon-circle-arrow-left"></span> Вернуться',
                                '/admin/user/index',
                                array(
                                    'class' => 'link'
                                )
                            );
                            ?>
                            <?php
                            $this->widget(
                                'bootstrap.widgets.TbButton',
                                array(
                                    'buttonType' => 'submit',
                                    'label' => 'Сохранить',
                                    'type' => null,
                                    'htmlOptions' => array(
                                        'class' => 'btn btn-blue',
                                        'value' => 'save',
                                        'name' => 'submit',
                                    ),
                                    'size' => 'small'
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <?php $this->endWidget(); ?>
                </div>
                <!-- end box content -->
            </div>
        </div>
    </div>
    <!-- row-fluid-->
</div><!--container-fluid-->