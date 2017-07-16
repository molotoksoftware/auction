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
 * This is the model class for table "reviews".
 *
 * @property string $id
 * @property string $item
 * @property string $user_to
 * @property string $user_from
 * @property string $text
 * @property string $value
 * @property string $role
 * @property string $date
 * @property string $update
 */
class Reviews extends CActiveRecord
{
    const ROLE_SELLER = 1;
    const ROLE_BUYER = 2;

    const VALUE_POSITIVE = 5;
    const VALUE_NEGATIVE = 1;

    public static $errorsAfterCreate = [];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'reviews';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('item, user_to, user_from, text, value', 'required'),
            array('item, user_to, user_from', 'length', 'max' => 11),
            array('text', 'length', 'max' => 255),
            array('value', 'length', 'max' => 2),
            array('id, item, user_to, user_from, text, value, date, update', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors()
    {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'date',
                'updateAttribute' => 'update',
            )
        );
    }

    protected function beforeSave()
    {
        $item = Auction::model()->findByPk($this->item);
        if ($item == false) {
            return;
        }
        if ($item->owner == Yii::app()->user->id) {
            $this->role = self::ROLE_SELLER;
        } else {
            $this->role = self::ROLE_BUYER;
        }

        return parent::beforeSave();
    }

    public function afterSave()
    {
        $user_id = '';
        if ($this->user_from == Yii::app()->user->id) {
            $user_id = $this->user_to;
        } else {
            $user_id = $this->user_from;
        }

        if ($this->value <= 2) {
            User::model()->updateCounters(array('rating' => -1), 'user_id=:user_id', array(':user_id' => $user_id));
        } elseif ($this->value >= 4) {
            User::model()->updateCounters(array('rating' => 1), 'user_id=:user_id', array(':user_id' => $user_id));
        }


        if ($this->hasEventHandler('onNewReview')) {
            $event = new CEvent($this);
            $this->onNewReview($event);
        }
        return true;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'userTo' => array(
                self::BELONGS_TO,
                'User',
                'user_to',
                'select' => 'login, nick, rating, certified'
            ),
            'userFrom' => array(
                self::BELONGS_TO,
                'User',
                'user_from',
                'select' => 'login, nick, rating, certified'
            ),
            'entity' => array(
                self::BELONGS_TO,
                'Auction',
                'item',
                'select' => 'name, auction_id, type, image, owner'
            ),
            'sale' => array(
                self::BELONGS_TO,
                'Sales',
                'sale_id',
                'select' => 'price'
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'item' => 'Item',
            'user_to' => 'User To',
            'user_from' => 'User From',
            'text' => 'Text',
            'value' => 'Value',
            'date' => 'Date',
            'update' => 'Update',
        );
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('item', $this->item, true);
        $criteria->compare('user_to', $this->user_to, true);
        $criteria->compare('user_from', $this->user_from, true);
        $criteria->compare('text', $this->text, true);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('date', $this->date, true);
        $criteria->compare('update', $this->update, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Reviews the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function onNewReview(CEvent $event)
    {
        $this->raiseEvent('onNewReview', $event);
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */
    public static function getRoleList()
    {
        return array(
            self::ROLE_BUYER,
            self::ROLE_SELLER,
        );
    }
    public static function getValueList()
    {
        return array(
            self::VALUE_POSITIVE,
            self::VALUE_NEGATIVE,
        );
    }

    public static function issetReviewByItemForUser($item, $fromUser, $toUser, $role)
    {
        return self::model()->exists(
            'item=:item and user_to=:user_to and user_from=:user_from and role=:role',
            [
                ':item'      => $item,
                ':user_to'   => $toUser,
                ':role'      => $role,
                ':user_from' => $fromUser,
            ]
        );
    }

    public function getTypeRatingClass()
    {
        if ($this->value <= 2) {
            return 'dislike';
        } else {
            return 'like';
        }
    }

    /**
     *
     * @param User $user
     * @return \Reviews
     */
    public function getTo(User $user)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'user_to=:user',
                'params' => array(':user' => $user->user_id)
            )
        );
        return $this;
    }
    public function getFrom(User $user)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'user_from=:user',
                'params' => array(':user' => $user->user_id)
            )
        );
        return $this;
    }

    public function getToId($userId)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'user_to=:user',
                'params' => array(':user' => $userId)
            )
        );
        return $this;
    }

    /**
     *
     * @param integer $role
     * @return \Reviews
     */
    public function getRole($role)
    {
        $allowedRoles = self::getRoleList();

        if (!empty($role) && in_array($role, $allowedRoles)) {
            $this->getDbCriteria()->mergeWith(
                array(
                    'condition' => 'role=:role',
                    'params' => array(':role' => $role)
                )
            );
        }
        return $this;
    }

    public function getValue($value)
    {
        $allowedValues = self::getValueList();

        if (!empty($value) && in_array($value, $allowedValues)) {
            $this->getDbCriteria()->mergeWith(
                array(
                    'condition' => 'value=:value',
                    'params' => array(':value' => $value)
                )
            );
        }
        return $this;
    }


    /**
     * возвращает количество неоставленных отзывов покупателю
     */
    public static function getCountForsakenBuyer($userId)
    {
        return Yii::app()->db->createCommand()
            ->select('count(*) as count')
            ->from('sales as s')
            ->join('auction as a', 'a.auction_id=s.item_id')
            ->where(
                's.buyer=:buyer and s.review_about_my_buyer=0',
                array(':buyer' => $userId)
            )
            ->queryScalar();
    }

    /**
     * возвращает количество неоставленных отзывов продавцу
     */
    public static function getCountForsakenSeller($userId)
    {

        return Yii::app()->db->createCommand()
            ->select('count(*) as count')
            ->from('sales as s')
            ->join('auction as a', 'a.auction_id=s.item_id')
            ->where(
                'a.owner=:owner and s.review_my_about_saller=0',
                array(':owner' => $userId)
            )
            ->queryScalar();
    }

    /**
     * @param $seller
     * @param $buyer
     * @param $item
     * @param $text
     * @param $rating
     *
     * @return bool
     */
    public static function makeReview($sale_id, $seller, $buyer, $item, $text, $rating)
    {
        $review = new self();

        //подключаем события
        $review->onNewReview = function ($event) use ($buyer) {

            if ($event->sender->role == self::ROLE_BUYER) {
                $updateColumn = 'review_about_my_buyer';
            } else {
                $updateColumn = 'review_my_about_saller';
            }

            Yii::app()->db->createCommand()
                ->update(
                    'sales',
                    [
                        $updateColumn => 1,
                    ],
                    'item_id=:item_id and buyer=:buyer',
                    [
                        ':item_id' => $event->sender->item,
                        ':buyer'   => $buyer,
                    ]
                );
        };
        //end event

        $lot = Yii::app()->db->createCommand()
            ->select('a.owner, a.auction_id')
            ->from('auction a')
            ->where('a.auction_id=:auction_id', array(':auction_id' => (int)$item))
            ->queryRow();

        $review->sale_id = $sale_id;
        $review->item = $item;
        $review->text = $text;
        $review->value = $rating;

        $isSeller = $lot['owner'] == Yii::app()->user->id;

        if ($isSeller) {
            $review->user_to = $buyer;
            $review->user_from = $seller;
        } else {
            $review->user_to = $seller;
            $review->user_from = $buyer;
        }

        if ($isSeller) {
            $exist = self::issetReviewByItemForUser(
                $lot["auction_id"],
                $lot["owner"],
                $buyer,
                self::ROLE_SELLER
            );
            if ($exist) {
                return false;
            }
        }
        $result = $review->save();
        self::$errorsAfterCreate = $review->getErrors();

        return $result;
    }
}
