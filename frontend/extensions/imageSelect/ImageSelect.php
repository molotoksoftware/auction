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
class ImageSelect extends CWidget {

    private $id;
    public $text = 'Change Image';
    private $assetsDir;
    public $path;
    public $raiting;
    public $alt = '';
    public $uploadUrl;
    public $htmlOptions = array();
    public $type = 0;

    public function init() {
        $this->id = uniqid();
        $dir = dirname(__FILE__) . '/assets';
        $this->assetsDir = Yii::app()->assetManager->publish($dir);
        $this->register();

        Yii::app()->clientScript->registerScript(
                'script_' . $this->id, "
            $('#div_image_select_" . $this->id . " form').hide();
			$('#div_image_select_" . $this->id . " a').file().choose(function(e, input) {
				$('.image-select-loading').show();
				input.appendTo('#div_image_select_" . $this->id . " form');

				$('#div_image_select_" . $this->id . " form').ajaxSubmit({
					success : function(responseText){
                            var data = $.parseJSON(responseText);
                            if (data.response.status=='success') {
                                $('#mini-user-avatar').attr('src', data.response.data.avatar_mini + '?' + new Date().getTime());
                                $('#div_image_select_" . $this->id . " img').attr('src', data.response.data.avatar + '?' + new Date().getTime());
                                $('.image-select-loading').hide();

                                if (data.response.data.type == 1) {
                                    $('#img1').val(data.response.data.img);
                                }

                                if (data.response.data.type == 2) {
                                    $('#img2').val(data.response.data.img);
                                }
                            }
				    }
				});
			});"
                , CClientScript::POS_LOAD
        );
    }

    public function run() {

        echo '<div class="panel panel-default" id="div_image_select_' . $this->id . '">';
        echo '<div class="panel-heading">'.Yii::t('basic', 'Your photo').'</div><div class="panel-body"><div class="row">';
        echo '<div class="col-xs-2">';

        echo CHtml::image($this->path, $this->alt, $this->htmlOptions);

        echo CHtml::form(
                $this->uploadUrl, 'POST', array(
            'enctype' => "multipart/form-data",
            'id' => "frm_img_select"
                )
        );
        echo CHtml::endForm();
        echo '</div>';
        echo '<div class="col-xs-10">';
        echo '<a>'.Yii::t('basic', 'Change photo').'</a>';
        echo '<p>'.Yii::t('basic', 'Change photo for your profile').'</p>';
        echo '</div></div></div>';
        echo '</div>';
    }

    public function register($rtl = false) {
        $this->registerCss($rtl);
        $this->registerScripts($rtl);
    }

    public function registerCss($rtl = false) {
        Yii::app()->clientScript->registerCssFile($this->assetsDir . '/css/hidden-file-input.css');
        Yii::app()->clientScript->registerCssFile($this->assetsDir . '/css/image-select.css');
    }

    public function registerScripts($rtl) {
        Yii::app()->clientScript->registerScriptFile($this->assetsDir . '/js/jquery.form.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile(
                $this->assetsDir . '/js/jquery-custom-file-input.js', CClientScript::POS_END
        );
    }

}
