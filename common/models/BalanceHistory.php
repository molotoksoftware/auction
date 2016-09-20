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
 * This is the model class for table "balance_history".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $description
 * @property string $type
 * @property string $summa
 * @property string $created_on
 */
class BalanceHistory extends CActiveRecord {

    const STATUS_ADD = 1;
    const STATUS_SUB = 2;
    const STATUS_RETURN = 3;
    const STATUS_COMMISSION_SALE_LOT = 4;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'balance_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array(
            array('user_id, type, summa', 'required'),
            array('user_id', 'length', 'max' => 10),
            array('description', 'length', 'max' => 255),
            array('type', 'length', 'max' => 1),
            array('created_on', 'safe'),
            array('id, user_id, description, type, summa, created_on', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors() {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_on',
            )
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'description' => 'Description',
            'type' => 'Type',
            'summa' => 'Summa',
            'created_on' => 'Created On',
        );
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('summa', $this->summa, true);
        $criteria->compare('created_on', $this->created_on, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchAdmin() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('type', $this->type, true);
        $criteria->compare('summa', $this->summa, true);
        $criteria->compare('description', $this->description, true);

        $criteria->join.= ' LEFT JOIN users ON users.user_id = t.user_id';
        $criteria->compare('login', $this->user_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => 'created_on DESC'
            ],
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BalanceHistory the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */
    public function getBuyerLink() {
        if (is_null($this->user)) {
            return '_';
        } else {
            return CHtml::link(
                            $this->user->login, Yii::app()->params["siteUrl"] . '/' . $this->user->login, array(
                        'target' => "_blank"
                            )
            );
        }
    }

    public function getSumma() {
        return floatval($this->summa);
    }

    public function getSummaFormat($decimals = 0) {
        return number_format(floatval($this->summa), $decimals, '.', ' ');
    }

    public function getSummaWithIcoType() {
        $ico = '';
        $class = 'label-green';
        $summa = $this->getSumma();

        if ($this->type == self::STATUS_ADD or $this->type == self::STATUS_RETURN) {
            $ico = '+';
        } elseif (in_array($this->type, [self::STATUS_SUB, self::STATUS_COMMISSION_SALE_LOT])) {
            $ico = '-';
            $class = 'label-red';
        }

        return '<span class="label ' . $class . '">' . $ico . ' ' . $summa . '</span>';
    }

    public static function getStatusList() {
        return [
            self::STATUS_ADD => Yii::t('money', 'Recharge'),
            self::STATUS_SUB => Yii::t('money', 'Discharge'),
            self::STATUS_RETURN => Yii::t('money', 'Return'),
            self::STATUS_COMMISSION_SALE_LOT => Yii::t('money', 'Commission'),
        ];
    }

    public function getStatus() {
        $statuses = self::getStatusList();
        return (isset($statuses[$this->type])) ? $statuses[$this->type] : '-';
    }

}
