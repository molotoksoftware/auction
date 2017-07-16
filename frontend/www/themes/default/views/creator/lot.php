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
/** @var FormCreateLot $model */
/** @var array $dependentCurrenciesData */

if (isset($_POST['options']) && !empty($_POST['options']) && isset($model->category_id) && !empty($model->category_id)) {
    $opt_post = 1;
} else {
    $opt_post = 0;
}

$type = get_class($model);

$cs = Yii::app()->clientScript;

$options = array(
    'csrfToken' => Yii::app()->request->csrfToken,
    'csrfTokenName' => Yii::app()->request->csrfTokenName,
    'dynamicCategoriesUrl' => Yii::app()->createUrl('/creator/dynamicCategoriesForSelect'),
    'downloadOptionsUrl' => Yii::app()->createUrl('/creator/getOptions'),
    'cat_1' => '#Cat1',
    'cat_2' => '#Cat2',
    'cat_3' => '#Cat3',
    'cat_4' => '#Cat4',
    'cat_5' => '#Cat5',
    'type' => $type,
    'select_cat_t' => Yii::t('basic', 'Please select the category'),
    'no_param' => Yii::t('basic', 'For this category of parameters to be determined'),
);

Yii::app()->clientScript->registerScriptFile(bu() . '/js/validate_text_range.js');
$opt = CJavaScript::encode($options);
Yii::app()->clientScript->registerScriptFile(bu() . '/js/creator.js', CClientScript::POS_END);

if (isset($_GET['tmp']) && !empty($_GET['tmp'])) {
    $cs->registerScript(
            'create-tr', 'changeTransaction();', CClientScript::POS_READY
    );
}

$cs->registerScript(
        'create-lot', '

    var creator = new Creator(' . $opt . ');
    creator.init();

    $("input[name=\'FormCreateLot[type_transaction]\']:radio").change(changeTransaction);

    var check_opt = "' . $opt_post . '";
    if (check_opt == 0)
    {
        var zz = $(".cat-list-block-wrp option:selected").last().val(); 
        $(".cat-list-block-wrp option[value="+zz+"]").trigger("change");
    }

', CClientScript::POS_READY
);

Yii::app()->clientScript->registerScriptFile(bu() . '/js/create_lot.js');

$cs->registerScript('tools-create-lot', '

//action change transaction
function changeTransaction(e) {
    var val = $("input[name=\"FormCreateLot[type_transaction]\"]:checked").val();
    switch(val){
        case "0":// Standart
            $("label[for=\"FormCreateLot_starting_price\"]")
                .html("'.Yii::t('basic','Starting price').'")
                .append(" <span class=\"required\">*</span>")
                .addClass("required");

            $("label[for=\"FormCreateLot_price\"]")
                .html("'.Yii::t('basic','Buy Now').'").removeClass("required").find("span.required");

            $("#FormCreateLot_starting_price").val(0).removeAttr("style");
            $("#starting_price_block").show();

            break;

        case "1"://fix price
            $("label[for=\"FormCreateLot_price\"]").html("'.Yii::t('basic','Buy Now').'").append(" <span class=\"required\">*</span>").addClass("required");
            $("#starting_price_block").hide();
            break;

        case "2":// from $1
            $("#FormCreateLot_starting_price").css({"border":"none","font-weight":"bold","color":"#009900","background" : "none"}).val("'.PriceHelper::formate(1).'");
            $("label[for=\"FormCreateLot_starting_price\"]").removeClass("required").find("span.required").remove();
            $("#FormCreateLot_starting_price").parent().show();
            break;

    }
}

function validatePrice(inp) {
    inp.value = inp.value.replace(/[^\d.]*/g, "")
                         .replace(/([.])[.]+/g, "$1")
                         .replace(/^[^\d]*(\d+([.]\d{0,5})?).*$/g, "$1");
}


', CClientScript::POS_END);

$seller = Yii::app()->user->getModel();
?>

<div class="container create_lot">

    <div class="row auction">
        <div class="col-xs-12">
            <h2><?= Yii::t('basic', 'Sell an item') ?></h2>
        </div>
    </div>
    <hr class="top10 horizontal_line">



    <?php
    /** @var CActiveForm $form */
    $form = $this->beginWidget(
            'CActiveForm', array(
        'id' => 'form-create-lot',
        'action' => $this->createUrl('/creator/lot'),
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'errorMessageCssClass' => 'error',
        'clientOptions' => array(
            'errorCssClass' => 'error-row',
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validateOnType' => false,
            'beforeValidate' => 'js:function() {

					if ($("input[name=\"userId\"]").val()==="") {
                        $(".qwik-link").click();
						return false;
					}

                    // check attributes
                    $(".cat-list-block p").html(""); var check_err_atr = 0;

                    $("div.mandat").each(function()
                    {
                        var atr_p = $(this).parent();
                        var main_type = $("p", atr_p).attr("class");

                        if (main_type == "m_type_1" || main_type == "m_type_9") {var atr_val = $("select", atr_p).val();}
                        if (main_type == "m_type_3") {var atr_val = $("input:radio:checked", atr_p).val();}
                        if (main_type == "m_type_4") {var atr_val = $("input:checkbox:checked", atr_p).val();}
                        if (main_type == "m_type_6") {var atr_val = $("input", atr_p).val();}
                        if (main_type == "m_type_7") {var atr_val = $("textarea", atr_p).val();}
                        if (main_type == "m_type_10") {var atr_val = $("input", atr_p).val();}

                        if (!atr_val)
                        {
                            check_err_atr = 1; $("p", atr_p).text("'.Yii::t('basic', 'You must select an attribute value').'");
                        }
                    });

                    if (check_err_atr == 1) {$("#Cat1").focus(); return false;} 

                    ////////
                    //editor.post();
                    var val = $("#FormCreateLot_starting_price").val();
                    if (val=="") {
                        val = 0;
                    }


                    return true;
                }',
        ),
        'focus' => array($model, 'name'),
        'htmlOptions' => array(
            'autocomplete' => 'off',
        ),
            )
    );
    ?>




    <?php $this->widget('frontend.widgets.imageUploader.ImageUploaderWidget', array()); ?>



    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Item title') ?>:</p>
        </div>
        <div class="col-xs-9 right_col">
            <?php echo $form->error($model, 'name'); ?>
            <?php echo $form->textField($model, 'name', array('class' => 'form-control width_input')); ?> 
        </div>
    </div>

    <?php
    $cat1_id = '';
    $cat2_id = '';
    $cat3_id = '';
    $cat4_id = '';
    $cat5_id = '';

    if (isset($model->category_id) && !empty($model->category_id)) {
        $base = new BaseAuction();
        $base->category_id = $model->category_id;

        $favourites_category = $base->getAncestorCategoryId();
        foreach ($favourites_category as $key => $value) {
            ${"cat" . ($key + 1) . "_id"} = $value;
        }
    }

    $cat_2_elements = Category::getCategoriesForSelect($cat1_id);
    $display_2 = (count($cat_2_elements) > 0) ? 'display:block' : 'display:none';
    $cat_3_elements = Category::getCategoriesForSelect($cat2_id);
    $display_3 = (count($cat_3_elements) > 0) ? 'display:block' : 'display:none';
    $cat_4_elements = Category::getCategoriesForSelect($cat3_id);
    $display_4 = (count($cat_4_elements) > 0) ? 'display:block' : 'display:none';
    $cat_5_elements = Category::getCategoriesForSelect($cat4_id);
    $display_5 = (count($cat_5_elements) > 0) ? 'display:block' : 'display:none';
    ?>

    <?php echo $form->hiddenField($model, 'category_id'); ?>
    <input type="hidden" name="hide_cats" id="hide_category_id" />

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Category') ?>:</p>
            <span><?= Yii::t('basic', 'Select a category') ?></span>
        </div>
        <div class="col-xs-9 right_col">
            <div class="cat-list-block">
                <div class="cat-list-block-label"><?= Yii::t('basic', 'Main category') ?></div>
                <?php echo $form->error($model, 'category_id'); ?>
                <?php
                echo Chtml::dropDownList('Cat1', $cat1_id, Category::getCategoriesForSelect(), [
                    'class' => 'cat-list form-control',
                    'id' => 'Cat1',
                    'style' => '',
                    'size' => 12
                ]);
                ?>
            </div>

            <div style="<?= $display_2; ?>" class="cat-list-block">
                <div class="cat-list-block-label"><?= Yii::t('basic', 'Subcategory') ?></div>
                <?php
                echo Chtml::dropDownList('Cat2', $cat2_id, $cat_2_elements, [
                    'class' => 'cat-list form-control',
                    'id' => 'Cat2',
                    'style' => $display_2,
                    'size' => 12
                ]);
                ?>
            </div>

            <div style="<?= $display_3; ?>" class="cat-list-block">
                <div class="cat-list-block-label"><?= Yii::t('basic', 'Subcategory') ?></div>
                <?php
                echo Chtml::dropDownList('Cat3', $cat3_id, $cat_3_elements, [
                    'class' => 'cat-list form-control',
                    'id' => 'Cat3',
                    'style' => $display_3,
                    'size' => 12
                ]);
                ?>
            </div>
            <div style="<?= $display_4; ?>" class="cat-list-block cat4_last">
            <div class="cat-list-block-label"><?= Yii::t('basic', 'Subcategory') ?></div>
                <?php
                echo Chtml::dropDownList('Cat4', $cat4_id, $cat_4_elements, [
                    'class' => 'cat-list form-control',
                    'id' => 'Cat4',
                    'style' => $display_4,
                    'size' => 12
                ]);
                ?>
            </div>
            <div style="<?= $display_5; ?>" class="cat-list-block cat4_last">
                <div class="cat-list-block-label"><?= Yii::t('basic', 'Subcategory') ?></div>
                <?php
                echo Chtml::dropDownList('Cat5', $cat5_id, $cat_5_elements, [
                    'class' => 'cat-list form-control',
                    'id' => 'Cat5',
                    'style' => $display_5,
                    'size' => 12
                ]);
                ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Additional options') ?></p>
        </div>

        <div class="col-xs-9 right_col" id="content-options-block">
            <?php if ($opt_post == 0): ?>
                <div id="content-options">
                    <?= Yii::t('basic', 'Please select the category') ?> ^
                </div>
            <?php elseif ($opt_post == 1): ?>
                <div id="content-options" style="padding: 0px 5px; background-color: rgb(241, 241, 241);">
                    <?php
                    $this->renderPartial(
                        '_options', array(
                            'options' => $ItemOptions,
                            'post' => $_POST['options']
                        )
                    );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Item description') ?></p>
            <span>
            </span>
        </div>
        <div class="col-xs-9 right_col">
            <?php echo $form->error($model, 'description'); ?>
            <?php
            Yii::import('backend.extensions.imperaviRedactor.ImperaviRedactorWidget');
            $this->widget('ImperaviRedactorWidget', array(
                'model' => $model,
                'attribute' => 'description',
                'options' => array(
                    'lang' => Yii::app()->language,
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
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Auction\'s type') ?></p>
        </div>
        <div class="col-xs-9 right_col">
            <?php echo $form->error($model, 'type_transaction'); ?>
            <?php
            echo CHtml::radioButtonList(
                    CHtml::activeName($model, 'type_transaction'), 0, array(
                Auction::TP_TR_STANDART => Yii::t('basic', 'Standart auction'),
                Auction::TP_TR_START_ONE => Yii::t('basic', 'From'). ' ' . PriceHelper::formate(1),
                Auction::TP_TR_SALE => Yii::t('basic', 'Fix price')
                    ), array(
                'id' => 'type_transaction',
                'template' => '<div class="radio-inline">{input}{label}</div>',
                'separator' => "\n"
                    )
            );
            ?>
            <div class="input_block">
                <div id="starting_price_block" class="div3">
                    <?php
                    echo $form->label($model, 'starting_price', [
                        'required' => true,
                        'label' => Yii::t('basic', 'Starting price')
                    ]);
                    ?><br>
                    <?php echo $form->textField($model, 'starting_price', ['class' => 'form-control width_input_short', 'onkeyup' => 'validatePrice(this)']); ?>
                    <?php echo $form->error($model, 'starting_price'); ?>

                </div>

                <div id="price_block" class="p_block">
                    <?php echo $form->label($model, 'price', ['label' => Yii::t('basic', 'Buy Now')]); ?><br>
                    <?php echo $form->textField($model, 'price', ['class' => 'form-control width_input_short', 'onkeyup' => 'validatePrice(this)']); ?>
                    <?php echo $form->error($model, 'price'); ?>

                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Items quantity') ?></p>
            <span></span>
        </div>
        <div class="col-xs-9 right_col">
            <?php echo $form->error($model, 'quantity'); ?>
            <?php echo $form->textField($model, 'quantity', ['class' => 'form-control width_input_short']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Duration') ?></p>
        </div>
        <div class="col-xs-9 right_col">
            <?php
            if (!isset($model->duration)) {
                $model->duration = 8;
            }
            echo Chtml::activeDropDownList(
                    $model, 'duration', Auction::getDurationList(), array(
                'empty' => Yii::t('basic', ' - select period - '),
                'class' => 'form-control width_input_short'
                    )
            );
            ?>
            <?php echo $form->error($model, 'duration'); ?>

            <div class="checkbox">
                <?php echo Chtml::activeCheckBox($model, 'is_auto_republish', ['style' => 'margin-left:0px']); ?> 
                <?= CHtml::activeLabel($model, 'is_auto_republish', array('label' => Yii::t('basic', 'Automatically republish item if buyer is not found'))) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Location') ?></p>
            <span></span>
        </div>
        <div class="col-xs-9 right_col">
            <?php $this->widget('frontend.widgets.citySelector.CitySelectorWidget', array('model' => $model)); ?>
            <?php echo $form->error($model, 'id_city'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'More contacts') ?></p>
            <span></span>
        </div>
        <div class="col-xs-9 right_col">
            <?php
            $model->contacts = $seller->add_contact_info;
            echo $form->textArea($model, 'contacts', ['class' => 'form-control width_input text_area']);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 left_col">
            <p><?= Yii::t('basic', 'Shipping terms') ?></p>
            <span></span>
        </div>
        <div class="col-xs-9 right_col">
            <?php
            $model->conditions_transfer = $seller->terms_delivery;
            echo $form->textArea($model, 'conditions_transfer', ['class' => 'form-control width_input text_area']);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3 left_col">
        </div>
        <div class="col-xs-9 right_col">
            <?php
            echo $form->errorSummary(
                    $model, '<span class="head_error">' . Yii::t('basic', 'Errors') . ':</span>', '<p></p>', array('class' => 'error_container')
            );
            ?>
            <?php echo CHtml::submitButton(Yii::t('basic', 'Submit Auction'), array('class' => 'btn btn-success')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>
