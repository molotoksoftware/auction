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

/**
 *
 * @property $page_id int
 * @property $creation_date datetime
 * @property $protected enum(0, 1) default 0, 0 - страница доступна для удаления, 1 - страница не доступна для удаления
 * @property $title string
 * @property $body text
 * @property $menu_title
 * @property $meta_title string
 * @property $meta_keywords string
 * @property $meta_description string
 * @property $update
 *
 *
 *
 */
class Page extends CActiveRecord
{

    private $_url;

    const PROTECTED_YES = 1;
    const PROTECTED_NO = 0;
    const PAGE_SUPPORT = 'support';


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function scopes()
    {
        return array(
            'published' => array()
        );
    }

    public function tableName()
    {
        return 'pages';
    }

    protected function getProtectedList()
    {
        return array(
            self::PROTECTED_NO => '',
            self::PROTECTED_YES => ''
        );
    }

    public function rules()
    {
        return array(
            array('title', 'required', 'on' => array('update', 'insert')),
            array('title', 'length', 'max' => 255),
            array('body', 'safe'),
            array('alias', 'unique'),
            array('protected', 'default', 'value' => self::PROTECTED_NO),
            array('protected', 'in', 'range' => array_keys($this->getProtectedList())),
            array('alias', 'length', 'max' => 200),
            array('meta_title, meta_description, meta_keywords', 'length', 'max' => 255),
        );
    }

    public function behaviors()
    {
        return array(
            'translit' => array(
                'class' => 'common.extensions.behaviors.translit.TranslitBehavior',
                'sourceAttribute' => 'title',
                'aliasAttribute' => 'alias'
            ),
            'seo' => array(
                'class' => 'common.extensions.seo.SeoBehavior',
            ),
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'creation_date',
                'updateAttribute' => 'update',
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'title' => 'Заголовок',
            'alias' => 'URL',
            'body' => 'Текст',
            'meta_title' => 'SEO title',
            'meta_keywords' => 'SEO keywords',
            'meta_description' => 'SEO description',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('title', $this->title, true);
        return new CActiveDataProvider($this, array(
            'sort' => array(
                'defaultOrder' => 'creation_date DESC'
            ),
            'pagination' => array(
                'pageSize' => 25,
            ),
            'criteria' => $criteria,
        ));
    }




    public function getUrl()
    {
        if ($this->_url === null) {
            $this->_url = Yii::app()->createUrl('/page/view', array('alias' => $this->alias));
        }
        return $this->_url;
    }


    public static function getCatName($cat) {

            $data = self::getSupportPageCategory();
            return (isset($data[(int)$cat]))?$data[(int)$cat]:'';

    }

}