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

    if (Getter::userModel()->getIsPro()) {
            $quantityUploadPhoto = (int)Yii::app()->params['quantityFotoForPro'];
    } else {
            $quantityUploadPhoto = (int)Yii::app()->params['quantityUploaddFoto'];
    }
?>


<div id="image-upload-block" class="row create_lot_right_photo">

    <input type="hidden" name="identifier" value="<?php echo md5(microtime()); ?>"/>
    <input type="hidden" name="model" value="<?php echo get_class($model); ?>"/>

    <div class="col-xs-3 left_col">
        <p><?= Yii::t('basic', 'Photo Gallery')?>:</p>
        <span>
            <strong><?= Yii::t('basic', 'File requirements')?>:</strong><br>
            - <?= Yii::t('basic', 'Image file formats')?>: jpg, gif, bmp, png<br>
            - Max 8 Mb<br><br>
           <?= Yii::t('basic', 'You can upload up to {photos} photos', ['{photos}' => $quantityUploadPhoto])?><br>
        </span>
    </div>
    <div class="col-xs-9 right_col photo_fl_r">
        <div class="add-photo-top"></div>
        <div class="lot-photo-block-wrp image-container" style="cursor: pointer">
            <?php
            if (isset($model) && !$model->isNewRecord) {

                $images = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('images')
                    ->where('item_id=:item_id', array(':item_id' => $model->getPrimaryKey()))
                    ->order('sort')
                    ->queryAll();


                foreach ($images as $img):?>
                    <?php
                    $path = str_replace('admin.', '', Yii::app()->request->getHostInfo());
                    $file = ImageAR::getImageURI($model->owner, true, 'large_' . $img['image']);
                    $imagePath = ImageAR::getImageSavePath($model->owner, true, 'large_' . $img['image']);
                    ?>

                    <div data-storage="locale" data-id="<?= $img['image']; ?>" data-pk="<?= $img['image_id']; ?>" class="image-item lot-photo-block">
                        <div class="lot-photo-inner">
                            <div class="lot-photo-wrp">
                                <img src="<?= $file; ?>" alt="">
                            </div>
                            <a href="#" class="del-lot-photo btn-remove"></a>
                        </div>
                        <div class="lot-photo-title">
                            <p><?=Yii::app()->format->size(@filesize($imagePath)); ?></p>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php } ?>
        </div>
        
        <div class="lot-photo-block add-photo">
            <div class="lot-photo-inner">
                <label>
                    <div class="lot-photo-wrp">
                        <div class="men-ico"></div>
                        <p><?= Yii::t('basic', 'Add <br /> Photo')?></p>
                        <div style="display: none;" class="loader-progresbar-wrp">
                            <div class="loader-progresbar" style="width:2%"></div>
                        </div>
                    </div>
                    <input type="file" name="image" class="file add_photo_sub" accept="image/*" multiple="multiple">
                </label>
            </div>
        </div>
        <div class="sorted-container"></div>
        <div style="clear:both"></div>
        <div>
             <a href="javascript:void(0);" class="del-all-photos"><?= Yii::t('basic', 'Delete all photos')?></a>
        </div>
    </div>
</div>


<script id="item-image" type="text/x-jquery-tmpl">
    <div data-storage="${storage}" data-id="${id}" class="image-item lot-photo-block">
        <div class="lot-photo-inner">
            <div class="lot-photo-wrp">
                <img src="${img.src}" alt="">
            </div>
            <a href="#" class="del-lot-photo btn-remove"></a>
        </div>
        <div class="lot-photo-title">
            <p>${size}</p>
        </div>
    </div>
</script>