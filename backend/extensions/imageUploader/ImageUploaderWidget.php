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



class ImageUploaderWidget extends CWidget
{

    public $assets;
    public $model;

    public function init()
    {
        $this->assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets');
    }
    
    public function run()
    {
        $cs = Yii::app()->clientScript;
        
        $cs->registerCoreScript('jquery')
           ->registerCoreScript('jquery.ui');

        
        if (YII_DEBUG) {
            $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.js');
        } else {
            $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.min.js');
        }
        
        //        $cs->registerScript('galleryManager#' . $this->id, "$('#{$this->id}').galleryManager({$opts});");
        $this->render('view', array('model' => $this->model));
        
    }

}
