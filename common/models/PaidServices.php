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
 * This is the model class for table "paid_service_order".
 *
 * @property string $id
 * @property string $user_id
 * @property string $user_email
 * @property string $user_name
 * @property string $services_id
 * @property integer $services_type
 * @property string $created_date
 * @property integer $total
 * @property integer $status
 * @property string $update
 */

class PaidServices extends CActiveRecord
{
    const STATUS_FAIL = 0;
    const STATUS_UNPAID = 2;
    const STATUS_SUCCESS = 1;


    const STATUS_SERVICE_ACTIVE = 1;
    const STATUS_SERVICE_UNACTIVE = 0;
    const STATUS_SERVICE_EXPIRY = 2;

    const TYPE_SERVICE_PRO_ACCOUNT = 3; /* покупка PRO */


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paid_service_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('user_id, services_id, services_type', 'required'),
            array('services_type, total, status', 'numerical', 'integerOnly' => true),
            array('user_id, services_id', 'length', 'max' => 11),
            array('user_email, user_name', 'length', 'max' => 255),
            array('created_date, update', 'safe'),
            array(
                'id, user_id, user_email, user_name, services_id, services_type, created_date, total, status, update',
                'safe',
                'on' => 'search'
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'user_email' => 'User Email',
            'user_name' => 'User Name',
            'services_id' => 'Services',
            'services_type' => 'Services Type',
            'created_date' => 'Created Date',
            'total' => 'Total',
            'status' => 'Status',
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
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('user_email', $this->user_email, true);
        $criteria->compare('user_name', $this->user_name, true);
        $criteria->compare('services_id', $this->services_id, true);
        $criteria->compare('services_type', $this->services_type);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('total', $this->total);
        $criteria->compare('status', $this->status);
        $criteria->compare('update', $this->update, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaidServices the static model class
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
     * PRO аккаунты
     *
     * @param User $user
     * @param int $idPro
     *
     * @throws CException
     * @return PaidServices
     */
    public function createProAccount(User $user, $idPro)
    {
        if (is_null($user)) {
            throw new CException('Не найден пользователь');
        }

        $pro = Yii::app()->db->createCommand()
            ->select('*')
            ->from('pro_price')
            ->where('id=:id', array(':id' => (int)$idPro))
            ->queryRow();

        if ($pro == false) {
            throw new CException('Не существует ПРО');
        }

        //проверить возможность оплаты
        if (floatval($pro['price']) > floatval($user->balance)) {
            $suma = $pro['price'] - $user->balance;
            $message = 'На вашем счету недостаточно средств для покупки ПРО аккаунта. Пожалуйста пополните счет на сумму (' . floatval(
                $suma
            ) . 'руб.)';
            throw new CException($message, 12);
        }

        //снимаем деньги со счета пользователя
        $user->moneySub(floatval($pro['price']), 'Покупка ПРО аккаунта');
        if ($user->save(false) == false) {
            throw new CException("Error User. \n " . CJSON::encode($user->getErrors()));
        }


        $createdDate = new DateTime('now');
        $interval = new DateInterval($pro['interval']);
        $completionDate = $createdDate->add($interval);

        Yii::app()->db->createCommand()
            ->insert(
                'service_pro_accounts',
                array(
                    'id_user' => $user->user_id,
                    'id_pro' => (int)$idPro,
                    'created_date' => date('Y-m-d H:i:s', time()),
                    'completion_date' => $completionDate->format('Y-m-d H:i:s'),
                    'status' => self::STATUS_SERVICE_ACTIVE
                )
            );


        $services_id = Yii::app()->db->lastInsertID;

        $this->user_id = $user->user_id;
        $this->user_email = $user->email;
        $this->user_name = $user->getFullName();
        $this->services_id = $services_id;
        $this->services_type = self::TYPE_SERVICE_PRO_ACCOUNT;
        $this->created_date = date('Y-m-d H:i:s', time());
        $this->status = self::STATUS_SUCCESS;
        $this->total = floatval($pro['price']);
        $this->update = date('Y-m-d H:i:s', time());

        if ($this->validate() == false) {
            throw new CException('error save');
        }


        Yii::app()->db->createCommand()
            ->update(
                'service_pro_accounts',
                array(
                    'status' => self::STATUS_SERVICE_ACTIVE
                ),
                'id=:id',
                array(
                    ':id' => $this->services_id
                )
            );


        //change status user
        Yii::app()->db->createCommand()
            ->update(
                'users',
                array(
                    'pro' => 1,
                ),
                'user_id=:user_id',
                array(':user_id' => $this->user_id)
            );

        return $this;
    }

    public function updateProAccount(User $user, $idPro)
    {
        if (is_null($user)) {
            throw new CException('Не найден пользователь');
        }

        $pro = Yii::app()->db->createCommand()
            ->select('*')
            ->from('pro_price')
            ->where('id=:id', array(':id' => (int)$idPro))
            ->queryRow();

        if ($pro == false) {
            throw new CException('Не существует ПРО');
        }

        //проверить возможность оплаты
        if (floatval($pro['price']) > floatval($user->balance)) {
            $suma = $pro['price'] - $user->balance;
            $message = 'На вашем счету недостаточно средств для покупки ПРО аккаунта. Пожалуйста пополните счет на сумму (' . floatval(
                $suma
            ) . 'руб.)';
            throw new CException($message, 12);
        }

        //снимаем деньги со счета пользователя
        $user->moneySub(floatval($pro['price']), 'Продление ПРО аккаунта');
        if ($user->save(false) == false) {
            throw new CException("Error User. \n " . CJSON::encode($user->getErrors()));
        }

        $service_pro_accounts = Yii::app()->db->createCommand()
            ->select('completion_date')
            ->from('service_pro_accounts')
            ->where('id=:id', array(':id' => $this->services_id))
            ->queryRow();

        $currentDate = new DateTime($service_pro_accounts['completion_date']);
        $interval = new DateInterval($pro['interval']);
        $completionDate = $currentDate->add($interval);

        Yii::app()->db->createCommand()
            ->update(
                'service_pro_accounts',
                array(
                    'status' => self::STATUS_SERVICE_ACTIVE,
                    'completion_date' => $completionDate->format('Y-m-d H:i:s'),
                ),
                'id=:id',
                array(
                    ':id' => $this->services_id
                )
            );


        $this->update = date('Y-m-d H:i:s', time());

        if ($this->validate() == false) {
            throw new CException('error save');
        }


        return $this;
    }

    public function successRedirect()
    {
        switch ($this->services_type) {
            case PaidServices::TYPE_SERVICE_PRO_ACCOUNT:
                Yii::app()->controller->redirect('/user/pro/index');
                break;
            case PaidServices::TYPE_RECHARGE_BALANCE:
                Yii::app()->controller->redirect('/user/balance/index');
                break;
        }
    }

    public static function hasActivePro($userId = null)
    {
        if (is_null($userId)) {
            $userId = Yii::app()->user->id;
        }

        $paid = PaidServices::model()->find(
            'user_id=:user_id and services_type=:type and status=:status',
            array(
                ':user_id' => $userId,
                ':type' => PaidServices::TYPE_SERVICE_PRO_ACCOUNT,
                ':status' => PaidServices::STATUS_SUCCESS
            )
        );
        if (is_null($paid)) {
            return false;
        } else {
            return $paid->id;
        }
    }

    public static function hasPromotionType($idItem, $itemType, $type)
    {
        $promotion = Yii::app()->db->createCommand()
            ->from('service_promotion')
            ->select('*')
            ->where(
                'id_item=:id_item and type_item=:type_item and type=:type and status=:status',
                array(
                    ':id_item' => $idItem,
                    ':type_item' => $itemType,
                    ':type' => $type,
                    ':status' => PaidServices::STATUS_SERVICE_ACTIVE
                )
            )
            ->queryRow();

        if ($promotion == false) {
            return false;
        } else {
            return $promotion;
        }
    }


    public static function getTypesList()
    {
        return array(
            self::TYPE_SERVICE_PRO_ACCOUNT => 'ПРО аккаунт'
        );

    }

    public static function getTypesPromotion()
    {
        return array(
            self::TYPE_SERVICE_PROMOTION_1,
            self::TYPE_SERVICE_PROMOTION_2,
            self::TYPE_SERVICE_PROMOTION_3
        );
    }

    public static function getStatusList()
    {
        return array(
            self::STATUS_FAIL => 'Ошибка',
            self::STATUS_UNPAID => 'Не оплачен',
            self::STATUS_SUCCESS => 'Успешно'
        );
    }


    public function getBuyerLink()
    {
        if (is_null($this->user)) {
            return '_';
        } else {
            return CHtml::link(
                $this->user->login,
                Yii::app()->params["siteUrl"] . '/' . $this->user->login,
                array(
                    'target' => "_blank"
                )
            );
        }
    }

    public function getStatus()
    {
        $data = self::getStatusList();
        return (isset($data[$this->status])) ? $data[$this->status] : '*';
    }

    public function getStatusLabel()
    {
        $status = $this->getStatus();
        $class = '';
        switch ($this->status) {
            case self::STATUS_SUCCESS:
                $class = 'label-green';
                break;
            case self::STATUS_FAIL:
                $class = 'label-dark-red';
                break;
            case self::STATUS_UNPAID:
                $class = 'label-blue';
                break;
        }
        return CHtml::tag('span', array('class' => 'label ' . $class), $status);
    }

    public function getType()
    {
        $data = self::getTypesList();
        return (isset($data[$this->services_type])) ? $data[$this->services_type] : '*';
    }


}
