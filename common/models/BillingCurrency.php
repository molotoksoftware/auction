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
 * This is the model class for table "billing_currency".
 *
 * Модель содержит список валют доступных в системе.
 *
 * @property integer               $id
 * @property string                $name
 * @property string                $code
 * @property integer               $is_active
 * @property string                $created_at
 * @property string                $updated_at
 *
 * The followings are the available model relations:
 * @property BillingCurrencyRate[] $billingCurrencyRates
 * @property BillingCurrencyRate[] $billingCurrencyRates1
 */
class BillingCurrency extends CActiveRecord
{
    const CODE_RUR = 'RUR';
    const CODE_RUB = 'RUB';
    const CODE_USD = 'USD';
    const CODE_EUR = 'EUR';
    const CODE_BYR = 'BYR';
    const CODE_UAH = 'UAH';
    const CODE_KZT = 'KZT';
    const CODE_AMD = 'AMD';

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
        return 'billing_currency';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['name, code', 'required'],
            ['is_active', 'numerical', 'integerOnly' => true],
            ['name', 'length', 'max' => 255],
            ['code', 'length', 'max' => 16],
            ['updated_at', 'safe'],
            ['id, name, code, is_active, created_at, updated_at', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'billingCurrencyRates'  => [self::HAS_MANY, 'BillingCurrencyRate', 'to_currency_id'],
            'billingCurrencyRates1' => [self::HAS_MANY, 'BillingCurrencyRate', 'from_currency_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'name'       => 'Name',
            'code'       => 'Code',
            'is_active'  => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);

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
     * @return BillingCurrency the static model class
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

    public function filterActive()
    {
        $this->getDbCriteria()->mergeWith([
            'condition' => 'is_active = 1',
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        $result = Yii::app()->getLocale()->getCurrencySymbol($this->code);
        return $result === '' || $result === null ? $this->code : $result;
    }

    /**
     * @return BillingCurrency[]
     */
    public static function getAllAvailableExceptRur()
    {
        return self::model()->findAll('is_active = 1 AND code != :rurCode', [':rurCode' => self::CODE_RUR]);
    }
}
