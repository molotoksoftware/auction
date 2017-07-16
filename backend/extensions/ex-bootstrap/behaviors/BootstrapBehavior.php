<?php

class BootstrapBehavior extends CBehavior {

    public function attach($owner) {
        parent::attach($owner);
        Yii::app()->setComponents(array(
            'bootstrap' => array(
                'class' => 'ext.bootstrap.components.Bootstrap',
                'responsiveCss' => true,
                'enableNotifierJS' => false,
                'coreCss' => true,
                'fontAwesomeCss' => true,
                'yiiCss' => true,
                'jqueryCss' => true,
            )
        ));
        Yii::app()->getComponent('bootstrap');
        //Yii::app()->preload[] = 'bootstrap';
        Yii::setPathOfAlias('ex-bootstrap', Yii::app()->basePath . '/extensions/ex-bootstrap');
    }

    public function init() {
        parent::init();
    }

}