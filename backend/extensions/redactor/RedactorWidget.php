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



Yii::import('ext.imperaviRedactor.ImperaviRedactorWidget');

class RedactorWidget extends ImperaviRedactorWidget
{

    public $pluginsImport;

    public function __construct()
    {
        parent::__construct();
        $this->options = array(
            'lang' => 'ru',
            'imageUpload' => Yii::app()->createAbsoluteUrl('/redactor/imageUpload'),
            'imageGetJson' => Yii::app()->createAbsoluteUrl('/redactor/uploadedImages')
        );

        $this->plugins = array(
            'fullscreen' => array(
                'js' => array('fullscreen.js',),
                /* 'clips' => array(
                     // Можно указать путь для публикации
                     'basePath' => 'application.components.imperavi.my_plugin',
                     // Можно указать ссылку на ресурсы плагина, в этом случае basePath игнорирутеся.
                     // По умолчанию, путь до папки plugins из ресурсов расширения
                     'baseUrl' => '/js/my_plugin',
                     'css' => array('clips.css',),
                     'js' => array('clips.js',),
                     // Можно также указывать зависимости
                     'depends' => array('imperavi-redactor',),
             )*/
            )
        );
    }

    public function init()
    {
        if (!is_null($this->pluginsImport)) {
            $this->plugins = CMap::mergeArray($this->plugins, $this->pluginsImport);
        }


        if ($this->selector === null) {
            list($this->name, $this->id) = $this->resolveNameID();
            $this->htmlOptions['id'] = $this->getId();
            $this->selector = '#' . $this->getId();
        }

        if ($this->hasModel()) {
            echo CHtml::openTag('div', array('class' => 'control-group'));
            echo CHtml::activeLabel(
                $this->model,
                $this->attribute,
                array('class' => 'control-label required', 'required' => true)
            );
            echo CHtml::openTag('div', array('class' => 'controls'));
            echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
            echo CHtml::closeTag('div');
            echo CHtml::closeTag('div');
        } else {
            echo CHtml::textArea($this->name, $this->value, $this->htmlOptions);
        }


        $this->registerClientScript();
    }

}
