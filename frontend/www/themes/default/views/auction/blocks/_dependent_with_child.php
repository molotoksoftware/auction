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

/** @var array $parentValues */
/** @var Filter $model */
/** @var array $parentAttribute */
/** @var array $childAttribute */


?>
    <div class="attr-filter-tree">

        <ul>
            <?php foreach ($parentValues as $parentId => $parentName): ?>
                <?php
                $parentIsChecked = isChecked($parentAttribute['attribute_id'], $parentId);
                ?>
                <li class="aft-item aft-item-parent">
                    <label>
                        <?=CHtml::activeCheckBox($model, "option[0][{$parentAttribute['attribute_id']}][]", [
                            'value'        => $parentId,
                            'checked'      => $parentIsChecked,
                            'uncheckValue' => null,
                        ])?>
                        <?=$parentName?>
                    </label>

                    <?php
                    $childValues = AttributeHelper::getAttributeValues(null, [$parentId]);
                    $childIsActive = $parentIsChecked || childIsChecked($childAttribute['attribute_id'], $childValues);
                    ?>

                    <?php if (!empty($childValues)): ?>
                        <span class="tree-node-btn <?=$childIsActive ? 'active' : ''?>"></span>

                        <ul class="aft-item-child-cont <?=$childIsActive ? 'active' : ''?>">
                            <?php foreach ($childValues as $childId => $childName): ?>
                                <li class="aft-item aft-item-child">
                                    <label>
                                        <?=CHtml::activeCheckBox($model,
                                            "option[0][{$childAttribute['attribute_id']}][]",
                                            [
                                                'value' => $childId . '_' . $parentId,
                                                'checked'      => isChecked($childAttribute['attribute_id'], $childId),
                                                'uncheckValue' => null,
                                            ]
                                        )?>
                                        <?=$childName?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
<?php

function isChecked($attrId, $value)
{
    if (!empty($_GET['Filter']['option'][0][$attrId])) {
        return array_search($value, $_GET['Filter']['option'][0][$attrId]) !== false;
    }
    return false;
}

function childIsChecked($attrId, $childValues)
{
    foreach ($childValues as $childId => $childName) {
        $isChecked = isChecked($attrId, $childId);
        if ($isChecked) {
            return true;
        }
    }
    return false;
}

?>