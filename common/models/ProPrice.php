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
 * This is the model class for table "pro_price".
 *
 * @property string $id
 * @property string $name
 * @property integer $duration
 * @property string $description
 * @property string $price
 */
class ProPrice extends CActiveRecord
{

    const    DURATION_1MONTH = 1;
    const    DURATION_6MONTH = 2;
    const    DURATION_1YEAR = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pro_price';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, duration, price, interval', 'required'),
            array('duration', 'numerical', 'integerOnly' => true),
            array('name, description', 'length', 'max' => 255),
            array('price', 'length', 'max' => 10),
            array('id, name, duration, description, price', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Названия',
            'duration' => 'Период',
            'description' => 'Описания',
            'price' => 'Цена',
            'interval' => 'Интервал'
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('duration', $this->duration);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('price', $this->price, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ProPrice the static model class
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

    public static function getDurationList()
    {
        return array(
            self::DURATION_1MONTH => '1 месяц',
            self::DURATION_6MONTH => '6 месяцев',
            self::DURATION_1YEAR => 'год'
        );
    }

    public function getPrice()
    {
        return floatval($this->price) . ' руб.';
    }

    public function getDuration()
    {
        $data = self::getDurationList();
        return (isset($data[$this->duration])) ? $data[$this->duration] : ' * ';
    }
}
