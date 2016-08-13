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


class SimpleImageUploadWidget extends CWidget
{
    
    public $model;
    public $form;
    public $attribute;
    public $width=120;
    public $height=120;
    public $versionName;
    
    public function init()
    {
        
        if (!Yii::app()->hasComponent('bootstrap')){
            throw new CException('Необходимо использовать компонент bootstrap');
        }
        
        $assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets');
        Yii::app()->clientScript
                ->registerCoreScript('jquery')
                ->registerCssFile($assets.'/css/bootstrap-fileupload.min.css')
                ->registerScriptFile($assets.'/js/bootstrap-fileupload.min.js');
    }
    
    public function run()
    {
        echo '<div class="control-group ">';
        echo $this->form->label($this->model, $this->attribute, array('class' => 'control-label'));
        echo '<div class="controls">';
        $class = (trim($this->model->{$this->attribute})!=='')?"fileupload-exists":"fileupload-new";
        echo '<div class="fileupload '.$class.'" data-provides="fileupload">';
        echo '<div class="fileupload-preview fileupload-exists thumbnail" style="width:' .$this->width .'px; height: '.$this->height.'px;">';
            
            if (trim($this->model->{$this->attribute}) !== '') {
                echo CHtml::image($this->model->getImage($this->versionName));
            }
        echo '</div>';
        echo '<div class="fileupload-new thumbnail" style="width: '.$this->width.'px; height: '.$this->height.'px;">';
        echo CHtml::image('http://www.placehold.it/'.$this->width.'x'.$this->height.'/EFEFEF/AAAAAA');
        echo '</div>';
        echo '<span class="btn btn-file btn-blue">';
        echo '<span class="fileupload-new">Выберите изображение</span>';
        echo '<span class="fileupload-exists">Изменить</span>';
        echo $this->form->fileField($this->model, $this->attribute); 
        echo '</span>';                      
        echo '<a href="#" class="btn btn-default  fileupload-exists" data-dismiss="fileupload">Удалить</a>';
        echo '</div>';
        echo $this->form->error($this->model, $this->attribute);
        echo  '</div>';
        echo '</div>';
    }
}

