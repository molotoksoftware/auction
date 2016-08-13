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


/** @var array $images */

$imagesData = array();
if (!empty($images)) {
    $basePath = Yii::app()->createAbsoluteUrl('/i') . '/';

    foreach ($images as $eachImage) {
        $thumbSrc = ImageAR::getImageURI($this->user_id, true, 'medium_'.$eachImage);
        $bigImageSrc = ImageAR::getImageURI($this->user_id, true, 'big_'.$eachImage);
        $imageSrc = ImageAR::getImageURI($this->user_id, true, 'large_'.$eachImage);
        $imagesData[$imageSrc] = array(
            'medium' => $thumbSrc,
            'large' => $imageSrc,
            'big' => $bigImageSrc,
        );
    }
}
?>

<div class="fotorama" data-width="100%" 
     data-ratio="640/480" 
     data-maxwidth="640" 
     data-thumbwidth="125" 
     data-thumbheight="93" 
     data-thumbmargin="5" 
     data-fit="scaledown" 
     data-nav="thumbs" 
     data-allowfullscreen="true">
    <?php if (empty($images)): ?>
        <?php //echo CHtml::image('http://placehold.it/288x288', ''); ?> 
        <img src="/img/nofoto.jpg">   
    <?php else: ?>
        <?php
        $main_img = $images[0];
        $base_path = Yii::app()->request->getHostInfo() . '/i2/'.$user.'/';
        echo CHtml::link(CHtml::image($base_path . 'thumbs/large_' . $main_img, ''), $base_path .'thumbs/big_'. $main_img);

        $links = array_slice($images, 1);
        foreach ($links as $link) {
            echo CHtml::link(CHtml::image($base_path . 'thumbs/large_' . $link, ''), $base_path .'thumbs/big_'. $link);
        }
        ?>

    <?php endif; ?>
</div>



