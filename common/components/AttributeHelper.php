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
 * Class AttributeHelper
 */
class AttributeHelper
{
    /**
     * @param array $attributes
     *
     * @return mixed
     */
    public static function makeNestedDependentExpanded($attributes)
    {
        foreach ($attributes as $id => $attribute) {
            if (self::isDependent($attribute) && $attribute['show_expanded']) {
                $attributes[$id]['child'] = $attributes[$attribute['child_id']];
                unset($attributes[$attribute['child_id']]);
            }
        }
        return $attributes;
    }

    /**
     * @param array $attribute
     *
     * @return bool
     */
    public static function isDependent($attribute)
    {
        return $attribute['type'] == Attribute::TYPE_DEPENDET_SELECT;
    }

    public static function getAttributeValues($attr_id = null, $parent_ids = null)
    {
        $query = Yii::app()->db->createCommand()
            ->select('value, value_id')
            ->from('attribute_values');

        if ($attr_id !== null) {
            $query->where('attribute_id=:attribute_id', [':attribute_id' => $attr_id]);

        } elseif ($parent_ids !== null) {
            $query->where(['in', 'parent_id', $parent_ids]);

        } else {
            return [];
        }

        $values = $query->order('sort ASC')->queryAll();

        $data = [];
        foreach ($values as $value) {
            $data[$value['value_id']] = $value['value'];
        }
        return $data;
    }
}