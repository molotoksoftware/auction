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
 * This is the model class for table "billing_currency_rate".
 *
 * Модель хранит курс валюты к другой валюте(рублю) на указанную дату.
 *
 * @property integer         $id
 * @property integer         $from_currency_id
 * @property integer         $to_currency_id
 * @property double          $rate
 * @property string          $rate_source
 * @property string          $created_at
 * @property double          $yesterday_difference
 *
 * The followings are the available model relations:
 * @property BillingCurrency $toCurrency
 * @property BillingCurrency $fromCurrency
 */
class BillingCurrencyRate extends CActiveRecord
{
    public function behaviors()
    {
        return [
            'CTimestampBehavior' => [
                'class'           => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_at',
                'updateAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'billing_currency_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['from_currency_id, to_currency_id, rate, rate_source, yesterday_difference', 'required'],
            ['from_currency_id, to_currency_id', 'numerical', 'integerOnly' => true],
            ['rate, yesterday_difference', 'numerical'],
            ['rate_source', 'length', 'max' => 255],
            ['id, from_currency_id, to_currency_id, rate, rate_source, created_at, yesterday_difference', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'toCurrency'   => [self::BELONGS_TO, 'BillingCurrency', 'to_currency_id'],
            'fromCurrency' => [self::BELONGS_TO, 'BillingCurrency', 'from_currency_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'                   => 'ID',
            'from_currency_id'     => 'From Currency',
            'to_currency_id'       => 'To Currency',
            'rate'                 => 'Rate',
            'rate_source'          => 'Rate Source',
            'created_at'           => 'Created At',
            'yesterday_difference' => 'Процент изменения курса с вчерашним днем',
        ];
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('from_currency_id', $this->from_currency_id);
        $criteria->compare('to_currency_id', $this->to_currency_id);
        $criteria->compare('rate', $this->rate);
        $criteria->compare('yesterday_difference', $this->yesterday_difference);
        $criteria->compare('rate_source', $this->rate_source, true);
        $criteria->compare('created_at', $this->created_at, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name.
     *
     * @return BillingCurrencyRate the static model class
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

    protected function afterFind()
    {
        $this->rate = floatval($this->rate);
        $this->yesterday_difference = floatval($this->yesterday_difference);
        parent::afterFind();
    }

    /**
     * @param string $date           Y-m-d формат
     * @param string $fromCurrencyId CODE
     * @param string $toCurrencyId   CODE
     *
     * @return BillingCurrencyRate
     */
    public function getRateByDate($date, $fromCurrencyId, $toCurrencyId)
    {
        return $this->find(
            'DATE(created_at) = :date AND from_currency_id = :from_currency_id AND to_currency_id = :to_currency_id',
            [':date' => $date, ':from_currency_id' => $fromCurrencyId, ':to_currency_id' => $toCurrencyId]
        );
    }

    /**
     * @param array $fromCurrencyIds
     *
     * @return BillingCurrencyRate[]
     */
    public static function getLatestRatesByFromCurrencies($fromCurrencyIds)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('from_currency_id', $fromCurrencyIds);
        $criteria->limit = count($fromCurrencyIds);
        $criteria->order = 'id DESC';

        return self::model()->findAll($criteria);
    }
}
