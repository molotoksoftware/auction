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
 * This is the model class for table "app_process".
 *
 * @property integer $id
 * @property string  $code
 * @property string  $title
 * @property string  $data
 * @property string  $created_at
 */
class AppProcess extends CActiveRecord
{
    const CODE_EMAIL_QUEUE = 'email_queue';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'app_process';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['code, title, data', 'required'],
            ['code, title', 'length', 'max' => 50],
            ['id, code, title, data, created_at', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'code'       => 'Code',
            'title'      => 'Title',
            'data'       => 'Data',
            'created_at' => 'Created At',
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
        $criteria->compare('code', $this->code, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('data', $this->data, true);
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
     * @return AppProcess the static model class
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

    protected function afterSave()
    {
        parent::afterSave();
        $this->data = unserialize($this->data);
    }

    protected function afterFind()
    {
        parent::afterFind();
        $this->data = unserialize($this->data);
    }

    protected function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }
        if (empty($this->data)) {
            $this->data = [];
        }
        $this->data = serialize($this->data);
        return true;
    }
}
