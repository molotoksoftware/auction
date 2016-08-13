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
 * Class ImageAR
 *
 * @property string $item_id
 * @property string $image
 * @property string $type
 * @property string $sort
 *
 * @method ImageAR sort()
 */
class ImageAR extends CActiveRecord
{
    const TYPE_AUCTION = 0;

    public function tableName()
    {
        return 'images';
    }

    public function defaultScope()
    {
        return [
            //'order' => 'order_weight DESC'
        ];
    }

    public function scopes()
    {
        return [
            'sort' => [
                'order' => 'sort ASC',
            ],
        ];
    }

    public function rules()
    {
        return [
            ['item_id, image, type', 'required'],
            ['item_id, type, sort', 'numerical', 'integerOnly' => true],
        ];
    }

    protected function beforeDelete()
    {

        return parent::beforeDelete();
    }


    public function deleteAndUnlink($user_id)
    {
        $thumb_keys = array_keys(Auction::$versions);
        $res = @unlink(ImageAR::getImageSavePath($user_id, false, $this->image));

        foreach ($thumb_keys as $thumb_key) {
            $u_res = @unlink(ImageAR::getImageSavePath($user_id, true, $thumb_key . '_' . $this->image));
            $res = $res && $u_res;
        }

        return $this->delete();
    }

    public static function getVersionsByType($type)
    {
        if ($type == self::TYPE_AUCTION) $versions = Auction::$versions;

        return $versions;
    }

    public function getVersions()
    {
        return ImageAR::getVersionsByType($this->type);
    }

    public static function generateName($filename, $lot_id = 0, $type_id = 0)
    {
        $name = md5(time() . $lot_id . $type_id . $filename);
        if (strrpos($filename, '.') !== FALSE) {
            $name .= substr($filename, strrpos($filename, '.'));
        }
        return $name;
    }

    public function saveThumbs($user_id)
    {
        $versions = $this->getVersions();

        try {
            $org_image = Yii::app()->image->load(ImageAR::getImageSavePath($user_id, false, $this->image));

            foreach ($versions as $v_name => $v_params) {
                $method = key($v_params);
                $args = array_values($v_params);

                call_user_func_array(
                    [$org_image, $v_name == 'big' ? 'maxWidth' : $method],
                    is_array($args[0]) ? $args[0] : [$args[0]]
                );

                if ($v_name == 'large' || $v_name == 'big') {
                    $org_image->watermark(Yii::getPathOfAlias('frontend.www.img') . '/watermark.png', 10, 10);
                }

                try {
                    $path = ImageAR::getImageSavePath($user_id, true, $v_name . '_' . $this->image);
                    echo "Saving thumb: " . $path . "\n";
                    $org_image->save($path);
                } catch (Exception $e) {
                    print_r($e);
                }
            }
        } catch (Exception $e) {
            echo "ImageAR->saveThumbs(): Error loading image\n";
        }
    }

    /* Копирует картинки из одного аукциона/тендера в другой (записи в БД, файлы, превьюшки) */
    public static function copyImages($from_item, $to_item, $type)
    {
        $images = ImageAR::model()->findAllByAttributes(['item_id' => $from_item->auction_id, 'type' => $type]);
        $versions = self::getVersionsByType($type);

        foreach ($images as $image) {

            $increment = ImageAR::getIncrementId();
            $imageName = md5(time() . $image->image) . '_' . $increment . substr($image->image, strrpos($image->image, '.'));
            $r = Yii::app()->db->createCommand()
                ->insert(
                    'images',
                    [
                        'image_id' => $increment,
                        'item_id'  => $to_item->auction_id,
                        'image'    => $imageName,
                        'type'     => $image->type,
                        'sort'     => $image->sort,
                    ]
                );

            if ($r) {
                @copy(ImageAR::getImageSavePath($from_item->owner, false, $image->image),
                    ImageAR::getImageSavePath($to_item->owner, false, $imageName));

                foreach ($versions as $v_name => $v_params) {
                    @copy(ImageAR::getImageSavePath($from_item->owner, true, $v_name . '_' . $image->image),
                        ImageAR::getImageSavePath($to_item->owner, true, $v_name . '_' . $imageName));
                }
            }
        }
    }

    public static function attachImageByUrl($url, $item, $type)
    {
        $name = self::generateName($url, $item->auction_id, $type);
        $img_name = static::setImageNameWithId($name);
        if (!empty($url))
            if (copy($url, ImageAR::getImageSavePath($item->owner, false, $img_name))) {
                $image = new ImageAR;
                $image->item_id = $item->auction_id;
                $image->image = $img_name;
                $image->type = $type;
                $image->sort = 0;

                if ($image->save()) {
                    echo "Image: " . $image->image_id . ' has been saved'; 

                    $image->saveThumbs($item->owner);
                    return $image;
                } else {
                    print_r($image->getErrors());
                }
            }

        return false;
    }

    public function relations()
    {
        return [];
    }

    public function attributeLabels()
    {
        return [];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function safeDir($dir)
    {
        if (!is_dir($dir)) {
            if ((@mkdir($dir)) == false) {
                throw new CException('отсутствует каталог Thumbs');
            }
        }

        return $dir;
    }

    public static function getImageSavePath($user_id, $thumb = false, $filename = '')
    {

        $res = self::safeDir(Yii::getPathOfAlias('frontend') . '/www/i2/');
        $res = self::safeDir($res . $user_id . '/');
        if ($thumb) $res = self::safeDir($res . 'thumbs/');
        return realpath($res) . '/' . $filename;

    }

    public static function getImageURI($user_id, $thumb = false, $filename = '')
    {

        $res = Yii::app()->baseUrl . '/i2/';
        $res = $res . $user_id . '/';
        if ($thumb) $res = $res . 'thumbs/';
        return $res . $filename;

    }

    public static function getLastId()
    {
        return intval(Yii::app()->db->createCommand()->select('max(image_id) as max')->from('images')->queryScalar());
    }

    public static function getIncrementId()
    {
        return static::getLastId() + 1;
    }

    public static function getImageIdByName($image)
    {
        $id = null;
        $splitName = explode("_", $image);
        if (isset($splitName[1])) {
            $id = $splitName[1];
        } elseif (is_numeric($image)) {
            $id = (int)$image;
        }
        return $id;
    }

    public static function setImageNameWithId($name)
    {
        $splitName = explode(".", $name);
        $count = count($splitName);
        if ($count > 1) {
            return $splitName[$count - 2] . "_" . ImageAR::getIncrementId() . "." . $splitName[$count - 1];
        } else
            return $name . "_" . ImageAR::getIncrementId();
    }
}
