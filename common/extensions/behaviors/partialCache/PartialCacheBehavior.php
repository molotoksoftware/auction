<?php

/**
 * TranslitBehavior class file.
 *
 * @author
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
class PartialCacheBehavior extends CActiveRecordBehavior
{
    public function getKey($id, $viewName) 
    {
        $owner = $this->getOwner();
        return $owner->tableName() . '-' . $id . '-' . $viewName;
    }

    public function cacheView($id, $viewName, $params) 
    {
        $real_viewName = str_replace('//', __DIR__.'/../../../../frontend/www/themes/default/views/', $viewName);

        if(isset(Yii::app()->controller)) {
            $controller = Yii::app()->controller;
        }
        else {
            $controller = new CController('Cacher');
        }

        $output = $controller->renderInternal($real_viewName, $params, true);
        Yii::app()->cache->set($this->getKey($id, $viewName), $output);
        return $output;
    }

    public function removeCache($id, $viewName) 
    {
        Yii::app()->cache->set($this->getKey($id, $viewName), null);
    }

    public function getViewCache($id, $viewName) 
    {
        return Yii::app()->cache->get($this->getKey($id, $viewName));
    }
}
