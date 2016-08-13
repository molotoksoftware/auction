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
 * Class GridView
 */
class GridView
{
    const SIZE_PARAM = 'size';

    const DEFAULT_SIZE = 10;

    /**
     * @return string
     */
    public static function pageSizeDropDown()
    {
        $getParam = self::SIZE_PARAM;
        $selectedPageSize = self::getPageSize();
        $name = 'gridPageSizeDropDown';
        $options = [
            10  => 10,
            50  => 50,
            100 => 100,
            250 => 250,
        ];
        $html = CHtml::dropDownList($name, $selectedPageSize, $options, ['class' => 'form-control']);

        Yii::app()->clientScript->registerScript($name, "
        var pageSizeDropDown = $('select[name=\"{$name}\"]');
        var grid = pageSizeDropDown.parent().parent().parent();
        pageSizeDropDown.on('change', function(){
            var selectedOption = $(this);
            if (selectedOption.val() !== '') {
                var urlJson = getJsonFromUrl();
                urlJson['{$getParam}'] = selectedOption.val();
                $.fn.yiiGridView.update(grid.attr('id'), {
                    data: urlJson
                });
            }
        });
        ");

        return $html;
    }

    public static function getPageSize()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();
        return $request->getQuery(self::SIZE_PARAM, self::DEFAULT_SIZE);
    }
}