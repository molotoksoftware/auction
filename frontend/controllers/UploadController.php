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
 * Class UploadController
 */
class UploadController extends FrontController
{

    public $thumbs = array(
        'width' => 165,
        'height' => 155,
        'quality' => 75
    );

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('upload', 'delete'),
                'users' => array('*'),
            ),
            array('deny'),
        );
    }


    public function actionUpload($type = null)
    {
        if ($type == 1)
        {
            if (isset(Yii::app()->session['mass_upload']) && !empty(Yii::app()->session['mass_upload']))
            {
                $_POST['identifier'] = Yii::app()->session['mass_upload'];
            }
            else
            {
                Yii::app()->session['mass_upload'] = $_POST['identifier'];
            }
        }

        $dir = Yii::app()->basePath . '/www/tmp/' . $_POST['identifier'];

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        Yii::import('frontend.widgets.imageUploader.FileModel');
        $model = new FileModel();
        $file = CUploadedFile::getInstanceByName('image');
        $model->file = $file;

        if (!$model->validate()) {
            RAjax::error($model->getErrors());
        }

        if (isset($type) && $type == 1)
        {
            $name = md5(microtime());
            //$old_name = str_replace('.'.$file->getExtensionName(), '', $file->name);
            $file_name = $name .'_:!!:_'.$file->name.'_:!!:_'. '.' . $file->getExtensionName();
        }
        else
        {
            $name = md5(microtime());
            $file_name = $name . '.' . $file->getExtensionName();
        }

        $tmp = glob($dir . '/*');
        $count = ($tmp !== false) ? count($tmp) : 0;

        if (!isset($type))
        {
            $userModel = Getter::userModel();
            if ($userModel && ($userModel->getIsPro())) {
                $allowedQuantity = (int)Yii::app()->params['quantityFotoForPro'];
            } else {
                $allowedQuantity = (int)Yii::app()->params['quantityUploaddFoto'];
            }
        }
        else
        {
            $allowedQuantity = (int)Yii::app()->params['quantityUploadMass'];
        }

        if ($count > $allowedQuantity) {
            RAjax::error(array('file' => [Yii::t('basic', 'Max. photo: {count}', ['{count}' => $allowedQuantity])]));
        }

        if ($file->saveAs($dir . '/' . $file_name)) {

            if (!is_dir($dir . '/thumbs/')) {
                mkdir($dir . '/thumbs/');
            }

            Getter::imageHandler()
                ->load($dir . '/' . $file_name)
                ->thumb(150, 150)
                ->save($dir . '/thumbs/' . $file_name);

            RAjax::success(
                array(
                    'file' => Yii::app()->request->getHostInfo(
                    ) . '/tmp/' . $_POST['identifier'] . '/thumbs/' . $file_name,
                    'name' => $file_name,
                    'size' => Yii::app()->format->size($file->getSize()),
                    'count' => $count,
                    'allow' => $allowedQuantity
                )
            );
        } else {
            RAjax::error();
        }
    }

    public function actionDelete()
    {
        if (isset($_POST['storage']) && isset($_POST['identifier']) && isset($_POST['id'])) {

            if ($_POST['storage'] == 'tmp') 
            {
                if (@unlink(Yii::app()->basePath . '/www/tmp/' . $_POST['identifier'] . '/' . $_POST['id'])) {
                    @unlink(Yii::app()->basePath . '/www/tmp/' . $_POST['identifier'] . '/thumbs/' . $_POST['id']);
                    RAjax::success();
                } else {
                    RAjax::error(array('message' => 'error delete'));
                }
            } 
            elseif ($_POST['storage'] == 'locale') 
            {
                $id = $_POST['id'];
                $pk = $_POST['pk'];
                $splitId = explode("_", $id);
                /** @var ImageAR $image */
                $image = null;
                if ($pk) {
                    $image = ImageAR::model()->findByPk((int)$pk);
                }
                if (!$image) {
                    $image = ImageAR::model()->findByAttributes(['image' => $id]);
                }
                if (!$image && isset($splitId[1])) {
                    $image = ImageAR::model()->findByAttributes(['image_id' => $splitId[1]]);
                }
                $user_id = 0;

                if (!$image) RAjax::error(array('message' => 'image not found'));

                if ($_POST['model'] == 'Auction' || $_POST['model'] == 'Advert') {
                    //delete main image
                    $auction = Auction::model()->findByPk($image->item_id);

                    if($auction) {
                        $user_id = $auction->owner;

                        if ($auction->image == $image->image) {
                            $auction->image = '';
                            $auction->save();
                        }
                    }
                }

                if ($image->deleteAndUnlink($user_id)) {
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
