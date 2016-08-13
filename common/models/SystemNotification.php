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
 * This is the model class for table "system_notification".
 *
 * @property string $id
 * @property string $type
 * @property string $text
 * @property string $user_id
 * @property string $date_created
 * @property string $update
 */
class SystemNotification extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'system_notification';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('type, text, user_id', 'required'),
            array('type', 'length', 'max' => 3),
            array('user_id', 'length', 'max' => 11),
            array('id, type, text, user_id, date_created, update', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors()
    {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'date_created',
                'updateAttribute' => 'update',
            )
        );
    }


    public function defaultScope()
    {
        return array(
            'order' => 't.date_created desc, t.read'
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
            'type' => 'Type',
            'text' => 'Text',
            'user_id' => 'User',
            'date_created' => 'Date Created',
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
        $criteria->compare('type', $this->type, true);
        $criteria->compare('text', $this->text, true);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('update', $this->update, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SystemNotification the static model class
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
    public function byUserId($id)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'user_id=:user_id',
                'params' => array(':user_id' => $id)
            )
        );
        return $this;
    }

    public function getByStatus($status)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => '`read`=:read',
                'params' => array(
                    ':read' => (int)$status
                )
            )
        );
        return $this;
    }

}
