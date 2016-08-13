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



class SimpleImageUploadBehavior extends CActiveRecordBehavior
{

    public $attributeName = 'file';

    /**
     * @var string алиас директории, куда будем сохранять файлы
     */
    public $savePathAlias;
    public $scenarios = array('insert', 'update');
    public $fileTypes = 'jpg, jpeg, png, gif';
    public $maxSizeFile = 10485760; //10 MB

    /**
     *
     * @var width
     * @var height
     * @var master (auto, none, width, height, cresize, centeredpreview) - мастер ресайз
     *
     *
     */
    public $versions = array(
        'preview' => array(
            'centeredpreview' => array(
                'width' => 120,
                'height' => 120,
            ),
        ),
        'medium' => array(
            'centeredpreview' => array(
                'width' => 400,
                'height' => 400,
            ),
        )
    );

    public function __construct()
    {
        if (!Yii::app()->hasComponent('image'))
            throw new CException('not found component Image');
    }

    /**
     * Шорткат для Yii::getPathOfAlias($this->savePathAlias).DIRECTORY_SEPARATOR.
     * Возвращает путь к директории, в которой будут сохраняться файлы.
     * @return string путь к директории, в которой сохраняем файлы
     */
    public function getSavePath()
    {
        return Yii::getPathOfAlias($this->savePathAlias) . DIRECTORY_SEPARATOR;
    }

    public function saveThumbs($file)
    {
        if (!is_dir($this->getSavePath() . 'thumbs')) {
            if ((@mkdir($this->getSavePath() . 'thumbs')) == false)
                throw new CException('отсутствует каталог Thumbs');
        }

        $org_image = Yii::app()->image->load($this->getSavePath() . $file);

        foreach ($this->versions as $name => $params) {
            $method = key($params);
            $args = array_values($params);

            call_user_func_array(array($org_image, $method), is_array($args[0]) ? $args[0] : array($args[0]));



            $org_image->save($this->getSavePath() . 'thumbs' . DIRECTORY_SEPARATOR . $name . '_' . $file);
        }

//        $masterImg = null;
//
//        switch ($this->master) {
//            case 'none':
//                $masterImg = Image::NONE;
//                break;
//            case 'auto':
//                $masterImg = Image::AUTO;
//                break;
//            case 'height':
//                $masterImg = Image::HEIGHT;
//                break;
//            case 'width':
//                $masterImg = Image::WIDTH;
//                break;
//            default :
//                $masterImg = null;
//        }
        //algorithm resize
//        [11:15:01] Kotanaev: 1. ресайзим его так, чтобы оно было 600 в ширину
//[11:15:29] Kotanaev: 2. также ресайзим и по вертикали, чтобы новая высота = старая высота*новая ширина/старая ширина
//[11:15:51] Kotanaev: 3. кропим сверху и снизу все, что выше 200 пикс в высоту
//[11:15:52] Kotanaev: 4. сохраняем
//
    }

    public function getBaseUrlForImage()
    {
        $pos = strpos($this->savePathAlias, 'www');
        $alias = substr($this->savePathAlias, $pos + 4);
        $path = str_replace('.', '/', $alias);
        return $path;
    }

    public function getImage($version = null)
    {

        $name_file = $this->getOwner()->getAttribute($this->attributeName);
        //default
        if (empty($name_file)) {
            if (!is_null($version)) {
                $data = array_values($this->versions[$version]);
                return 'http://placehold.it/' . $data[0]['width'] . 'x' . $data[0]['height'];
            }
            return '';
        }


        if (strpos(Yii::app()->request->getHostInfo(), 'admin')) {
            $base_url = str_replace('lab7', 'admin7', Yii::app()->request->getHostInfo());
        } else {
            $base_url = Yii::app()->request->getHostInfo();
        }
//        $base_url = str_replace('admin.', '', Yii::app()->request->getHostInfo());
//        $base_url = str_replace('lab7', 'admin7', Yii::app()->request->getHostInfo());

        $file = $base_url . '/' . $this->getBaseUrlForImage() . DIRECTORY_SEPARATOR;


        if (!is_null($version)) {
            $file.='thumbs/' . $version . '_' . $name_file;
        } else {
            $file.=$name_file;
        }



        return $file;
    }

    public function attach($owner)
    {
        parent::attach($owner);
        //add validation
        if (in_array($owner->getScenario(), $this->scenarios)) {
            $fileValidator = CValidator::createValidator(
                            'file', $owner, $this->attributeName, array(
                        'types' => $this->fileTypes,
                        'allowEmpty' => true,
                        'maxSize' => $this->maxSizeFile,
                        'safe' => false));
            $owner->validatorList->add($fileValidator);
        }
    }

    public function beforeSave($event)
    {
        if (!is_dir($this->getSavePath())) {

            if (@mkdir($this->getSavePath(), 0777, true) == false) {
                throw new CException('not found catalog for save');
            }
        }

        if ($file = CUploadedFile::getInstance($this->getOwner(), $this->attributeName)) {
            if (!$this->getOwner()->isNewRecord) {
                $this->deleteFile();
            }


            $file_name = md5(microtime()) . '.' . $file->getExtensionName();
            $this->getOwner()->setAttribute($this->attributeName, $file_name);


            if ($file->saveAs($this->getSavePath() . $file_name)) {
                $this->saveThumbs($file_name);
            } else {
                throw new CException('При сохранении файла произошла ошибка');
            }
        }
        return true;
    }

    public function beforeDelete($event)
    {
        $this->deleteFile();
        return true;
    }

    public function deleteFile()
    {
        $file = $this->getSavePath() . $this->getOwner()->getAttribute($this->attributeName);
        @unlink($file);

        foreach (array_keys($this->versions) as $key) {
            @unlink($this->getSavePath() . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . $key . '_' . $this->getOwner()->getAttribute($this->attributeName));
        }

//        $filePath_thumbs = $this->getSavePath() . 'thumbs' . DIRECTORY_SEPARATOR  . '_' . $this->getOwner()->getAttribute($this->attributeName);
//
//
//        if (file_exists($filePath) && file_exists($filePath_thumbs) && trim($this->getOwner()->getAttribute($this->attributeName))!=='') {
//
//            if (!@unlink($filePath)) {
//                throw new CException('При удалении файла произошла ошибка');
//            }
//            if (!@unlink($filePath_thumbs)) {
//                throw new CException('При удалении файла произошла ошибка');
//            }
//
//        }
    }

}

