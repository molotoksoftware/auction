<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
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


class BaseController extends CController
{
    public $breadcrumbs = [];
    public $pageDescription = '';
    public $pageKeywords = '';
    private $metaTags = [];
    private $hasOgImageMetaTag = false;


    protected function performAjaxValidation($model, $id)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $id) {
            header('Content-type: application/json');
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function setPageTitle($value)
    {
        $name = '';
        if (!empty($value)) {
            $name .= $value;
        }


        if (!empty(Yii::app()->params['siteName'])) {
            if (!empty($name)) {
                $name .= ' | ' . Yii::app()->params['siteName'];
            } else {
                $name = Yii::app()->params['siteName'];
            }
        }

        $this->pageTitle = $name;
    }

    protected function loadModel($class, $id, $criteria = [], $exceptionOnNull = true)
    {
        if (empty($criteria)) {
            $model = CActiveRecord::model($class)->findByPk($id);
        } else {
            $finder = CActiveRecord::model($class);
            $c = new CDbCriteria($criteria);
            $c->mergeWith(
                [
                    'condition' => $finder->getTableAlias(true) . '.' . $finder->tableSchema->primaryKey . '=:id',
                    'params'    => [':id' => $id],
                ]
            );
            $model = $finder->find($c);
        }
        if (isset($model)) {
            return $model;
        } else {
            if ($exceptionOnNull) {
                throw new CHttpException(404, 'Unable to find the requested object.');
            }
        }
    }

    /**
     * @param array $attributes
     */
    public function addMetaTag($attributes)
    {
        $this->metaTags[] = $attributes;
        if ($this->hasOgImageMetaTag === false && isset($attributes['property'])
            && $attributes['property'] == 'og:image'
        ) {
            $this->hasOgImageMetaTag = true;
        }
    }

    public function renderMetaTags()
    {
        foreach ($this->metaTags as $metaTag) {
            echo CHtml::tag('meta', $metaTag) . "\n";
        }
    }

    /**
     * @return bool
     */
    public function getHasOgImageMetaTag()
    {
        return $this->hasOgImageMetaTag;
    }

    /**
     * @return array
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

}

