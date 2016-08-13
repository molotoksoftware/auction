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

class UsersService extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'users_service';
	}

	public function rules()
	{
		return array(
			array('id, service, service_id', 'required'),
			array('id', 'length', 'max' => 11),
			array('service, service_id', 'length', 'max' => 100),
            array('service, service_id', 'filter', 'filter' => 'trim'),
            array('service, service_id', 'filter', 'filter' => array($obj = new CHtmlPurifier(), 'purify')),
            array('service, service_id', 'filter', 'filter' => 'strip_tags'),

			array('id, service, service_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'service' => 'Service',
			'service_id' => 'Service',
		);
	}

	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('service', $this->service, true);
		$criteria->compare('service_id', $this->service_id, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}