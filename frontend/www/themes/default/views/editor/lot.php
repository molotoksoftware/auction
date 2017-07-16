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
/** @var Auction $model */
/** @var array $dependentCurrenciesData */
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
    'type' => $type
);

Yii::app()->clientScript->registerScriptFile(bu() . '/js/validate_text_range.js');
Yii::app()->clientScript->registerScriptFile(bu() . '/js/creator.js', CClientScript::POS_END);


$opt = CJavaScript::encode($options);
$cs->registerScriptFile(bu() . '/js/creator.js', CClientScript::POS_END);
$cs->registerScript(
        'create-lot', '

    var creator = new Creator(' . $opt . ');
    creator.init();

    changeTransaction();
    $("input[name=\'Auction[type_transaction]\']:radio").change(changeTransaction);
    


', CClientScript::POS_READY
);



$cs->registerScript('tools-create-lot', '

function changeTransaction(e) {
    var val = $("input[name=\"Auction[type_transaction]\"]:checked").val();
    
        switch(val){
        case "0":
        $("label[for=\"Auction_starting_price\"]")
                .html("'.Yii::t('basic','Starting price').'")
                .append(" <span class=\"required\">*</span>")
                .addClass("required");
                
        $("label[for=\"Auction_price\"]")
        .html("'.Yii::t('basic','Buy Now').'").removeClass("required").find("span.required");
                
        $("#Auction_starting_price").removeAttr("style");
            $("#starting_price_block").show();
            
            break;
        
        case "1":// Buy now
            $("label[for=\"Auction_price\"]").html("'.Yii::t('basic','Buy Now').'").append(" <span class=\"required\">*</span>")
                 .addClass("required");
            $("#starting_price_block").hide();
            
            break;
        case "2":// From 1

            $("#Auction_starting_price").css({"border":"none","font-weight":"bold","color":"#009900","background" : "none"}).val("'.PriceHelper::formate(1).'");
            $("label[for=\"Auction_starting_price\"]").removeClass("required").find("span.required").remove();
            $("#Auction_starting_price").parent().show();
            break;

    }

}

function validatePrice(inp) {
    inp.value = inp.value.replace(/[^\d.]*/g, "")
                         .replace(/([.])[.]+/g, "$1")
                         .replace(/^[^\d]*(\d+([.]\d{0,5})?).*$/g, "$1");
}
', CClientScript::POS_END);

/*
$cs->registerScript('tools-create-lot', $js, CClientScript::POS_END);
*/

$seller = Yii::app()->user->getModel();
?>

<div class="container create_lot">

    <div class="row auction">
        <div class="col-xs-9">
            <h2><?= Yii::t('basic', 'Edit item') ?></h2>
        </div>
    </div>
    <hr class="top10 horizontal_line">

    <?php
    /** @var CActiveForm $form */
    $form = $this->beginWidget(
            'CActiveForm', array(
        'id' => 'form-create-lot',
        'action' => $this->createUrl('/editor/lot/id/' . $model->auction_id),
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'errorMessageCssClass' => 'error',
        'clientOptions' => array(
            'errorCssClass' => 'error-row',
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validateOnType' => false,
            'beforeValidate' => 'js:function(){
                
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
                var val = $("#' . CHtml::activeId($model, 'starting_price') . '").val();
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


    <?php $this->widget('frontend.widgets.imageUploader.ImageUploaderWidget', ['model' => $model]); ?>


    <?php if ($model->status == 7) : ?>
        <div class="row action_lot">
            <div class="col-xs-3 left_col">
                <p><?= Yii::t('basic', 'Put up for sale?')?></p>
            </div>
            <div class="col-xs-9 right_col">

                <?php
                $to_status = (isset($_POST['to_status']) && $_POST['to_status'] == 4) ? 4 : 1;
                echo CHtml::dropDownList(
                        'to_status', $to_status, array(
                    1 => Yii::t('basic', 'Yes'),
                    4 => Yii::t('basic', 'No')
                        ), ['class' => 'form-control width_input']
                );
                ?>

            </div>
        </div>
    <?php else: ?>
        <div class="row action_lot">
            <div class="col-xs-3 left_col">
                <p><?= Yii::t('basic', 'Actions')?></p>
            </div>
            <div class="col-xs-9 right_col">

                <?php
                $refreshDef = (isset($_GET['strepub']) && $_GET['strepub'] == 1) ? 1 : 0;
                echo CHtml::dropDownList(
                        'refresh', $refreshDef, array(
                    0 => Yii::t('basic', 'Update'),
                    1 => Yii::t('basic', 'Update and republish')
                        ), ['class' => 'form-control width_input']
                );
                ?>

            </div>
        </div>
    <?php endif;  ?>

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

    $favourites_category = $model->getAncestorCategoryId();
    foreach ($favourites_category as $key => $value) {
        ${"cat" . ($key + 1) . "_id"} = $value;
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
            <?php echo $form->error($model, 'category_id'); ?>
            <div class="cat-list-block">
                <div class="cat-list-block-label"><?= Yii::t('basic', 'Main category') ?></div>
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

            <div id="content-options" style="padding: 0px 5px; background-color: rgb(241, 241, 241);">
                <?php
                $this->renderPartial(
                        '_options', array(
                    'options' => $ItemOptions,
                    'selected' => true,
                    'auction_id' => $model->auction_id
                        )
                );
                ?>
            </div>

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
                'attribute' => 'text',
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
                    CHtml::activeName($model, 'type_transaction'), $model->type_transaction, array(
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
            <?php echo CHtml::submitButton(Yii::t('basic', 'Save'), array('class' => 'btn btn-success')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>

</div>