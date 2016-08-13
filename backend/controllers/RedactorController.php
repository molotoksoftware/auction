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



class RedactorController extends BackController
{

    const FILE_PATH = '/uploads/redactor/images/';

    public function filters()
    {
        return array(
                // 'accessControl',
        );
    }

    public function getBaseUrl()
    {
        return str_replace('admin.', '', Yii::app()->request->getHostInfo());
    }

    public function actionUploadedImages()
    {
        $images = array();
        $handler = opendir(Yii::getPathOfAlias('frontend') . '/www' . self::FILE_PATH);
        while ($file = readdir($handler)) {
            if ($file != "." && $file != "..")
                $images[] = $file;
        }
        closedir($handler);
        $jsonArray = array();
        foreach ($images as $image)
            $jsonArray[] = array(
                'thumb' => $this->getBaseUrl() . self::FILE_PATH . $image,
                'image' => $this->getBaseUrl() . self::FILE_PATH . $image
            );

        header('Content-type: application/json');
        echo CJSON::encode($jsonArray);
        Yii::app()->end();
    }

    public function actionImageUpload()
    {
        $file = CUploadedFile::getInstanceByName('file');
        $file_name = md5(date('YmdHis')) . '.jpg';

        if ($file->saveAs(Yii::getPathOfAlias('frontend') . '/www' . self::FILE_PATH . $file_name)) {

            $array = array(
                'filelink' => $this->getBaseUrl() . self::FILE_PATH . $file_name
            );
            header('Content-type: application/json');
            echo CJSON::encode($array);
            Yii::app()->end();
        }

        throw new CHttpException(403, 'The server is crying in pain as you try to upload bad stuff');
    }

}
