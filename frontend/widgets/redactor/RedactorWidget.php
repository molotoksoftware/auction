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


class RedactorWidget extends CInputWidget
{

    public $assets;
    public $idField;

    public function init()
    {
        $this->assets = Yii::app()->assetManager->publish(dirname(__FILE__) . '/assets');
        Yii::app()->clientScript
                ->registerCoreScript('jquery')
                ->registerScriptFile($this->assets . '/js/jquery.wysibb.min.js')
                ->registerCssFile($this->assets . '/css/default/wbbtheme.css');

        Yii::app()->clientScript
                ->registerScript('redactor_wysibb', "
                    var wbbOpt = {
                        buttons: 'bold,italic,underline,link,smilebox',
                        themePrefix:'',
                        themeName:''
                    }

                    $('#".$this->idField."').wysibb(wbbOpt);  
        ");
    }

    public function run()
    {
        $htmlOptions = array('id' => $this->idField);
        $htmlOptions = CMap::mergeArray($htmlOptions, $this->htmlOptions);
        echo CHtml::textArea($this->name, $this->value, $htmlOptions);
    }

}
