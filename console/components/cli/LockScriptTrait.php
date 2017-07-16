<?php

/**
 * Class LockScriptTrait
 *
 * Класс для блокировки cli скриптов на время выполнения, чтобы предотвратить паралельный запуск скрипта.
 */
trait LockScriptTrait
{
    protected function lockScript($scriptName)
    {
        Yii::app()->cache->set($scriptName, 1);
    }

    protected function unLockScript($scriptName)
    {
        Yii::app()->cache->set($scriptName, 0);
    }

    protected function isLockedScript($scriptName)
    {
        return 1 == Yii::app()->cache->get($scriptName);
    }
}