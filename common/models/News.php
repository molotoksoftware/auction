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
 * @property string $news_id
 * @property string $title
 * @property string $author_id
 * @property string $date_created
 * @property string $date
 * @property string $alias
 * @property string $short_description
 * @property string $content
 * @property integer $status
 * @property string $clicked
 * @property string $images
 */
class News extends CActiveRecord
{
    private $_url;

    const SAVE_PATH = 'frontend.www.images.news';
    const STATUS_DRAFT = 2;
    const STATUS_PUBLISHED = 1;
    const STATUS_UNPUBLISHED = 0;
    const STATUS_MODERATION = 3;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function scopes()
    {
        return array(
            'published' => array(
                'condition' => 'status=:status',
                'params' => array(':status' => self::STATUS_PUBLISHED)
            )
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
            'uploadedFile' => array(
                'class' => 'backend.extensions.simpleImageUpload.SimpleImageUploadBehavior',
                'attributeName' => 'images',
                'savePathAlias' => self::SAVE_PATH,
                'versions' => array(
                    'preview' => array(
                        'cresize' => array(
                            'width' => 75,
                            'height' => 53,
                        ),
                    ),
                    'small' => array(
                        'cresize' => array(
                            'width' => 105,
                            'height' => 105,
                        ),
                    ),
                    'large' => array(
                        'cresize' => array(
                            'width' => 248,
                            'height' => 287,
                        ),
                    )
                ),
            ),
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'date_created',
                'updateAttribute' => 'update',
            ),
        );
    }

    public function rules()
    {
        return array(
            array('title, date, content', 'required'),
            array('short_description, alias', 'filter', 'filter' => 'trim'),
            array('short_description', 'filter', 'filter' => 'strip_tags'),
            array('status, author_id', 'numerical', 'integerOnly' => true),
            array('alias', 'unique', 'message' => 'названия \'{value}\' уже существует'),
            //array('status', 'in', 'range' => array_keys($this->getStatusList())),
            array('status', 'boolean'),
            array('status', 'default', 'setOnEmpty' => true, 'value' => self::STATUS_PUBLISHED),
            array('alias, title', 'length', 'max' => 255),
            array('short_description', 'length', 'max' => 255),
            array(
                'news_id, title, role, author_id, date_created, date, alias, short_description, content, status, clicked',
                'safe',
                'on' => 'search'
            ),
        );
    }

    public function tableName()
    {
        return 'news';
    }

    public function getStatusList()
    {
        return array(
            self::STATUS_PUBLISHED => 'Опубликовано',
            self::STATUS_UNPUBLISHED => 'Неопубликовано'
        );
    }

    public function getStatus()
    {
        $data = $this->getStatusList();
        return isset($data[$this->status]) ? $data[$this->status] : '*';
    }

    public function last($num = 3)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'order' => 'date DESC',
                'limit' => $num,
            )
        );
        return $this;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'title' => 'Заголовок',
            'images' => 'Изображения',
            'date' => 'Дата',
            'alias' => 'Ссылка',
            'short_description' => 'Описание',
            'content' => 'Текст',
            'status' => "Статус",
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('title', $this->title, true);

        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 100
            ),
            'sort' => array(
                'defaultOrder' => 'date DESC'
            ),
            'criteria' => $criteria,
        ));
    }

    protected function afterDelete()
    {
        $cache = new CFileCache();
        $cache->cachePath = Yii::getPathOfAlias('frontend.runtime.cache');
        $cache->delete(COutputCache::CACHE_KEY_PREFIX . 'widget-last-news');

        return parent::afterDelete();
    }

    public function getUrl()
    {
        if ($this->_url === null) {
            $this->_url = Yii::app()->createUrl('/news/view', array('alias' => $this->alias));
        }
        return $this->_url;
    }

}
