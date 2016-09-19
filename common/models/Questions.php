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


class Questions extends CActiveRecord
{

    const STATUS_ACTIVE = 1; //active
    const STATUS_DELETED = 2; // question deleted

    const UNREAD_STATUS = 0; // unread question
    const READ_STATUS = 1; // read question

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'questions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            return array(
            array('item_id, author_id, owner_id, text, read, status', 'required'),
            array('item_id, author_id, owner_id, status', 'numerical', 'integerOnly'=>true),
            array('item_id, owner_id, author_id', 'length', 'max'=>11),
            array('text', 'length', 'max'=>2048),
            );
    }

    public function behaviors()
    {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
            )
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations()
    {
            return array(
                'owner'=>array(self::BELONGS_TO, 'User', 'author_id'),
                'auction'=>array(self::BELONGS_TO, 'Auction', 'item_id'),
            );
    }

    public function defaultScope()
    {
        return array(
            'order' => 't.created desc, t.read'
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
            return array(
                    'id' => 'Question',
                    'item_id' => 'ID лота',
                    'author_id' => 'Автор',
                    'owner_id' => 'Продавец',
                    'text' => 'Текст',
                    'status' => 'Статус',
                    'created' => 'Добавлен',
            );
    }

    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
    
    public function afterSave() {
        parent::afterSave();

        if ($this->isNewRecord) {
            $params = [
                'linkItem'     => $this->auction->getLink(true),
                'lotModel'     => $this->auction,
                'author'    => Getter::userModel(),
                'question'  => $this->text,
            ];

            $ntf = new Notification($this->owner_id, $params, Notification::TYPE_NEW_ASK);
            $ntf->send();
        }
    }

    /**
     * ************************************************************************* 
     * API
     * *************************************************************************
     */

    public function getListUserQiestions() 
    {

        $listQuestions = self::model()->findAll('owner_id = :owner_id AND status = :status', [
            ':owner_id' => Yii::app()->user->id,
            ':status' => self::STATUS_ACTIVE]);

        return $listQuestions;
    }

    public function byUserId($id)
    {
        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'owner_id=:owner_id AND status=:status',
                'params' => array(':owner_id' => $id, ':status' => self::STATUS_ACTIVE)
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
