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


?>

<div class="list-group main_cat">


    <?php 
    $category_name = '';
    if ($path != 'all') {

        $categories = explode('/', $path);
        $category_name = array_pop($categories);
        $current_cat = Category::model()->find('alias=:alias', [':alias' => $category_name]);

        if (empty($current_cat))
            throw new CHttpException(404, Yii::t('basic', 'Category not found'));

        $categories = $current_cat->children()->findAll();
        $ancestors = $current_cat->ancestors()->findAll($current_cat->category_id);

        if (count($categories) == 0 && $current_cat->level != 2) {
            $parent_current_cat = $current_cat->parent;
            $current_cat = Category::model()->findByPk($parent_current_cat->category_id);
            $categories = $current_cat->children()->findAll();


        }
        $parent = $current_cat->parent;
        $cat_alias = ($parent->alias == 'root')?'auction':'auctions/'.$parent->alias;
        $cat_name = ($parent->name == 'root')?Yii::t('basic', 'All categories'):$parent->name;


        echo '<a class="maincat list-group-item" href="/'.$cat_alias.'"><b>'.$cat_name.'</b></a>';
        echo '<a class="list-group-item" href="/auctions/'.$current_cat->alias.'"><b>'.$current_cat->name.'</b></a>';
    } else {

        $current_cat = Category::model()->findByPk(Category::DEFAULT_CATEGORY);
        $categories = $current_cat->children()->findAll();
    }

    if (isset($parent_current_cat->alias)) {
        $category_alias = $parent_current_cat->alias.'/';
    }

    /* */



    ?>

    <?php foreach ($categories as $item): ?>

        <a class="subcat list-group-item<?=$category_name==$item->alias?' bold':''?>" href="/auctions/<?=$item->getPath(); ?>">

    <?=$item->name?><span class="main_badge"> (<?=$item->auction_count?>)</span></a>



    <?php endforeach; ?>
</div>