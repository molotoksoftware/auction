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



class MultiselectWidget extends CInputWidget
{

    public $data;

    public function init()
    {
        $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets');
        Yii::app()->clientScript
                ->registerCssFile($assets . '/css/ui.multiselect.css')
                ->registerCoreScript('jquery.ui')
                ->registerScriptFile($assets . '/js/plugins/localisation/jquery.localisation-min.js')
                ->registerScriptFile($assets . '/js/plugins/scrollTo/jquery.scrollTo-min.js')
                ->registerScriptFile($assets . '/js/ui.multiselect.js')
                ->registerScript(
                        $this->id, '$.localise("ui-multiselect", {language: "ru", path: "' . $assets . '/js/locale/' . '"});
                	$(".multiselect").multiselect();        
                        ', CClientScript::POS_READY
        );
    }

    public function run()
    {
        $htmlOptions = CMap::mergeArray($this->htmlOptions, array('multiple' => true, 'class' => 'multiselect'));

        if (!empty($this->value)) {
            //необходимо отсортировать в правильном порядке
            $data = array();
            foreach ($this->data as $d_key => $d_val) {
                if (!in_array($d_key, $this->value)) {
                    $data[][$d_key] = $d_val;
                }
            }

            foreach ($this->value as $item) {
                $data[][$item] = $this->data[$item];
            }
        } else {
            $data = $this->data;
        }



        echo CHtml::dropDownList($this->name, $this->value, $data, $htmlOptions);
    }

}

