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
 * This is the model class for table "track_owners".
 *
 * @property integer $id_track_owners
 * @property string $owner
 * @property string $id_user
 * @property integer $crt_date
 */
class TrackOwners extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'track_owners';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
			return array(
			array('owner, id_user', 'required'),
			array('crt_date', 'numerical', 'integerOnly'=>true),
			array('owner, id_user', 'length', 'max'=>11),
			array('id_track_owners, owner, id_user, crt_date', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_track_owners' => 'Id Track Owners',
			'owner' => 'Owner',
			'id_user' => 'Id User',
			'crt_date' => 'Crt Date',
		);
	}

	/**
	 * @return CActiveDataProvider the data provider that can return the models
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_track_owners',$this->id_track_owners);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('id_user',$this->id_user,true);
		$criteria->compare('crt_date',$this->crt_date);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TrackOwners the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * ************************************************************************* 
     * API
     * *************************************************************************
     */

    public static function getListUserForOwner() 
    {

        return Yii::app()->db->createCommand()
        ->select('a.*, b.*')
        ->from('track_owners a')
        ->leftJoin('users b', 'b.user_id=a.owner')
        ->where('a.id_user=:user_id', array(':user_id' => Yii::app()->user->id))
        ->queryAll();
    }

    public static function getCountAuctionsFromTrackUsers($params) 
    {

        $all_item = Yii::app()->db->createCommand()
            ->select('*')
            ->from('track_owners a')
            ->leftJoin('auction b', 'b.owner=a.owner')
            ->where('a.id_user=:user_id AND b.status=:status', $params)
            ->queryAll();

        return count($all_item);
    }

    public static function getLotData() 
    {

        return Yii::app()->db->createCommand()
            ->select('a.*, b.*, bid.price as current_bid')
            ->from('track_owners a')
            ->leftJoin('auction b', 'b.owner=a.owner')
            ->leftJoin('bids bid', 'bid.bid_id=b.current_bid')
            ->where('a.id_user=:user_id AND b.status=:status');
    }
}
