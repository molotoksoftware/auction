<?php

/**
 * Скрипт notify для yii
 * http://nijikokun.github.com/bootstrap-notify/
 * 
 */
class ENotify extends CWidget {

    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';

    public $position;
    public $type = 'default';
    public $message;

   

    public function run() {

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->assetManager->publish(Yii::getPathOfAlias('ex-bootstrap.assets.js') . '/bootstrap-notify.js'), CClientScript::POS_END);


        if (Yii::app()->user->hasFlash(self::TYPE_ERROR)) {
            $this->type = 'error';
        } elseif (Yii::app()->user->hasFlash(self::TYPE_SUCCESS)) {
            $this->type = 'success';
        } elseif (Yii::app()->user->hasFlash(self::TYPE_INFO)) {
            $this->type = 'info';
        } elseif (Yii::app()->user->hasFlash(self::TYPE_WARNING)) {
            $this->type = 'warning';
        }

        if ($this->type != 'default') {
            $selector = $this->position;
            $text = Yii::app()->user->getFlash($this->type);

//{$this->type}
            Yii::app()->clientScript->registerScript(__CLASS__ . '#' . $selector, "
 $('.{$selector}').notify({
                type:'bangTidy',
                fadeOut:{enabled: true, delay: 3000 },
                transition:'fade',
                message:{
                    text:'{$text}'
                }
                }).show();
            ", CClientScript::POS_END);
        }//end if
    }

}