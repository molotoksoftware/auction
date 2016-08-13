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
 * This is the model class for table "attribute_values".
 *
 * @property string $value_id
 * @property string $attribute_id
 * @property string $value
 * @property string $sort
 * @property string $update
 */
class AttributeValues extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'attribute_values';
    }

    public function defaultScope()
    {
        return array(
            'order' => 'sort ASC'

        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('attribute_id, value, sort', 'required'),
            array('value', 'length', 'max' => 255),
            array('value_id, attribute_id, value, sort, update', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'child' => array(self::HAS_MANY, 'AttributeValues', 'parent_id', 'order' => 'sort ASC')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'value_id' => 'Value',
            'attribute_id' => 'Attribute',
            'value' => 'Value',
            'sort' => 'Sort',
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

        $criteria->compare('value_id', $this->value_id, true);
        $criteria->compare('attribute_id', $this->attribute_id, true);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('sort', $this->sort, true);
        $criteria->compare('update', $this->update, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AttributeValues the static model class
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


    public function beforeDelete()
    {
        $child = self::model()->findAll(
            'parent_id=:parent_id',
            array(
                ':parent_id' => $this->value_id
            )
        );

        if (count($child) > 0) {
            foreach ($child as $ch) {
                $ch->delete();
            }
        }
        return parent::beforeDelete();
    }
}
