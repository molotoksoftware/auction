<?php
$this->pageTitle = 'Редактирование новости "' . $model->title . '"';

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-leaf',
        'label' => 'Новости',
        'url' => array('/news/news/index'),
    ),
    array(
        'icon' => 'icon-cogs',
        'label' => 'Редактирование новости',
        'url' => '',
    )
);
?>

<div class="container-fluid padded">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="box-header">
                    <span class="title"><i class="icon-pencil"></i>  Редактирование новости</span>

                    <ul class="box-toolbar">
                        <li>                            
                            <a rel="tooltip" data-original-title="Вернуться" href="<?= Yii::app()->createUrl('/news/news/index'); ?>"><i class="icon-reply"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="box-content">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                        'id' => 'form-news',
                        'type' => 'vertical',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                        ),
                        'focus' => array($model, 'title'),
                        'htmlOptions' => array(
                            'enctype' => 'multipart/form-data'
                        ),
                            ));
                    ?>
                    <div class="padded">
                        <?php echo $form->errorSummary($model); ?>
                        <?php
                        echo $form->textFieldRow($model, 'title', array(
                            'class' => 'span8'
                        ));
                        ?>
                        
                        <div class="control-group ">
                            <label class="control-label" for="<?php echo CHtml::activeId($model, 'date') ?>"><?php echo $model->getAttributeLabel('date'); ?></label>
                            <div class="controls">
                                <?php
                                $date = date('d-m-Y', strtotime($model->date));
                                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                                    'name' => CHtml::activeName($model, 'date'),
                                    'options' => array(
                                        // 'showAnim'=>'fold',
                                        'dateFormat' => 'dd-mm-yy',
                                    ),
                                    'language' => 'ru',
                                    'value' => $date,
                                    'htmlOptions' => array(
                                        'style' => 'width:100px;')
                                ));
                                ?>
                            </div>
                        </div>
                        

                        <?php
                        echo $form->textAreaRow($model, 'short_description', array(
                            'class' => 'span8'
                        ));
                        ?>

                        <?php
                        Yii::import('ext.redactor.RedactorWidget');
                        $this->widget('RedactorWidget', array(
                            'model' => $model,
                            'attribute' => 'content',
                        ));
                        ?>
                        <?php $form->error($model, 'content'); ?>

                        <hr/>
                        <?php echo $form->toggleButtonRow($model, 'status'); ?>
                        
                        <?php
                        $this->widget('backend.extensions.simpleImageUpload.SimpleImageUploadWidget', array(
                            'model' => $model,
                            'form' => $form,
                            'attribute' => 'images'
                        ));
                        ?>

                        <?php
                        $this->widget('common.extensions.seo.widgets.SeoWidget', array(
                            'model' => $model,
                            'titleAttribute' => 'meta_title',
                            'descriptionAttribute' => 'meta_description',
                            'keywordsAttribute' => 'meta_keywords'
                        ));
                        ?>
                    </div><!--end paped -->
                    <div class="form-actions">
                        <div class="pull-right"> 
                            <?php
                            echo CHtml::link('<span class="icon-circle-arrow-left"></span> Вернуться', '/admin/news/index', array(
                                'class' => 'link'
                            ));
                            ?>
                            <?php
                            $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType' => 'submit',
                                'label' => 'Сохранить',
                                'type' => null,
                                'htmlOptions' => array(
                                    'class' => 'btn btn-blue',
                                    'value' => 'save',
                                    'name' => 'submit',
                                ),
                                'size' => 'small'
                            ));
                            ?>
                        </div>
                    </div>
<?php $this->endWidget(); ?>
                </div><!-- end box content -->
            </div>
        </div>
    </div><!-- row-fluid-->
</div><!--container-fluid-->