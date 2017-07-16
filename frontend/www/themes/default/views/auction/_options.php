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


cs()->registerCssFile(bu() . '/js/libs/jquery_multiselect/bootstrap-multiselect.css');
cs()->registerScriptFile(bu() . '/js/libs/jquery_multiselect/bootstrap-multiselect.js');


cs()->registerScript(
    'random',
    '

    $(".multiselect").multiselect({
        buttonWidth: "200pх",
        nonSelectedText: "'.Yii::t('basic', '- select value -').'",
        numberDisplayed: 5,
        enableCaseInsensitiveFiltering: true,
        allSelectedText: "'.Yii::t('basic', 'Selected').'",
        filterPlaceholder: "'.Yii::t('basic', 'Search').'",

    });

    ',
    CClientScript::POS_READY
);


$lastParentOption = null;

foreach ($options as $option) {
    if (!empty($option['child_id'])) {
        $lastParentOption = $option;
    }

    if (($option['type'] == Attribute::TYPE_DROPDOWN)
        or ($option['type'] == Attribute::TYPE_RADIO_LIST)
        or ($option['type'] == Attribute::TYPE_DEPENDET_SELECT)
        or ($option['type'] == Attribute::TYPE_CHILD_ELEMENT)
        or ($option['type'] == Attribute::TYPE_CHECKBOX_LIST)
    ) {

        $for = CHtml::activeId($filter, '[option][0]' . $option['attribute_id']);
        echo "<div class='form-group form-inline " . ($option['show_expanded'] ? 'row-expanded' : '') . "'>";
        echo CHtml::label($option['name'], $for);
        echo "<br>";

        switch ($option['type'])
        {
            case 9:
                $select_class = 'multiselect parent_id_'.$option['attribute_id'].' child_id_'.$option['child_id'].' multi_parent';
                break;
            default:
                $select_class = 'multiselect';
        }

        $attributeValues = AttributeHelper::getAttributeValues($option['attribute_id']);
        if ($lastParentOption && $lastParentOption['child_id'] == $option['attribute_id']) {
            $parentAttributeIds = [-1];
            if (!empty($_GET['Filter']['option'][0][$lastParentOption['attribute_id']])) {
                $parentAttributeIds = $_GET['Filter']['option'][0][$lastParentOption['attribute_id']];
            }
            $attributeValues = AttributeHelper::getAttributeValues(null, $parentAttributeIds);
        }

        if ($option['type'] == Attribute::TYPE_DEPENDET_SELECT && !empty($option['child'])) {
            renderDependentWithChild($option, $filter);

        } else {

            if (!$option['show_expanded']) {
                echo CHtml::activeDropDownList(
                    $filter, 'option[0][' . $option['attribute_id'] . ']',
                    $attributeValues,
                    [
                        'class'    => $select_class,
                        'multiple' => "multiple",
                    ]
                );
            } else {
                echo CHtml::activeCheckBoxList(
                    $filter, 'option[0][' . $option['attribute_id'] . ']',
                    $attributeValues, ['uncheckValue' => null]
                );
            }
        }

        echo "</div>";
        echo "<br/>";
    }

    if ($option['type'] == Attribute::TYPE_TEXT_RANGE)
    {
        if (isset($_GET['Filter']['option'][1][$option['attribute_id']]['from']) && preg_match("/^[0-9]+$/", $_GET['Filter']['option'][1][$option['attribute_id']]['from']) && $_GET['Filter']['option'][1][$option['attribute_id']]['from'] > 0) {$from = $_GET['Filter']['option'][1][$option['attribute_id']]['from'];} else {$from = null;}
        if (isset($_GET['Filter']['option'][1][$option['attribute_id']]['to']) && preg_match("/^[0-9]+$/", $_GET['Filter']['option'][1][$option['attribute_id']]['to']) && $_GET['Filter']['option'][1][$option['attribute_id']]['to'] > 0) {$to = $_GET['Filter']['option'][1][$option['attribute_id']]['to'];} else {$to = null;}
        echo CHtml::label($option['name'], '');
        echo "<div class='form-group form-inline'>".
            CHtml::textField('Filter[option][1][' . $option['attribute_id'] . '][from]', $from, array('class' => 'form-control options_range', 'placeholder' => Yii::t('basic', 'From'), 'onkeyUp'=>'return type_text_range_check(this);')).' - '.
            CHtml::textField('Filter[option][1][' . $option['attribute_id'] . '][to]', $to, array('class' => 'form-control options_range', 'placeholder' => Yii::t('basic', 'To'), 'onkeyUp'=>'return type_text_range_check(this);')).
            ' <input type="submit" value=">" class="btn btn-default">'.
            "</div>";
        echo "<br/>";
    }
}

/**
 * @param array  $option
 * @param Filter $filter
 */
function renderDependentWithChild($option, $filter)
{
    $parentValues = AttributeHelper::getAttributeValues($option['attribute_id']);

    Yii::app()->getController()->renderPartial('//auction/blocks/_dependent_with_child', [
        'model'           => $filter,
        'parentValues'    => $parentValues,
        'parentAttribute' => $option,
        'childAttribute'  => $option['child'],
    ]);
}
