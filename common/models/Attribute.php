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
 * This is the model class for table "attribute".
 *
 * @property string $attribute_id
 * @property string $child_id
 * @property string $name
 * @property string $sys_name
 * @property string $identifier
 * @property integer $type
 * @property string $description
 * @property string $display_preview_page
 * @property string $display_filter
 * @property string $update
 * @property bool $show_expanded
 */
class Attribute extends CActiveRecord
{
    const TYPE_DROPDOWN = 1;
    const TYPE_RADIO_LIST = 3;
    const TYPE_CHECKBOX_LIST = 4;
    const TYPE_YES_NO = 5;
    const TYPE_TEXT = 6;
    const TYPE_TEXT_AREA = 7;
    const TYPE_CHILD_ELEMENT = 8;
    const TYPE_DEPENDET_SELECT = 9;
    const TYPE_TEXT_RANGE = 10;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'attribute';
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'attr'
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('sys_name', 'length'),
            array('identifier', 'unique'),
            array('child_id', 'numerical'),
            array('display_preview_page, display_filter, mandatory, show_expanded', 'boolean'),
            array('type', 'numerical', 'integerOnly' => true),
            array('name, description', 'length', 'max' => 255),
            array('attribute_id, name, identifier', 'safe', 'on' => 'search'),
        );
    }

    public function beforeValidate()
    {
        if (empty($this->sys_name)) {
            $this->sys_name = $this->name;
        }

        if ($this->type == self::TYPE_DEPENDET_SELECT) {
            if (empty($this->child_id)) {
                $this->addError('child_id', 'Необходимо выбрать дочерний элемент');
            }
        }
        return parent::beforeValidate();
    }

//

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'values' => array(self::HAS_MANY, 'AttributeValues', 'attribute_id')
        );
    }

    public function behaviors()
    {
        return array(
            'translit' => array(
                'class' => 'common.extensions.behaviors.translit.TranslitBehavior',
                'sourceAttribute' => 'sys_name',
                'aliasAttribute' => 'identifier'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'attribute_id'         => 'Attribute',
            'child_id'             => 'Дочерний элемент',
            'name'                 => 'Название',
            'sys_name'             => 'Название',
            'identifier'           => 'Идентификатор',
            'type'                 => 'Тип',
            'description'          => 'Описание',
            'display_preview_page' => 'Отображать на странице аукционов',
            'display_filter'       => 'Отображать в фильтре',
            'mandatory'            => 'Обязательный для заполнения',
            'show_expanded'        => 'Отображать развернутым',
        ];
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search()
    {

        $criteria = new CDbCriteria;

        $criteria->compare('attribute_id', $this->attribute_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('sys_name', $this->sys_name, true);
        $criteria->compare('identifier', $this->identifier, true);
        $criteria->compare('type', $this->type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 100
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Attribute the static model class
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

    public static function getAvailableType()
    {
        return array(
            'Выбор' => array(
                self::TYPE_DROPDOWN => 'Список',
                self::TYPE_RADIO_LIST => 'Переключатель',
                self::TYPE_CHECKBOX_LIST => 'Флажок',
            ),
            'Поле ввода' => array(
                self::TYPE_TEXT => 'Текст',
                self::TYPE_TEXT_AREA => 'Текстовая область',
                self::TYPE_TEXT_RANGE => 'Диапазон',
            ),
         /*   'Другое' => array(
                self::TYPE_CHILD_ELEMENT => 'Использовать как дочерний атрибут'
            )*/
        );
    }

    /**
     * возвращает форматированный вывод для таблицы
     */
    public function getTypeForTable()
    {
        if ($this->type == self::TYPE_DEPENDET_SELECT) {
            return "<span class='label label-cyan'>зависимый список</span>";
        }

        $data = self::getAvailableType();
        foreach ($data as $item) {
            if (isset($item[$this->type])) {
                $ico = 'label-blue';
                if ($this->type == self::TYPE_CHILD_ELEMENT) {
                    $ico = 'label-grey';
                }

                return "<span class='label " . $ico . "'>" . mb_strtolower(
                    $item[$this->type],
                    Yii::app()->charset
                ) . "</span>";
            }
        }
        return '*';
    }

    public function getDisplayPreviewAdminTable()
    {
        if ($this->display_preview_page) {
            return '<i class="icon-eye-open"></i>';
        } else {
            return '<i class="icon-eye-close"></i>';
        }
    }

    public function getDisplayFilterAdminTable()
    {
        if ($this->display_filter) {
            return '<i class="icon-filter"></i>';
        } else {
            return '';
        }
    }

    public static function getAllChildAttribute()
    {
        $attribute = self::model()->findAll(
            'type=:type',
            array(
                ':type' => Attribute::TYPE_CHILD_ELEMENT
            )
        );
        return CHtml::listData($attribute, 'attribute_id', 'sys_name');
    }

    public function beforeDelete()
    {
        $values = AttributeValues::model()->findAll(
            'attribute_id=:attribute_id',
            array(
                ':attribute_id' => $this->attribute_id
            )
        );
        if (!empty($values)) {
            foreach ($values as $v) {
                $v->delete();
            }
        }

        Yii::app()->db->createCommand()
            ->delete('auction_attribute_value','attribute_id=:attribute_id',array(':attribute_id'=>$this->attribute_id));

        return parent::beforeDelete();
    }

    public function allowedAttribute()
    {
        return $this;

        $this->getDbCriteria()->mergeWith(
            array(
                'condition' => 'type not in(:not_types)',
                'params' => array(
                    ':not_types' => implode(
                        ',',
                        array(
                            self::TYPE_CHILD_ELEMENT
                        )
                    )
                )
            )
        );
        return $this;
    }
}