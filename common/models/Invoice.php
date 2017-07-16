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


class Invoice extends CActiveRecord {

    /**
     *
     * @var string Имя плательщика
     */
    public $client_name;
    /**
     *
     * @var string e-mail плательщика
     */
    public $client_email;
    /**
     *
     * @var string телефон плательщика
     */
    public $client_phone;
    /**
     *
     * @var string IP плательщика (нужно передавать для прямой переадресации на страницу ПС)
     */
    public $client_ip;
    /**
     *
     * @var string Назначение платежа
     */
    public $description;
    /**
     *
     * @var boolean  Если нужно инициализировать рекуррентный профиль - поставить true
     */
    public $recurrent_start = false;
    /**
     *
     * @var int Срок действия транзакции - сутки
     */
    public $lifetime = 86400;
    /**
     *
     * @var string Дополнительный параметр
     */
    public $user_params = NULL;
    /**
     *
     * @var string Идентификатор платежной системы 
     */
    public $payment_system = NULL;
    /**
     *
     * @var string Переопределить установленный в настройках точки Result URL 
     */
    public $result_url = NULL;
    /**
     *
     * @var string Переопределить установленный в настройках точки Success URL 
     */
    public $success_url = NULL;
    /**
     *
     * @var string Переопределить установленный в настройках точки Fail URL 
     */
    public $fail_url = NULL;

    public function tableName()
    {
        return 'invoice';
    }


    public function rules()
    {
        return array(
         //   array('user_id, terminal, amount, currency, payment_system', 'required'),
        );
    }



    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }


    public function search()
    {

    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        if (isset(Yii::app()->user->id)) {
            $this->user_id = Yii::app()->user->id;
            return parent::beforeSave();
        }
    }
    
    public function behaviors()
    {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_on',
            )
        );
    }


}
