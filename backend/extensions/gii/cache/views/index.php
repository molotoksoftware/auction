<h1>Cache Generator</h1>
<p>This generator helps you to quickly generate the skeleton code for a new cache class.</p>
<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>
<div class="row ygc-class-selector">
    <?php echo $form->labelEx($model,'baseClass'); ?>
    <?php echo $form->dropDownList($model,'baseClass',$model->getBaseClassNames(),array('style'=>'width:100%;')); ?>
    <div class="tooltip">
        This is the class that the new cache class will extend from.
        Please make sure the class exists and can be autoloaded.
    </div>
    <?php echo $form->error($model,'baseClass'); ?>
</div>
<?php
$this->widget('zii.widgets.jui.CJuiTabs', array(
    'tabs'=>array(
        'Component'=>array(
            'content' => $this->renderPartial('__class',array('model'=>$model,'form'=>$form),true),
            'id' => 'tab1'),
        'Information'=>array(
            'content' => $this->renderPartial('__infos',null,true),
            'id' => 'tab2'),
    ),
    'headerTemplate'=>'<li><a href="{url}">{title}</a></li>',
    'options'=>array(
        'collapsible'=>true,        
    ),
    'htmlOptions'=>array(
        'style'=>'width:100%;'
    ),
));
?>
<?php $this->endWidget(); ?>