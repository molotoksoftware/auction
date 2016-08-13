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

/** @var Controller $this */

Yii::app()->clientScript->registerScript(
    'users',
    "

   $('a.registration').click(function(){
        $('.shadow_wall_social').hide();
       $('.shadow_wall_reg').toggle();
       $(this).toggleClass('active');
       $('a.login').removeClass('active');
       $('.shadow_wall_auth').hide();
   });
   $('a.login').click(function(){
        $('.shadow_wall_social').hide();
       $('.shadow_wall_auth').toggle();
       $(this).toggleClass('active');
       $('a.registration').removeClass('active');
       $('.shadow_wall_reg').hide();
   });
   $('.shadow_wall_reg, .shadow_wall_auth').click(function(){
       $('a.registration, a.login').removeClass('active');
       $(this).hide();
   })
   $(document).keyup(function(e) {
     if (e.keyCode == 27) {
           $('a.registration, a.login').removeClass('active');
           $('.shadow_wall_reg, .shadow_wall_auth').hide();
       }
   });
   $('.registration_form').click(function(e){
      e.stopPropagation();
   });

   $('#btn-recovery').click(function() {
        $('#form-login').hide()
        $('#form-recovery').show();
        return false;
   });

   $('#btn-return-auth').click(function() {
        $('#form-login').show()
        $('#form-recovery').hide();
        return false;
   });
   
   // Открываем всплывающее окно для продолжения соц регистрации
   $('.soc_container a').click(function(){
        $('a.login').removeClass('active');
        $('a.registration').removeClass('active');
        $('.shadow_wall_auth').hide();
        $('.shadow_wall_reg').hide();
        
        $('.shadow_wall_social').show();
        
        $('#social_hid_type').val($(this).data('id'));
   });   
   
   ",
    CClientScript::POS_READY
);

?>
<div class="shadow_wall_reg">
    <div class="registration_form">
        <span class="span_angle"></span>
        <?php
        Yii::import('frontend.modules.user.models.*');
        $model = new RegistrationForm();
        /** @var CActiveForm $form */
        $form = $this->beginWidget(
            'CActiveForm',
            [
                'id'                     => 'form-registration',
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
                'action'                 => Yii::app()->createUrl('/user/user/registration'),
                'clientOptions'          => [
                    'validateOnSubmit' => true,
                    'errorCssClass'    => 'error-row',
                    'validateOnChange' => false,
                    'validateOnType'   => false,
                    'afterValidate'    => new CJavaScriptExpression('function($form, data, hasError) {
                        if (!hasError) {
                            if (submittedAction == "submit_send_code") {
                                if (data.success && data.step == "send_sms") {
                                    $form.find(".reg_submit__send_code").hide();
                                    $form.find(".sms_code_row").show();
                                    $form.find(".reg_submit__register").show();
                                } else {
                                    alert("Ошибка при отправке смс, попробуйте позже.");
                                }
                            } else if (submittedAction == "submit_register") {
                                if (data.success) {
                                    window.location.href = data.redirectUrl;
                                }
                            }
                        }
                        return false;
                    }'),
                ],
                'htmlOptions'            => [
                    'autocomplete' => 'off',
                    'class'        => 'form-popup',
                ],
            ]
        );
        ?>

        <div>
            <?php echo $form->label($model, 'email'); ?>
            <?php echo $form->textField($model, 'email'); ?>
            <?php echo $form->error($model, 'email'); ?>
            <?php echo $form->hiddenField($model, 'last_ip_addr'); ?>
            <?php echo $form->error($model, 'last_ip_addr'); ?>
        </div>
        <div>
            <?php echo $form->label($model, 'login'); ?>
            <?php echo $form->textField($model, 'login'); ?>
            <?php echo $form->error($model, 'login'); ?>
        </div>
        <div>
            <?php echo $form->label($model, 'password'); ?>
            <?php echo $form->passwordField($model, 'password'); ?>
            <?php echo $form->error($model, 'password'); ?>
        </div>
        <div>
            <?php echo $form->label($model, 'confirmPassword'); ?>
            <?php echo $form->passwordField($model, 'confirmPassword'); ?>
            <?php echo $form->error($model, 'confirmPassword'); ?>
        </div>

        <?php $this->widget('frontend.widgets.user.UserTelephoneFormRow', [
            'form'             => $form,
            'regFormModel'     => $model,
            'telephoneField'   => 'telephone',
            'countryCodeField' => 'telephone_country_code',
        ]); ?>

        <div class="div_check">
            <?php echo $form->error($model, 'agreeLicense'); ?>
            <?php echo $form->checkBox($model, 'agreeLicense'); ?>
            <label>Принимаю <a target="_blank"
                               href="<?= Yii::app()->createUrl('/page/view', array('alias' => 'rules')); ?>">пользовательское
                    соглашение</a></label>
        </div>
        <div class="sms_code_row" style="display: none;">
            <?php echo $form->label($model, 'sms_code'); ?>
            <?php echo $form->numberField($model, 'sms_code', array('maxlength' => 4)); ?>
            <?php echo $form->error($model, 'sms_code'); ?>
        </div>
        <div class="div_sub reg_submit__send_code">
            <?php echo CHtml::submitButton('Выслать проверочный код', ['name' => 'submit_send_code']); ?>
            <div class="clear"></div>
        </div>
        <div class="div_sub reg_submit__register" style="display: none;">
            <?php echo CHtml::submitButton('Зарегистрироваться', ['name' => 'submit_register']); ?>
            <div class="clear"></div>
        </div>
        <!--
        <div class="form_bottom">
            <span>Или войти с помощью:</span>
            <div class="soc_container">
                <a class="auth-link facebook fb" data-id="facebook"></a>
                <a class="auth-link vkontakte vk" data-id="vkontakte"></a>
                <a class="auth-link twitter tw" data-id="twitter"></a>
                <div class="clear"></div>
            </div>
            <?php //Yii::app()->eauth->renderWidget(array('action' => '/user/login')); ?>
        </div>
        -->
        <?php $this->endWidget(); ?>
    </div>
</div>
<div class="shadow_wall_auth">
    <div class="registration_form auth_form">
        <span class="span_angle"></span>
        <?php
        $model = new LoginForm();
        $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id' => 'form-login',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'action' => Yii::app()->createUrl('/user/user/login'),
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                    'errorCssClass' => 'error-row',
                    'validateOnChange' => false,
                    'validateOnType' => false,
                ),
                'htmlOptions' => array(
                    'autocomplete' => 'on',
                    'class' => 'form-popup'
                )
            )
        );
        ?>
        <? echo CHtml::hiddenField('returnUrl', Yii::app()->request->requestUri); ?>
        <div>
            <?php echo $form->label($model, 'login'); ?>
            <?php echo $form->textField($model, 'login'); ?>
            <?php echo $form->error($model, 'login'); ?>
        </div>
        <div>
            <?php echo $form->label($model, 'password'); ?>
            <?php echo $form->passwordField($model, 'password'); ?>
            <?php echo $form->error($model, 'password'); ?>
            <a id="btn-recovery" href="<?php echo Yii::app()->createUrl('/user/user/recovery'); ?>"> Забыли пароль?</a>
        </div>
        <div class="div_check">
            <?php echo $form->label($model, 'rememberMe'); ?>
            <?php echo $form->checkBox($model, 'rememberMe'); ?>
        </div>
        <div class="div_sub">
            <?php echo CHtml::submitButton('Войти'); ?>
            <div class="clear"></div>
        </div>
        <!--<div class="form_bottom">
            <span>Или войти с помощью:</span>
            <div class="soc_container">   
                <a class="auth-link facebook fb" data-id="facebook"></a>
                <a class="auth-link vkontakte vk" data-id="vkontakte"></a>
                <a class="auth-link twitter tw" data-id="twitter"></a>
                <div class="clear"></div>
            </div>
            <?php //Yii::app()->eauth->renderWidget(array('action' => '/user/user/login')); ?>
        </div>-->
        <?php $this->endWidget(); ?>

        <?php
        $recvModel = new RecoveryForm();

        $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id' => 'form-recovery',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'action' => Yii::app()->createUrl('/user/user/recovery'),
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                    'validateOnChange' => true,
                    'validateOnType' => false,
                    'errorCssClass' => 'error-row',
                    'afterValidate' => 'js:function(form, data, hasError) {
                        if (!hasError) {
                            alert("Инструкция по восстановлению пароля была отправлена на указанный вами при регистрации e-mail");
                            $("#RecoveryForm_email").val("");
                            $("#form-login").show()
                            $("#form-recovery").hide();
                        }
                    }'
                ),
                'htmlOptions' => array(
                    'autocomplete' => 'off',
                    'style' => 'display:none'
                )
            )
        );
        ?>
        <div>
            <?php echo $form->label($recvModel, 'email');?>
            <?php echo $form->textField($recvModel, 'email'); ?>
            <?php echo $form->error($recvModel, 'email')?>
            <div class="clear"></div>
        </div>

        <div class="div_sub">
            <?php echo CHtml::submitButton('отправить'); ?>
            <a id="btn-return-auth">Я передумал</a>

            <div class="clear"></div>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>
<div class="shadow_wall_social">
    <div class="registration_form">
        <span class="span_angle"></span>
        <?php
        Yii::import('frontend.modules.user.models.*');
        $model = new SocialForm();
        $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id' => 'form-social',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => Yii::app()->createUrl('/user/user/registration'),
                'clientOptions' => array(
                    'validateOnSubmit' => true,
                    'errorCssClass' => 'error-row',
                    'validateOnChange' => false,
                    'validateOnType' => false,
                    'afterValidate' => 'js:function(form,data,hasError){
                        if(!hasError){
                            var email = $("#SocialForm_email").val();
                            var telephone = $("#SocialForm_telephone").val();
                            
                            $.cookie("soc_email", email, {path: "/"});
                            $.cookie("soc_telephone", telephone, {path: "/"});
                            
                            var ser = $("#social_hid_type").val();
                            
                            window.location = "/login?service=" + ser;
                        }
                    }'
                ),
                'htmlOptions' => array(
                    'autocomplete' => 'off',
                    'class' => 'form-popup',
                    'style' => 'padding-bottom: 20px;'
                )
            )
        );
        ?>
        <input type="hidden" id="social_hid_type" />
        <div>
            <?php echo $form->label($model, 'email'); ?>
            <?php echo $form->textField($model, 'email'); ?>
            <?php echo $form->error($model, 'email'); ?>
        </div>
        <div>
            <?php echo $form->label($model, 'telephone'); ?>
            <?php
            $this->widget(
                'CMaskedTextField',
                array(
                    'model' => $model,
                    'attribute' => 'telephone',
                    'mask' => '+7-999-999-9999',
                    'placeholder' => '_'
                        . '',
                    'htmlOptions' => array(
                        'class' => 'inp_phone'
                    )
                )
            );
            ?>
            <?php echo $form->error($model, 'telephone'); ?>
        </div>
        <div class="div_sub" style="text-align: center; margin-top: 10px;">
            <?php echo CHtml::submitButton('Продолжить', array('style' => 'float: none;')); ?>
            <div class="clear"></div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>
<?php
$request = Yii::app()->getRequest();

cs()->registerScript(
    'percent-mask',
    '
    $("#form-registration").keypress(function(event) {
        if (event.which == "13") {
            event.preventDefault();
        }
    });

    var submittedAction = "";
    $("#form-registration input[name=submit_send_code], #form-registration input[name=submit_register]").on("click", function() {
        submittedAction = $(this).attr("name");
    });
    '
);
?>