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
 * Class ImageUploaderWidget
 */
class ImageUploaderWidget extends CWidget
{

    public $assets;
    public $model;
    public $type;

    public function init()
    {
        $this->assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets');
    }

    public function run()
    {

        if (isset($this->type) && $this->type == 1)
        {
            $options = array(
                'uploadUrl' => Yii::app()->createUrl('/upload/upload/type/1'),
                'deleteUrl' => Yii::app()->createUrl('/upload/delete')
            );
            
            $type = 1;
        }
        else
        {
            $options = array(
                'uploadUrl' => Yii::app()->createUrl('/upload/upload'),
                'deleteUrl' => Yii::app()->createUrl('/upload/delete')
            );
            
            $type = 0;
        }

        if (Yii::app()->request->enableCsrfValidation) {
            $options['csrfTokenName'] = Yii::app()->request->csrfTokenName;
            $options['csrfToken'] = Yii::app()->request->csrfToken;
        }

        $options['mainPhotoText'] = Yii::t('basic', 'Main photo');
        $options['additionalPhotoText'] =Yii::t('basic', 'Additional photos');

        $options = CJavaScript::encode($options);
        $cs = Yii::app()->clientScript;

        if (isset($this->type) && $this->type == 1)
        {
            $cs->registerCoreScript('jquery')
                ->registerCoreScript('jquery.ui')
                ->registerScriptFile(bu() . '/js/jquery.tmpl.min.js')
                ->registerScriptFile($this->assets . '/image-uploader2.js')
                ->registerScript('imageUploader#' . $this->id, "$('#image-upload-block').imageUploader({$options});");
        }
        else
        {
            $cs->registerCoreScript('jquery')
                ->registerCoreScript('jquery.ui')
                ->registerScriptFile(bu() . '/js/jquery.tmpl.min.js')
                ->registerScriptFile($this->assets . '/image-uploader.js')
                ->registerScript('imageUploader#' . $this->id, "$('#image-upload-block').imageUploader({$options});");
        }

        if (YII_DEBUG) {
            $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.js');
        } else {
            $cs->registerScriptFile($this->assets . '/jquery.iframe-transport.min.js');
        }

        cs()->registerScript('create_rewiew', "

        $('.del-all-photos').click(function() {
            if(confirm('" . Yii::t('basic', 'Do you really want to delete these photos?'). "')) {
                $('.del-lot-photo').click();
            }
        })
        ", CClientScript::POS_END);

        $this->render(
            'view',
            array(
                'model' => $this->model,
                'type' => $type,
            )
        );
    }

}
