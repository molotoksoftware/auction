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

$this->pageTitle = 'Редактирование лота';
$this->breadcrumbs = array(
    array(
        'icon' => 'icon-folder-open',
        'label' => 'Каталог',
        'url' => array('/catalog/category/index'),
    ),
    array(
        'icon' => 'icon-legal',
        'label' => 'Лоты',
        'url' => array('/catalog/auction/index'),
    ),
    array(
        'icon' => 'icon-pencil',
        'label' => 'Редактирование лота',
        'url' => '',
    ),
);
$type = get_class($model);
?>
<?php $this->renderPartial('_scripts_lot'); ?>


<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-pencil"></i> Редактирование лота</span>
                    <ul class="nav nav-tabs nav-tabs-right">
                        <li>
                            <a rel="tooltip" data-original-title="Вернуться"
                               href="<?= Yii::app()->createUrl('/catalog/auction/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="box-content">
                    <?php
                    $form = $this->beginWidget(
                        'bootstrap.widgets.TbActiveForm',
                        array(
                            'id' => 'form-auction',
                            'type' => 'horizontal',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => true,
                            'clientOptions' => array(
                                'validateOnSubmit' => true,
                                'validateOnChange' => false,
                                'validateOnType' => false,
                                'beforeValidate' => 'js:function(){
                                    $("#' . $type . '_starting_price").val(parseInt($("#Auction_starting_price").val()));
                                    return true;
                                }'
                            ),
                            'focus' => array($model, 'name'),
                            'htmlOptions' => array(
                                'autocomplete' => 'off',
                                //'enctype' => 'multipart/form-data'
                            ),
                        )
                    );
                    ?>

                    <div class="padded">
                        <?php
                        $date = new DateTime('now');
                        $date_end = new DateTime($model->bidding_date);
                        $interval = $date->diff($date_end);
                        $days = '';
                        $f = $interval->format('%R%');
                        ?>
                        <div class="alert <?= ($f == '-') ? 'alert-error' : 'alert-success'; ?>">
                            <i class=" icon-calendar"></i>Опубликовано:
                            <?= ' ' . Yii::app()->dateFormatter->format('dd MMMM yyyy H:mm:ss', $model->created); ?>
                            <br/>

                            <i class=" icon-time"></i>До окончания торгов:
                            <?php
                            if ($f == '-') {
                                echo '<b>завершенный лот</b>, дата окончания: ' . Yii::app()->dateFormatter->format(
                                        'dd MMMM yyyy H:m:s',
                                        $model->bidding_date
                                    );
                            } else {
                                if ($interval->format('%a') > 0) {
                                    $days = $interval->format('%a') . ' <span>' . Yii::t(
                                            'app',
                                            'day|days',
                                            $interval->format('%a')
                                        ) . '</span>';
                                }
                                $time = $interval->format('%H:%I');
                                echo $days . ' ' . $time;
                            }
                            ?>
                            <br/>
                            <br/>
                            Статус: <b><?= $model->getStatus(); ?></b>
                        </div>

                        <?php echo $form->errorSummary($model); ?>

                        <div class="well">

                            <div class="control-group ">
                                <label for="duration" class="control-label">Перевыставить?</label>
                                <div class="controls">
                                    <?php
                                    echo CHtml::dropDownList(
                                        'refresh',
                                        '',
                                        array(
                                            0 => 'Редактировать только информацию',
                                            1 => 'Перевыставить лот заново'
                                        )
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="control-group ">
                                <label for="duration" class="control-label">Состояние</label>
                                <div class="controls">
                                    <?php echo $form->dropDownList(
                                        $model,
                                        'status',
                                        array(
                                            1 => 'активный',
                                            4 => 'завершен'
                                        )
                                    ); ?>
                                </div>
                            </div>
                        </div>

                        <?php
                        echo $form->textFieldRow(
                            $model,
                            'name',
                            array(
                                'class' => 'span8'
                            )
                        );
                        ?>


                        <div class="control-group">
                            <label class="control-label required required" for="Auction_text">Описание <span
                                    class="required">*</span></label>
                            <div class="controls">
                                <div class="redactor_box">
                                    <?php
                                    Yii::import('backend.extensions.imperaviRedactor.ImperaviRedactorWidget');
                                    $this->widget('ImperaviRedactorWidget', array(
                                        'model' => $model,
                                        'attribute' => 'text',
                                        'options' => array(
                                            'lang' => 'ru',
                                            'convertVideoLinks' => 'true',
                                            'buttons' => array('formatting', '|', 'bold', 'italic', 'deleted', '|', 'image', 'video'),
                                            'iframe' => true,
                                            'minHeight' => 150
                                        ),
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php $form->error($model, 'category_id'); ?>
                        <?php echo $form->hiddenField($model, 'category_id'); ?>
                        <!-- ........................... КАТЕГОРИИ ........................... -->
                        <div id="cnt-categories">
                            <div class="row-fluid">
                                <div class="span4">
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

                                    echo Chtml::dropDownList(
                                        'Cat1',
                                        $cat1_id,
                                        Category::getCategoriesForSelect(),
                                        array(
                                            'class' => 'span12',
                                            'ajax' => array(
                                                'type' => 'GET',
                                                'dataType' => 'json',
                                                'url' => $this->createUrl('/catalog/auction/dynamicCategoriesForSelect'),
                                                'data' => new CJavaScriptExpression(
                                                    '{"cat_id":$(this).find("option:selected").val(), "level":2}'),
                                                'beforeSend' => 'js:function(){
                                                $("#Auction_category_id").val("");
                                                $("#Cat2").find("option:selected").html("идет загрузка категорий...");
                                                $("#Cat2").attr("disabled", true);
                                                $("#Cat3").hide();
						$("#Cat4").hide();
						$("#Cat5").hide();
                                                hideOptions();     
                                            }',
                                                'success' => 'js:function(data) {
                                                if (data.isSubCategories) {
                                                    $("#Cat2").html(data.options);
                                                    $("#Cat2").show();
                                                    $("#Cat2").attr("disabled", false);
                                                } else {
                                                    $("#Cat2").hide();
                                                    downloadOptions(cat_id);
                                                    cat_id = $("#Cat1").find("option:selected").val();
                                                    $("#' . $type . '_category_id").val(cat_id);
                                                    
                                                }
                                            }'
                                            ),
                                            'id' => 'Cat1',
                                            'style' => '',
                                            'onchange' => new CJavaScriptExpression('
                                            cat_id = $("#Cat1").find("option:selected").val();
                                            $("#' . $type . '_category_id").val(cat_id);'),
                                            'empty' => '- выберите категорию -'
                                        )
                                    );
                                    ?>
                                </div>
                                <div class="span4">
                                    <?php
                                    $cat_2_elements = Category::getCategoriesForSelect($cat1_id);
                                    echo Chtml::dropDownList(
                                        'Cat2',
                                        $cat2_id,
                                        $cat_2_elements,
                                        array(
                                            'class' => 'span12',
                                            'ajax' => array(
                                                'type' => 'GET',
                                                'dataType' => 'json',
                                                'url' => $this->createUrl('/catalog/auction/dynamicCategoriesForSelect'),
                                                'data' => new CJavaScriptExpression(
                                                    '{"cat_id":$(this).find("option:selected").val(), "level":3}'),
                                                'beforeSend' => 'js:function(){
                                                $("#Cat3").hide(); 
                                                $("#Cat4").hide(); 
                                                $("#Cat5").hide();
                                                $("#Cat3").find("option:selected").html("идет загрузка категорий...");
                                                $("#Cat3").attr("disabled", true);
                                                $("#' . $type . '_category_id").val("");
                                                hideOptions();
                                            }',
                                                'success' => 'js:function(data){
                                                if (data.isSubCategories) {
                                                    $("#Cat3").html(data.options);
                                                    $("#Cat3").show();
                                                    $("#Cat3").attr("disabled",false);                                                    
                                                } else {
                                                    cat_id = $("#Cat2").find("option:selected").val();
                                                    $("#' . $type . '_category_id").val(cat_id);
                                                    downloadOptions(cat_id);
                                                }
                                            }'
                                            ),
                                            'id' => 'Cat2',
                                            'style' => (count($cat_2_elements) > 0) ? 'display:block' : 'display:none',
                                            'empty' => '- выберите категорию -'
                                        )
                                    );
                                    ?>
                                </div>
                                <div class="span4">
                                    <?php
                                    $cat_3_elements = Category::getCategoriesForSelect($cat2_id);
                                    echo Chtml::dropDownList(
                                        'Cat3',
                                        $cat3_id,
                                        $cat_3_elements,
                                        array(
                                            'class' => 'span12',
                                            'ajax' => array(
                                                'type' => 'GET',
                                                'dataType' => 'json',
                                                'url' => $this->createUrl('/catalog/auction/dynamicCategoriesForSelect'),
                                                'data' => new CJavaScriptExpression(
                                                    '{"cat_id":$(this).find("option:selected").val(), "level":4}'),
                                                'beforeSend' => 'js:function(){
                                                $("#Cat4").hide(); $("#Cat5").hide();
                                                $("#Cat4").find("option:selected").html("идет загрузка категорий...");
                                                $("#Cat4").attr("disabled", true);
                                                $("#' . $type . '_category_id").val("");
                                                hideOptions();
                                            }',
                                                'success' => 'js:function(data){
                                                if (data.isSubCategories) {
                                                    $("#Cat4").html(data.options);
                                                    $("#Cat4").show();
                                                    $("#Cat4").attr("disabled",false);                                                    
                                                } else {
                                                    cat_id = $("#Cat3").find("option:selected").val();
                                                    $("#' . $type . '_category_id").val(cat_id);
                                                    downloadOptions(cat_id);
                                                }
                                            }'
                                            ),
                                            'id' => 'Cat3',
                                            'style' => (count($cat_3_elements) > 0) ? 'display:block' : 'display:none',
                                            'empty' => '- выберите категорию -'
                                        )
                                    );
                                    ?>
                                </div>
                                <div class="span4" style="margin-left: 0px;margin-top: 10px;">
                                    <?php
                                    $cat_4_elements = Category::getCategoriesForSelect($cat3_id);
                                    echo Chtml::dropDownList(
                                        'Cat4',
                                        $cat4_id,
                                        $cat_4_elements,
                                        array(
                                            'class' => 'span12',
                                            'ajax' => array(
                                                'type' => 'GET',
                                                'dataType' => 'json',
                                                'url' => $this->createUrl('/catalog/auction/dynamicCategoriesForSelect'),
                                                'data' => new CJavaScriptExpression(
                                                    '{"cat_id":$(this).find("option:selected").val(), "level":5}'),
                                                'beforeSend' => 'js:function(){
                                                $("#Cat5").hide();
                                                $("#Cat5").find("option:selected").html("идет загрузка категорий...");
                                                $("#Cat5").attr("disabled", true);
                                                $("#' . $type . '_category_id").val("");
                                                hideOptions();
                                            }',
                                                'success' => 'js:function(data){
                                                if (data.isSubCategories) {
                                                    $("#Cat5").html(data.options);
                                                    $("#Cat5").show();
                                                    $("#Cat5").attr("disabled",false);                                                    
                                                } else {
                                                    cat_id = $("#Cat4").find("option:selected").val();
                                                    $("#' . $type . '_category_id").val(cat_id);
                                                    downloadOptions(cat_id);
                                                }
                                            }'
                                            ),
                                            'id' => 'Cat4',
                                            'style' => (count($cat_4_elements) > 0) ? 'display:block' : 'display:none',
                                            'empty' => '- выберите категорию -'
                                        )
                                    );
                                    ?>
                                </div>
                                <div class="span4" style="margin-top: 10px;">
                                    <?php
                                    $cat_5_elements = Category::getCategoriesForSelect($cat4_id);
                                    echo Chtml::dropDownList(
                                        'Cat5',
                                        $cat5_id,
                                        $cat_5_elements,
                                        array(
                                            'id' => 'Cat5',
                                            'class' => 'span12',
                                            'style' => (count($cat_5_elements) > 0) ? 'display:block' : 'display:none',
                                            'empty' => '- выберите категорию -',
                                            'onchange' => new CJavaScriptExpression('
                                            cat_id = $("#Cat5").find("option:selected").val();
                                            $("#' . $type . '_category_id").val(cat_id);
                                            downloadOptions(cat_id);')
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- ........................... КАТЕГОРИИ ........................... -->


                        <div id="content-options"
                             style="<?= (count($options) > 0) ? 'display: block' : 'display: none'; ?>;">
                            <?php
                            $this->renderPartial(
                                '_options',
                                array(
                                    'options' => $options,
                                    'selected' => true,
                                    'auction_id' => $model->auction_id
                                )
                            );
                            ?>
                        </div>
                        <?php
                        echo $form->textFieldRow(
                            $model,
                            'quantity',
                            array(
                                'class' => '',
                                'hint' => 'Количество товара в единицах (целое число)'
                            )
                        );
                        ?>


                        <div class="control-group ">
                            <label for="type_transaction" class="control-label">Тип аукциона и цена</label>

                            <div class="controls">
                                <?php
                                echo CHtml::activeRadioButtonList(
                                    $model,
                                    'type_transaction',
                                    array(
                                        Auction::TP_TR_STANDART => 'Стандартный',
                                        Auction::TP_TR_START_ONE => 'От ' . PriceHelper::formate(1),
                                        Auction::TP_TR_SALE => 'Фиксированная цена'
                                    ),
                                    array(
                                        'id' => 'type_transaction',
                                        'separator' => '',
                                        'template' => '<label class="radio inline">{input} {label}</label>'
                                    )
                                );
                                ?>
                            </div>
                        </div>
                        <?php echo $form->textFieldRow($model, 'starting_price'); ?>
                        <?php echo $form->textFieldRow($model, 'price'); ?>
                        <div class="control-group ">
                            <label for="duration" class="control-label">Продолжительность торгов</label>

                            <div class="controls">
                                <?php echo Chtml::activeDropDownList(
                                    $model,
                                    'duration',
                                    Auction::getDurationList(),
                                    array('empty' => ' - выберите период - ')
                                ); ?>
                                <?php echo $form->error($model, 'duration'); ?>
                            </div>
                        </div>


                        <?php echo $form->textAreaRow($model, 'conditions_transfer', array('row' => 60, 'cols' => 3, 'class' => 'span8')); ?>


                        <?php

                        echo $form->textFieldRow(
                            $model,
                            'owner',
                            array(
                                'class' => 'span8'
                            )
                        );
                        ?>




                        <?php

                        //таблица ставок
                        $this->renderPartial(
                            '_table_bids',
                            array(
                                'model' => $model
                            )
                        );

                        ?>

                        <?php
                        /*
                        //таблица вопросов
                        $this->renderPartial(
                            '_table_questions',
                            array(
                                'model' => $model
                            )
                        );
                         *
                         */
                        ?>


                    </div>
                    <!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right">
                            <?php
                            echo CHtml::link(
                                '<span class="icon-circle-arrow-left"></span> Вернуться',
                                '/catalog/auction/index',
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
                                        'value' => 'index',
                                        'name' => 'submit',
                                    ),
                                    'size' => 'small',
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <?php $this->endWidget(); ?>
                </div>
                <!-- end box -->

            </div>
            <!-- end box content -->
        </div>
    </div>
</div><!-- row-fluid-->
</div><!--container-fluid-->