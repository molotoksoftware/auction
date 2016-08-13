<h1>Log Route Generator</h1>
<p>This generator helps you to quickly generate the skeleton code for a new routing class.</p>
<?php $form=$this->beginWidget('CCodeForm', array('model'=>$model)); ?>
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