<?php

class SeoControllerBehavior extends CBehavior
{
    public $defaultAttributeTitle = 'name';
    public $titleAttribute = 'seo_title';
    public $descriptionAttribute = 'seo_description';
    public $keywordsAttribute = 'seo_keywords';

    public function registerSEO($model)
    {
        if (is_null($model) || $model==false) {

            $keywords = Setting::model()->find('name=:name', array(':name' => 'keywords'))->value;
            Yii::app()->getController()->pageKeywords = $keywords;
            $description = Setting::model()->find('name=:name', array(':name' => 'description'))->value;
            Yii::app()->getController()->pageDescription = $description;
            return false;
        }

        if (!$model->isNewRecord) {

            if (empty($model->{$this->titleAttribute})) {
                $title = $model->{$this->defaultAttributeTitle};
            } else {
                $title = $model->{$this->titleAttribute};
            }

            if (!empty($title)) {
                Yii::app()->getController()->pageTitle = CHtml::encode($title);
            }



            if (!empty($model->{$this->descriptionAttribute})) {
                $description = $model->{$this->descriptionAttribute};
                Yii::app()->getController()->pageDescription = $description;
            } else {
                $description = Setting::model()->find('name=:name', array(':name' => 'description'))->value;
                Yii::app()->getController()->pageDescription = $description;
            }


            if (!empty($model->{$this->keywordsAttribute})) {
                Yii::app()->getController()->pageKeywords  = $model->{$this->keywordsAttribute};
            } else {
                $keywords = Setting::model()->find('name=:name', array(':name' => 'keywords'))->value;
                Yii::app()->getController()->pageKeywords = $keywords;

            }

        }
    }

}
