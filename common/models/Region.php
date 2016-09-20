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


class Region extends CActiveRecord
{
    public function tableName()
    {
        return 'region';
    }

    public function defaultScope()
    {
        return array(
            //'order' => 'order_weight DESC'
        );
    }

    public function rules()
    {
        return array(
            array('id_country, name', 'required'),
            array('id_region, id_country, name', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'country' => array(self::BELONGS_TO, 'Country', 'id_country'),
            'cities' => array(self::HAS_MANY, 'City', 'id_region')
        );
    }

    public function attributeLabels()
    {
        return array(
            'id_region' => 'ID',
            'id_country' => 'Страна',
            'name' => 'Регион',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id_region', $this->id_region);
        $criteria->compare('id_country', $this->id_country);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getRegionsByCountry($id_country)
    {
        $regions = Yii::app()->cache ? Yii::app()->cache->get('regions-'.$id_country) : false;

        if ($regions === false)
        {
            $regions = Region::model()->findAllByAttributes(array('id_country' => $id_country));
            if(Yii::app()->cache) {
                Yii::app()->cache->set('regions-'.$id_country, $regions, Yii::app()->params['cache_duration']);
            }
        }

        return $regions;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function getNameById($id)
    {
        $cityName = Yii::app()
            ->getDb()
            ->createCommand()
            ->select('name')
            ->from(self::model()->tableName())
            ->where('id_region = :id_region', [':id_region' => (int)$id])
            ->queryScalar();
        return $cityName;
    }
}
