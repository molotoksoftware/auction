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


class UploadController extends BackController
{

    public $thumbs = array(
        'width' => 120,
        'height' => 120,
        'quality' => 75
    );

    public function filters()
    {
        return array(
                // 'accessControl',
        );
    }


    public function actionUpload()
    {
        $dir = Yii::app()->basePath . '/www/tmp/' . $_POST['identifier'];
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $file = CUploadedFile::getInstanceByName('image');
        $name = md5(microtime());
        $file_name = $name . '.' . $file->getExtensionName();

        if ($file->saveAs($dir . '/' . $file_name)) {
            $thumb = Yii::app()->image->load($dir . '/' . $file_name);
            $thumb->cresize($this->thumbs['width'], $this->thumbs['height']);
            //->quality($this->thumbs['quality']);
            if (!is_dir($dir . '/thumbs/')) {
                mkdir($dir . '/thumbs/');
            }

            $thumb->save($dir . '/thumbs/' . $file_name);

            RAjax::success(array(
                'file' => Yii::app()->request->getHostInfo() . '/tmp/' . $_POST['identifier'] . '/thumbs/' . $file_name,
                'name' => $file_name
            ));
        } else {
            RAjax::error();
            var_dump($file->getError());
            echo "NO";
        }
    }

    public function actionDelete()
    {
        if (isset($_POST['storage']) && isset($_POST['identifier']) && isset($_POST['id'])) {
            if ($_POST['storage'] == 'tmp') {

                if (@unlink(Yii::app()->basePath . '/www/tmp/' . $_POST['identifier'] . '/' . $_POST['id'])) {
                    @unlink(Yii::app()->basePath . '/www/tmp/' . $_POST['identifier'] . '/thumbs/' . $_POST['id']);
                    RAjax::success();
                } else {
                    RAjax::error(array('message' => 'error delete'));
                }
            } elseif ($_POST['storage'] == 'locale') {
                Yii::app()->db->createCommand()
                        ->delete('images', 'image=:image', array(
                            ':image' => $_POST['id']));

               if ($_POST['model'] == 'Auction' || $_POST['model'] == 'Advert') {
                    //delete main image
                    $item = Yii::app()->db->createCommand()
                            ->select('auction_id')
                            ->from('auction')
                            ->where('image=:image', array(':image' => $_POST['id']))
                            ->queryRow();
                    if ($item !== false) {
                        Yii::app()->db->createCommand()
                                ->update('auction', array(
                                    'image' => ''
                                        ), 'auction_id=:auction_id', array(
                                    ':auction_id' => $item['auction_id']
                        ));
                    }
                }


                $path = realpath(Yii::getPathOfAlias('frontend')) . '/www/i/';

                if (@unlink($path . $_POST['id'])) {
                    foreach (Auction::$versions as $v_name => $v_params) {
                        @unlink($path . 'thumbs' . DIRECTORY_SEPARATOR . $v_name . '_' . $_POST['id']);
                    }
                    RAjax::success();
                } else {
                    RAjax::error(array('message' => 'error delete'));
                }
            }
        } else {
            RAjax::error(array('message' => 'no available data'));
        }
    }

}
