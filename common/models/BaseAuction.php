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
 * This is the model class for table "auction".
 *
 * @property string $auction_id
 * @property string $name
 * @property string $text
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $image
 * @property string $category_id
 * @property string $created //Дата публикации
 * @property integer $type_transaction
 * @property string $price
 * @property string $starting_price
 * @property string $conditions_transfer
 * @property integer $publication_status
 * @property string $bidding_date
 * @property string $duration
 * @property string $quantity
 * @property string $quantity_sold
 * @property string $viewed
 * @property string $longitude
 * @property string $owner
 * @property integer $status
 * @property integer $current_rate
 * @property integer $current_bid
 * @property string $update
 * @property string $id_city
 * @property string $id_region
 * @property string $id_country

 *
 * The followings are the available model relations:
 * @property Category $category
 * @property User $ownerModel
 */
class BaseAuction extends CActiveRecord
{
    private $_url;

    public $form_is_paid_placement;
    public $form_paid_placement_id_duration;

    public $form_is_placement_up;
    public $form_placement_up_id_duration;

    public $form_is_allot_item_duration;
    public $form_is_allot_item;


    const TYPE_AUCTION = 1;
    //status
    const ST_ACTIVE = 1; //активный
    const ST_SOLD_BLITZ_PRICE = 2; // продан по блиц
    const ST_SOLD_SUCCESS_BID = 3; // продан в результате завершения торгов
    const ST_COMPLETED_EXPR_DATE = 4; //завершенный по истечению даты
    const ST_SAME = 7; // Выставить похожий

    const ST_DELETED = 10;

    /**
     * @return string the associated database table name
     */

    public function tableName()
    {
        return 'auction';
    }


    public function scopes()
    {
        return array(
            'published' => array(
                'condition' => 'status=:status',
                'params' => array(':status' => self::ST_ACTIVE)
            )
        );
    }

    public function behaviors()
    {
        return array(
            'changedAttributes' => array(
                'class' => 'common.extensions.behaviors.changedAttributes.ChangedAttributesBehavior'
            ),
            'partialCache' => array(
                'class' => 'common.extensions.behaviors.partialCache.PartialCacheBehavior'
            )
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, category_id', 'required'),
            array('id_country, id_region, id_city', 'require_not_same', 'message' => 'Заполните место с точностью до города'),
            array('id_country, id_region, id_city', 'numerical', 'integerOnly' => true),
            array('duration', 'required', 'message' => 'Укажите продолжительность торгов'),
            array('owner', 'required', 'message' => 'Укажите пользователя'),
            array(
                'price, starting_price',
                'numerical', 'numberPattern'=>'/^[0-9]{1,9}(\.[0-9]{1,2})?$/'
            ),
            array('type_transaction, publication_status', 'numerical', 'integerOnly' => true),
            array('category_id, owner', 'length', 'max' => 10),
            array('name, meta_description, meta_keywords, image, conditions_transfer', 'length', 'max' => 2048),
            array('text', 'safe'),
            array('contacts', 'length'),
            array('quantity', 'numerical', 'min' => 0, 'max' => 9999),
            array('form_is_paid_placement, form_is_placement_up', 'boolean'),
            array('viewed', 'length', 'max' => 6),
            array('conditions_transfer, contacts, name', 'filter', 'filter' => array($obj = new CHtmlPurifier(), 'purify')),
            array(
                'auction_id, name, text, meta_description, meta_keywords, image, category_id, created, type_transaction, price, starting_price, conditions_transfer, publication_status, status, bidding_date, quantity, viewed, owner, update',
                'safe',
                'on' => 'search'
            )
        );
    }

    public function require_not_same($attribute, $params)
    {
        if (!strlen($this->getAttribute($attribute))) {
            $this->addError($attribute, isset($params['message']) ? $params['message'] : 'Необходимо заполнить поле ' . $this->getAttributeLabel($attribute));
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'category'          => [self::BELONGS_TO, 'Category', 'category_id'],
            'country'           => [self::BELONGS_TO, 'Country', 'id_country'],
            'region'            => [self::BELONGS_TO, 'Region', 'id_region'],
            'city'              => [self::BELONGS_TO, 'City', 'id_city'],
            'ownerModel'        => [self::BELONGS_TO, 'User', 'owner'],
        ];
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'auction_id'                      => 'Auction',
            'name'                            => 'Название',
            'text'                            => 'Описание',
            'meta_description'                => 'Meta Description',
            'meta_keywords'                   => 'Meta Keywords',
            'image'                           => 'Image',
            'category_id'                     => 'Категория',
            'created'                         => 'Дата публикации',
            'type_transaction'                => 'Тип сделки (0-аукцион1-продажа)',
            'price'                           => 'Блиц-цена',
            'starting_price'                  => 'Начальная цена',
            'conditions_transfer'             => 'Условия передачи',
            'publication_status'              => '0-Публиковать/1-Не публиковать',
            'bidding_date'                    => 'Продолжительность торгов',
            'quantity'                        => 'Количество',
            'contacts'                        => 'Контактная информация',
            'owner'                           => 'Пользователь',
            'duration'                        => 'Продолжительность торгов',
        ];
    }


    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('status', $this->status);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('text', $this->text, true);
        $criteria->compare('meta_description', $this->meta_description, true);
        $criteria->compare('meta_keywords', $this->meta_keywords, true);
        $criteria->compare('image', $this->image, true);
        $criteria->compare('category_id', $this->category_id, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('type_transaction', $this->type_transaction);
        $criteria->compare('price', $this->price, true);
        $criteria->compare('starting_price', $this->starting_price, true);
        $criteria->compare('conditions_transfer', $this->conditions_transfer, true);
        $criteria->compare('publication_status', $this->publication_status);
        $criteria->compare('bidding_date', $this->bidding_date, true);
        $criteria->compare('quantity', $this->quantity, true);
        $criteria->compare('viewed', $this->viewed, true);
        $criteria->compare('owner', $this->owner, true);
        $criteria->compare('update', $this->update, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 100
            ),
            'sort' => array(
                'defaultOrder' => 'created DESC',
                'attributes' => array(
                    '*',
                    'countBids' => array(
                        'asc' => '(SELECT COUNT(*) from bids
                        WHERE bids.lot_id = t.auction_id) ASC',
                        'desc' => '(SELECT COUNT(*) from bids 
                        WHERE bids.lot_id = t.auction_id) DESC',
                    ),
                    'countQuestions' => array(
                        'asc' => '(SELECT COUNT(*) from questions q
                        WHERE q.item_id = t.auction_id and type=1) ASC',
                        'desc' => '(SELECT COUNT(*) from questions q 
                        WHERE q.item_id = t.auction_id and type=1) DESC',
                    ),
                )
            )
        ));
    }

    public function beforeSave()
    {
        if ($this->getIsNewRecord()) {
            $this->created = date('Y-m-d H:i:s', time());
        }

        return parent::beforeSave();
    }

    public function beforeDelete()
    {
        $images = Yii::app()->db->createCommand()
            ->select('image ')
            ->from('images')
            ->where('item_id=:item_id and type=0', array(':item_id' => $this->auction_id))
            ->queryAll();

        $save_path = Yii::getPathOfAlias('frontend') . '/www/i/';
        if (count($images) > 0) {
            foreach ($images as $img) {
                @unlink($save_path . $img['image']);
                //delete resize
                $type = get_class($this);
                foreach ($type::$versions as $v_name => $v_params) {
                    @unlink($save_path . 'thumbs' . DIRECTORY_SEPARATOR . $v_name . '_' . $img['image']);
                }
                //end delete resize
            }
        }
        Yii::app()->db->createCommand()
            ->delete('images', 'item_id=:item_id and type=0', array(':item_id' => $this->auction_id));


        //favorites
        $favorites = Yii::app()->db->createCommand()
            ->select('favorite_id')
            ->from('favorites')
            ->where(
                'item_id=:item_id and type=:type',
                array(
                    ':item_id' => $this->auction_id,
                    ':type' => 1
                )
            )
            ->queryAll();

        if (count($favorites) > 0) {
            foreach ($favorites as $fav) {
                Yii::app()->db->createCommand()
                    ->delete('favorites', 'favorite_id=:favorite_id', array(':favorite_id' => $fav['favorite_id']));
            }
        }


        return parent::beforeDelete();
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Auction the static model class
     *
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function instantiate($attributes)
    {
        if ($attributes['type'] == self::TYPE_AUCTION) {
            $model = new Auction(null);
        } else {
            throw new Exception('not allowed type');
        }
        return $model;
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */

    /**
     * возвращает массив родительских категорий
     * @return array
     *
     */
    public function getAncestorCategoryId()
    {
        if (!$this->category) {
            throw new CException('категория не существует');
        }
        $data = array();
        $ancestors = $this->category->ancestors()->findAll();
        foreach ($ancestors as $ancestor) {
            if ($ancestor->category_id != Category::DEFAULT_CATEGORY) {
                $data[] = $ancestor->category_id;
            }
        }
        array_push($data, $this->category->category_id);
        return $data;
    }

    public function getNameForAdminTable()
    {
        if ($this->type == self::TYPE_AUCTION) {
            return '<i class="icon-bolt"></i> <a href="'.Yii::app()->params['siteUrl'].'/auction/'.$this->auction_id.'" target="_blank">' . $this->name . '</a>';
        } else {
            return $this->name;
        }
    }

    public function getCategoryName()
    {
        if (is_null($this->category)) {
            return '- * -';
        } else {
            return $this->category->name;
        }
    }

    public function getCountBids()
    {

        return Yii::app()->db->createCommand()
            ->select('count(*)')
            ->from('bids')
            ->where('bids.lot_id=:auction_id', array(':auction_id' => $this->auction_id))
            ->queryScalar();
    }

    public function getDateCreated()
    {
        return Yii::app()->dateFormatter->format('dd.MM.yyyy H:mm:ss', $this->created);
    }

    public function getDateCompletion()
    {
        $date = new DateTime('now');
        $date_end = new DateTime($this->bidding_date);
        $interval = $date->diff($date_end);
        $days = '';
        $f = $interval->format('%R%');
        if ($f == '-' || $this->status != 1) {
            //return '<span class="label label-dark-red"><b>завершенный лот</b>, дата окончания: ' . Yii::app()->dateFormatter->format('dd MMMM yyyy H:m:s', $this->bidding_date)."</span>";
            return '<span class="label label-dark-red"><b>завершенный лот</b></span>';
        } else {
            if ($interval->format('%a') > 0) {
                $days = $interval->format('%a') . ' <span>' . Yii::t(
                        'app',
                        'day|days',
                        $interval->format('%a')
                    ) . '</span>';
            }

            $time = $interval->format('%H:%I');
            return $days . ' ' . $time;
        }
    }

    public function getLink($absolute = false)
    {
        if ($absolute) {
            return CHtml::link(
                $this->name,
                Yii::app()->createAbsoluteUrl('/auction/view', array('id' => $this->auction_id))
            );
        } else {
            return CHtml::link($this->name, Yii::app()->createUrl('/auction/view', array('id' => $this->auction_id)));
        }
    }

    public static function staticGetLink($name, $item_id)
    {
        return CHtml::link($name, Yii::app()->createAbsoluteUrl('/auction/view', array('id' => $item_id)));
    }

    public function getUrl()
    {
        if ($this->_url === null) {
            $this->_url = Yii::app()->createUrl('/auction/view', array('id' => $this->auction_id));
        }
        return $this->_url;
    }

    public function getAbsoluteUrl()
    {
        return Yii::app()->createAbsoluteUrl('/auction/view', ['id' => $this->auction_id]);
    }

    protected function afterSave()
    {
        //Category::recalcAuctionCount($this->category_id);
        //BaseAuction::recache_AuctionItemById($this->auction_id);
    }

    protected function afterDelete()
    {
        //Category::recalcAuctionCount($this->category_id);
        //BaseAuction::remove_cache_AuctionItemById($this->auction_id);
    }

    public function byStatus($status)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'status=:status',
                'params' => array(
                    ':status' => $status
                )
            )
        );
        return $this;
    }

    /*
     * Вычисляет type_transaction на основе текущих аттрибутов (используется при импорте)
     */
    public function determineTypeTransaction()
    {
        /*
         * если начальная цена есть и она больше рубля = type_transaction = 0
         *  если только цена или блиц-цена = type_transaction = 1
         *  если начальная цена есть и она 1 рубль, то type_transaction = 2
         */
        if ($this->starting_price && $this->starting_price > 0) {
            if (intval($this->starting_price) == 1)
                $this->type_transaction = 2;
            else
                $this->type_transaction = 0;
        } else {
            $this->type_transaction = 1;
        }
    }

    public function removeImages()
    {
        $images = ImageAR::model()->findAllByAttributes(['item_id' => $this->getPrimaryKey()]);

        $folder = Yii::getPathOfAlias('frontend') . '/www/i2/' . $this->owner;

        $tmpFolder = Yii::getPathOfAlias('frontend') . '/www/tmp';
        $removedImages = 0;
        if (is_dir($folder)) {
            if ($images) {
                $removedImages = count($images);
                $imageIds = [];
                foreach ($images as $image) {
                    $imageFile = $image->image;
                    $imageIds[] = $image->image_id;

                    $file['origin'] = $folder . '/' . $imageFile;
                    $file['tmp'] = $tmpFolder . '/' . $imageFile;
                    $file['thumb'] = $tmpFolder . '/thumbs/' . $imageFile;

                    /*
                     * Delete origin and temporary image files
                     */
                    foreach ($file as $key => $path) {
                        if (is_file($path)) {
                            @unlink($file);
                        }
                    }

                    $thumbsFolder = $folder . '/thumbs';

                    $thumbsSize = ['large', 'big', 'medium', 'prv'];

                    foreach ($thumbsSize as $size) {
                        $fullImagePath = $thumbsFolder . '/' . $size . '_' . $imageFile;
                        if (is_file($fullImagePath)) {
                            @unlink($fullImagePath);
                        }
                    }

                    $image->delete();
                }

            }
        }
        $this->image = '';
        $this->update(['image']);

        return $removedImages;
    }
}
