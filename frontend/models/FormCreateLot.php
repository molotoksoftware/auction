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


class FormCreateLot extends CFormModel
{

    public $name;
    public $category_id;
    public $description;
    public $price;
    public $starting_price;
    public $conditions_transfer;
    public $duration;
    public $type_transaction;
    public $location;
    public $longitude;
    public $latitude;
    public $quantity;
    public $is_auto_republish;
	public $add_contact_info;
	public $contacts;

    public $id_country;
    public $id_region;
    public $id_city;

    public function rules()
    {
        return array(
            array('id_country, id_region, id_city', 'required', 'message' => Yii::t('basic', 'Select a location'), 'on' => 'main'),
            array('id_country, id_region, id_city', 'numerical', 'integerOnly' => true),
            array('name', 'required', 'message' => Yii::t('basic', 'Enter the name of the item'), 'on' => 'main'),
            array('category_id', 'required', 'message' => Yii::t('basic', 'Choice of category is not finished'), 'on' => 'main'),
            array('description', 'required', 'message' => Yii::t('basic', 'Enter the description of the item'), 'on' => 'main'),
            array('duration', 'required', 'message' => Yii::t('basic', 'Select the duration'), 'on' => 'main'),
			array('contacts', 'length'),
            array('conditions_transfer, contacts, category_id, location, duration, name', 'filter', 'filter' => array($obj = new CHtmlPurifier(), 'purify')),
            array('description', 'descValidator'),
			array(
                'price, starting_price',
                'numerical', 'numberPattern'=>'/^[0-9]{1,9}(\.[0-9]{1,2})?$/'
            ),
            array('conditions_transfer, add_contact_info', 'length'),
            array('is_auto_republish', 'boolean'),
            array('quantity', 'default', 'value' => 1),
            array('quantity', 'numerical', 'max' => 999),
            array('duration, type_transaction', 'numerical'),
        );
    }

    public function descValidator($attribute, $params)
    {
        $purifier = new CHtmlPurifier();
        $purifier->setOptions(array(
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
        ));
        $this->description = $purifier->purify($this->description);
        $this->description = str_replace('<iframe frameborder="', '<iframe allowfullscreen="" frameborder="', $this->description);
    }

    public function beforeValidate()
    {

        /* From 1 */
        if ($this->type_transaction == Auction::TP_TR_START_ONE) {
            $this->starting_price = 1;
        }


        /* Buy now */
        if ($this->type_transaction == Auction::TP_TR_SALE) {
            $this->starting_price = 0;
            if (((int)$this->price) <= 0 && $this->scenario != 'nomain') {
                $this->addError('price', Yii::t('basic', 'Specify price'));
            }
        }


        if ($this->type_transaction == Auction::TP_TR_STANDART) {

            if (!$this->hasErrors()) {
                if (((float)$this->starting_price) <= 0 && $this->scenario != 'nomain') {
                    $this->addError('starting_price', Yii::t('basic', 'Specify the starting price'));
                }
            }

            if (!$this->hasErrors()) {
                if (($this->price > 0) && ($this->starting_price > $this->price)) {
                    $this->addError('starting_price', Yii::t('basic', 'Starting price can\'t be more than buy now price'));
                }
            }

            if (!$this->hasErrors()) {
                if (($this->price > 0) && ($this->starting_price == $this->price)) {
                    $this->addError('starting_price', Yii::t('basic', 'Starting price can\'t be equal to the buy now price'));
                }
            }
        }


        return parent::beforeValidate();
    }


    public function attributeLabels()
    {
        return [
            'price'                 => Yii::t('basic', 'Buy Now'),
            'starting_price'        => Yii::t('basic', 'Specify price'),
            'quantity'              => Yii::t('basic', 'Йгфтешен'),
        ];
    }
}
