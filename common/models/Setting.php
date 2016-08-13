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
 * This is the model class for table "setting".
 *
 * @property string $id
 * @property string $name
 * @property string $title
 * @property integer $type
 * @property string $value
 * @property string $description
 * @property string $preload
 * @property string $update
 */
class Setting extends CActiveRecord
{
    const TYPE_FIELD_TEXT = 1;
    const TYPE_FIELD_TEXT_AREA = 2;
    const TYPE_FIELD_CHECK_BOX = 3;

    const TYPE_COMMON = 1;
    const TYPE_PRO = 2;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'setting';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, value', 'required'),
            array('type', 'numerical', 'integerOnly' => true),
            ['comission', 'boolean'],
            array('name, title, description', 'length', 'max' => 255),
            array('value', 'length', 'max' => 512),
            array('preload', 'length', 'max' => 1),
            array('id, name, title, type, value, description, preload, update', 'safe', 'on' => 'search'),
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
            'name' => 'Name',
            'title' => 'title',
            'type' => 'Type',
            'value' => 'Value',
            'description' => 'Description',
            'preload' => 'Preload',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('preload', $this->preload, true);
        $criteria->compare('update', $this->update, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Setting the static model class
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


    /**
     * @param int $type
     * @return Setting
     */
    public function getByType($type)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'type=:type',
                'params' => array(':type' => $type),
                'order' => 'sort ASC'
            )
        );
        return $this;
    }

    /**
     * @return Setting
     */
    public function getByPreload()
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'preload="1"'
            )
        );
        return $this;
    }

    public static function getByName($name)
    {
        $field = Setting::model()->find('name=:name', array(':name' => $name));
        if (isset($field)) {
            return $field->value;
        }
    }


}
