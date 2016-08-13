<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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



?>


<script type="text/javascript/javascript">
// validate int for type_text_range
function type_text_range_check(input) {
    ch = input.value.replace(/[^\d]/g, '');
    if (ch.length == 1 && ch==0){ch = ch.slice(0, -1);}
    input.value = ch;
};
</script>

<?php

foreach ($options as $option) {
    switch ($option['type']) {
        case Attribute::TYPE_DROPDOWN:
            echo "<div class=\"control-group\">";
            echo CHtml::label($option['name'], 'options_' . $option['attribute_id'], array('class' => 'control-label'));
            echo "<div class='controls'>";
            $selected_id = (isset($selected) && $selected) ? $option['value_id'] : '';
            echo CHtml::dropDownList(
                'options[0][' . $option['attribute_id'] . ']',
                $selected_id,
                getAttributeValues($option['attribute_id']),
                array('class' => 'span8',)
            );
            echo "</div>";
            echo "</div>";
            break;
        case Attribute::TYPE_RADIO_LIST:
            echo "<div class=\"control-group\">";
            echo CHtml::label($option['name'], 'options_' . $option['attribute_id'], array('class' => 'control-label'));
            echo "<div class='controls'>";
            $values = getAttributeValues($option['attribute_id']);
            $default_value = '';
            if (!empty($values)) {
                $tmp = array_keys($values);
                $default_value = array_shift($tmp);
            }
            $selected_id = (isset($selected) && $selected) ? $option['value_id'] : $default_value;
            echo CHtml::radioButtonList(
                'options[0][' . $option['attribute_id'] . ']',
                $selected_id,
                $values,
                array(
                    'separator' => '',
                    'template' => '<label class="radio">{input} {label}</label>'
                )
            );
            echo "</div>";
            echo "</div>";
            break;
        case Attribute::TYPE_CHECKBOX_LIST:
            echo "<div class=\"control-group\">";
            echo CHtml::label($option['name'], 'options_' . $option['attribute_id'], array('class' => 'control-label'));
            echo "<div class='controls'>";
            $values = getAttributeValues($option['attribute_id']);
            $selected_values = (isset($selected) && $selected) ? getSelectedValues(
                $option['attribute_id'],
                $option['auction_id']
            ) : array();
            echo CHtml::checkBoxList(
                'options[0][' . $option['attribute_id'] . ']',
                $selected_values,
                $values,
                array(
                    'labelCssClass' => 'checkbox',
                    'template' => '<label class="checkbox">{input}{label}</label>'
                )
            );
            echo "</div>";
            echo "</div>";
            break;
        case Attribute::TYPE_TEXT:
            echo "<div class=\"control-group\">";
            echo CHtml::label($option['name'], 'options_' . $option['attribute_id'], array('class' => 'control-label'));
            echo "<div class='controls'>";
            $value = (isset($selected) && $selected) ? $option['value'] : '';
            echo CHtml::telField('options[1][' . $option['attribute_id'] . ']', $value, array('class' => 'span8'));
            echo "</div>";
            echo "</div>";
            break;
        case Attribute::TYPE_TEXT_AREA:
            echo "<div class=\"control-group\">";
            echo CHtml::label($option['name'], 'options_' . $option['attribute_id'], array('class' => 'control-label'));
            echo "<div class='controls'>";
            $value = (isset($selected) && $selected) ? $option['value'] : '';
            echo CHtml::textArea('options[1][' . $option['attribute_id'] . ']', $value, array('class' => 'span8'));
            echo "</div>";
            echo "</div>";
            break;
        
        case Attribute::TYPE_TEXT_RANGE:
            echo "<div class=\"control-group\">";
            echo CHtml::label($option['name'], 'options_' . $option['attribute_id'], array('class' => 'control-label'));
            echo "<div class='controls'>";
            $value = (isset($selected) && $selected) ? $option['value'] : '';
            echo CHtml::telField('options[1][' . $option['attribute_id'] . ']', $value, array('class' => 'span8', 'onkeyUp' => 'return type_text_range_check(this);', 'placeholder' => 'введите целое число'));
            echo "</div>";
            echo "</div>";
            break;
        
        case Attribute::TYPE_DEPENDET_SELECT:
            $selected_id = (isset($selected) && $selected) ? $option['value_id'] : '';
            $rootAttr = Attribute::model()->findByPk($option['attribute_id']);

            $childAttr = Attribute::model()->findByPk($rootAttr->child_id);
            if (is_null($childAttr)) {
                echo CHtml::tag('p', 'Не определен дочерний элемент');
                break;
            }
            $childAttrName = 'options_0_' . $childAttr->attribute_id;


            echo "<div class=\"control-group\">";
            echo CHtml::label(
                $option['name'],
                'options_0_' . $option['attribute_id'],
                array('class' => 'control-label')
            );
            echo "<div class='controls'>";
            echo Chtml::dropDownList(
                'options[0][' . $option['attribute_id'] . ']',
                $selected_id,
                getAttributeValues($option['attribute_id']),
                array(
                    'class' => 'span8',
                    'empty' => '- выберите значения -',
                    'onchange' => new CJavaScriptExpression('
                       var select_id = $("#' . 'options_0_' . $option['attribute_id'] . '").find("option:selected").val();
                       $.ajax({
                        url:      "' . Yii::app()->createUrl('/catalog/attribute/getChildValues') . '",
                        data:     {"id":select_id},
                        type:     "GET",
                        dataType: "json",
                        beforeSend: function() {
                                 $("#' . $childAttrName . '").val("");
                                 $("#' . $childAttrName . '").find("option:selected").html("идет загрузка...");
                                 $("#' . $childAttrName . '").attr("disabled", true);
                        },
                        success: function(data) {
                                 $("#' . $childAttrName . '").html(data.options);
                                 $("#' . $childAttrName . '").attr("disabled", false);
                        }

                       });


                    ')
                )
            );

            echo "</div>";
            echo "</div>";

            echo "<div class=\"control-group\">";
            echo CHtml::label(
                $childAttr->name,
                'options_0_' . $childAttr->attribute_id,
                array('class' => 'control-label')
            );
            echo "<div class='controls'>";

            $selected_child_id = '';
            if (isset($selected) && $selected) {
                $aav = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('auction_attribute_value')
                    ->where(
                        'auction_id=:auction_id and attribute_id=:attribute_id',
                        array(
                            ':auction_id' => $auction_id,
                            ':attribute_id' => $childAttr->attribute_id
                        )
                    )
                    ->queryRow();
                if ($aav !== false) {
                    $selected_child_id=$aav['value_id'];
                }
            }
            $depValues = getDependentAttrValues($selected_id);

            echo CHtml::dropDownList(
                'options[0][' . $childAttr->attribute_id . ']',
                $selected_child_id,
                $depValues,
                array(
                    'class' => 'span8',
                    'empty' => '- выберите значения -'
                )
            );

            echo "</div>";
            echo "</div>";

            break;

    }
}

function getAttributeValues($attr_id)
{
    $data = array();
    $values = Yii::app()->db->createCommand()
        ->select('value, value_id')
        ->from('attribute_values')
        ->where('attribute_id=:attribute_id', array(':attribute_id' => $attr_id))
        ->order('sort ASC')
        ->queryAll();

    foreach ($values as $value) {
        $data[$value['value_id']] = $value['value'];
    }
    return $data;
}

function getDependentAttrValues($rootAttrId) {
    $aav = AttributeValues::model()->findAll('parent_id=:parent_id', array(
            ':parent_id'=>$rootAttrId
        ));
    return CHtml::listData($aav,'value_id','value');
}

function getSelectedValues($attr_id, $auction_id)
{
    $values = Yii::app()->db->createCommand()
        ->select('value_id')
        ->from('auction_attribute_value')
        ->where(
            'attribute_id=:attribute_id and auction_id=:auction_id',
            array(
                ':attribute_id' => $attr_id,
                ':auction_id' => $auction_id
            )
        )
        ->queryColumn();

    return $values;
}

