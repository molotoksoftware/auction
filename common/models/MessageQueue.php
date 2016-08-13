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
 * This is the model class for table "message_queue".
 *
 * Сущность используется как очередь сообщений(емайл, смс).
 *
 * @property integer $id
 * @property integer $is_email
 * @property integer $is_sms
 * @property string  $message_object TODO удалить
 * @property string  $message_json
 * @property integer $scope
 * @property integer $status
 * @property string  $created_at
 * @property string  $processed_at
 */
class MessageQueue extends CActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_PROCESSING = 10;
    const STATUS_PROCESSED_SUCCESS = 20;
    const STATUS_PROCESSED_ERROR = 30;

    const SCOPE_EMAIL_MAILING = 1;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'message_queue';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['is_email, is_sms, message_json, scope', 'required'],
            ['is_email, is_sms', 'boolean'],
            ['status, scope', 'numerical', 'integerOnly' => true],
            ['processed_at, message_object', 'safe'],
            ['id, is_email, is_sms, message_object, message_json, status, created_at, processed_at, scope', 'safe', 'on' => 'search'],
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
            'id'             => 'ID',
            'is_email'       => 'Is Email',
            'is_sms'         => 'Is Sms',
            'message_object' => 'Message Object',
            'message_json'   => 'Message Json',
            'scope'          => 'Scope',
            'status'         => 'Status',
            'created_at'     => 'Created At',
            'processed_at'   => 'Processed At',
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
        $criteria->compare('is_email', $this->is_email);
        $criteria->compare('is_sms', $this->is_sms);
        $criteria->compare('message_object', $this->message_object, true);
        $criteria->compare('scope', $this->scope);
        $criteria->compare('status', $this->status);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('processed_at', $this->processed_at, true);

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
     * @return MessageQueue the static model class
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

    protected function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        /*$this->message_object = trim(serialize($this->message_object));*/
        if (!is_array($this->message_json)) {
            $this->message_json = [];
        }
        $this->message_json = CJSON::encode($this->message_json);
        return true;
    }

    protected function afterValidate()
    {
        parent::afterValidate();
        /*$this->message_object = unserialize($this->message_object);*/
        $this->message_json = CJSON::decode($this->message_json);
    }

    protected function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }
        /*$this->message_object = trim(serialize($this->message_object));*/
        $this->message_json = CJSON::encode($this->message_json);
        return true;
    }

    protected function afterSave()
    {
        parent::afterSave();
        /*$this->message_object = unserialize($this->message_object);*/
        $this->message_json = CJSON::decode($this->message_json);
    }

    protected function afterFind()
    {
        parent::afterFind();

        $this->message_json = CJSON::decode($this->message_json);
    }


    public function sendSuccess()
    {
        $this->status = self::STATUS_PROCESSED_SUCCESS;
        $this->processed_at = new CDbExpression('NOW()');
        $this->update(['status', 'processed_at']);
    }

    public function sendFailed()
    {
        $this->status = self::STATUS_PROCESSED_ERROR;
        $this->processed_at = new CDbExpression('NOW()');
        $this->update(['status', 'processed_at']);
    }
}
