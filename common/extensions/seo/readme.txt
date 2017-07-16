Then add behavior to model

public function behaviors() {
	return array(
              'seo' => array(
                'class' => 'ext.seo-behavior.SeoBehavior',
              ),
              ...
	);
}

In _form view for this model, insert ESEOEditWidget

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sample-form',
)); ?>
...
        <?php
             $this->widget('common.extensions.seo.widgets.SeoWidget', array(
                'model' => $model,
             ));
        ?>
...
<?php $this->endWidget(); ?>

Add ESEOControllerBehavior to controller

public function behaviors() {
	return array(
              'SeoBehavior' => array(
                     'class' => 'common.extensions.seo.SeoControllerBehavior'
              ),
              ...
	);
}

And last that you need to do - add folowing code to your controller in view action

public function actionView() {
	$model=$this->loadModel($id);
        $this->registerSEO($model);
}

