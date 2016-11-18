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
 * This is the model class for table "sales".
 *
 * @property string        $sale_id
 * @property string        $item_id
 * @property integer       $buyer
 * @property string        $date
 * @property string        $type
 * @property string        $price
 * @property integer       $review_my_about_saller
 * @property integer       $review_about_my_buyer
 * @property integer       $quantity
 * @property integer       $amount
 * @property string        $seller_id
 * @property integer       $status
 * @property integer       $email_notify
 * @property integer       $del_status
 * @property boolean       $del_status_buyer
 *
 * @property Auction       $auction
 * @property User          $buyerModel
 */
class Sales extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */

    public function tableName()
    {
        return 'sales';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['item_id, buyer, date, type, price, amount', 'required'],
            [
                'buyer, review_my_about_saller, review_about_my_buyer, quantity, amount, status, email_notify, del_status, del_status_buyer',
                'numerical',
                'integerOnly' => true
            ],
            ['item_id, price, seller_id', 'length', 'max' => 10],
            ['type', 'length', 'max' => 1],

            [
                'sale_id, item_id, buyer, date, type, price, review_my_about_saller, review_about_my_buyer, quantity, amount, seller_id, status, email_notify, del_status',
                'safe',
                'on' => 'search'
            ],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'auction'       => [self::BELONGS_TO, 'Auction', 'item_id'],
            'buyerModel'    => [self::BELONGS_TO, 'User', 'buyer'],
            'sellerModel'   => [self::BELONGS_TO, 'User', 'seller_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'sale_id'                => 'Sale',
            'item_id'                => 'Item',
            'buyer'                  => 'Buyer',
            'date'                   => 'Date',
            'type'                   => 'Type',
            'price'                  => 'Price',
            'review_my_about_saller' => 'Review My About Saller',
            'review_about_my_buyer'  => 'Review About My Buyer',
            'quantity'               => 'Quantity',
            'amount'                 => 'Amount',
            'seller_id'              => 'Seller',
            'status'                 => 'Status',
            'email_notify'           => 'Email Notify',
            'del_status'             => 'Del Status',
        ];
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

     //   $criteria->compare('status_mail', $this->status_mail, true);
        $criteria->compare('seller_id', $this->seller_id, true);
        $criteria->compare('buyer', $this->buyer, true);
        $criteria->compare('sale_id', $this->sale_id, true);
        $criteria->compare('item_id', $this->item_id, true);

        return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
                'pagination' => [
                     'pageSize' => 20,
                ], 
               'sort' => [
                   'defaultOrder' => 'sale_id DESC',
                ],
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name.
     *
     * @return Sales the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */

}
