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


class City extends CActiveRecord
{
    public function tableName()
    {
        return 'city';
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
            array('id_region, id_country, name', 'required'),
            array('id_city, id_region, id_country, name', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'country' => array(self::BELONGS_TO, 'Country', 'id_country'),
            'region' => array(self::BELONGS_TO, 'Region', 'id_region')
        );
    }

    public function attributeLabels()
    {
        return array(
            'id_city' => 'ID',
            'id_region' => 'Регион',
            'id_country' => 'Страна',
            'name' => 'Регион',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id_city', $this->id_city);
        $criteria->compare('id_region', $this->id_region);
        $criteria->compare('id_country', $this->id_country);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getCitiesByRegion($id_region)
    {
        $cities = Yii::app()->cache ? Yii::app()->cache->get('cities-of-region-'.$id_region) : false;

        if ($cities === false)
        {
            $cities = City::model()->findAllByAttributes(array('id_region' => $id_region));
            if(Yii::app()->cache) {
                Yii::app()->cache->set('cities-of-region-'.$id_region, $cities, Yii::app()->params['cache_duration']);
            }
        }

        return $cities;
    }

    public static function cityPath($id_city) {
        /** @var City $city */
        $city = City::model()->with('region', 'country')->findByPk($id_city);
        return self::getCityPathString($city);
    }

    /**
     * @param City $city
     *
     * @return string
     */
    public static function getCityPathString(City $city)
    {
        return CHtml::encode($city->country->name . ', ' . $city->region->name . ', ' . $city->name);
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
            ->where('id_city = :id_city', [':id_city' => (int)$id])
            ->queryScalar();
        return $cityName;
    }

    public static function isMoscowCityId($id)
    {
        return $id == 1;
    }

    /**
     * @param array       $ids
     * @param string      $columns
     * @param null|string $indexBy
     * @param bool|true   $asArray
     *
     * @return array
     * @throws CDbException
     */
    public static function getByIds($ids, $columns = '*', $indexBy = null, $asArray = true)
    {
        return ActiveRecord::findAllByIds(
            self::model()->tableName(),
            'id_city',
            $ids,
            $columns,
            $indexBy,
            $asArray
        );
    }
}
