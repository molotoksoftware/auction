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

    <?php
    $params = $_GET;
    if (isset($params['Filter'])) {
        unset($params['Filter']);
    }

    $path = Yii::app()->request->getParam('path', null);
    $categories = explode('/', $path);
    $category_name = array_pop($categories);
    $category = Category::model()->find('alias=:alias', [':alias' => $category_name]);

    $cat_new = $category?$category:'';

    ?>

    <form class="form-inline search_form_top" action="<?= Yii::app()->createUrl($searchActionInWidget); ?>">
    <div class="input-group search_form">
        <div class="input-group-btn search-panel">
            <?php
            $value = CHtml::encode((isset($_GET['search'])) ? $_GET['search'] : "");
            ?>

            <input class="form-control input_text" name="search" autocomplete="off" type="text" value="<?= $value; ?>"
                   placeholder="<?=Yii::t('basic', 'Enter search keywords here')?>"/>

                    <?php
                    $main = Category::model()->findByPk(Category::DEFAULT_CATEGORY);
                    $categories = $main->children()->findAll();

                    $data_cat_search = CHtml::listData($categories, 'category_id', 'name');
                    $data_cat_search = CMap::mergeArray(array(
                        isset($cat_new['category_id'])?$cat_new['category_id']:'' => isset($cat_new['name'])?$cat_new['name']:Yii::t('basic', 'All categories'),
                        '' => Yii::t('basic', 'All categories')), $data_cat_search);
                    !$userNickInWidget?$data_cat_search['users'] = Yii::t('basic', 'By Username'):'';
                    !$userNickInWidget?$data_cat_search['auction'] = Yii::t('basic', 'By Lot number'):'';

                    if (isset($_GET['cat'])) {
                        $select_cat = (isset($_GET['cat']));
                    } else if (isset($cat_new['category_id'])) {
                        $select_cat = $category->category_id;
                    } else {
                        $select_cat = '';
                    }

                    $select_cat = (isset($_GET['cat'])) ? $_GET['cat'] : '';
                    echo CHtml::dropDownList(
                        'cat',
                        (int)$select_cat,
                        $data_cat_search,
                        array(
                            'class' => 'form-control select_cat',
                            'tabindex' => 1,
                            'autocomplete' => 'off'
                        )
                    );
                    ?>

            <div class="input-group-btn">
                <input type="submit" class="btn btn-default" value="<?=Yii::t('basic', 'Search')?>"/>
            </div>
        </div>
    </div>
    </form>
<?php if ($userNickInWidget): ?>
<small><?= Yii::t('basic', 'Search by {user} items', ['{user}' => $userNickInWidget]) ?> <span style="color: #b3b3b3;" id="change-search-action"  class="glyphicon glyphicon-remove"></span></small>
<?php endif;