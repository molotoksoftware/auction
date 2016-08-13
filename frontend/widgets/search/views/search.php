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
        $url = '/auction/index';

    ?>

    <form class="form-inline search_form_top" action="<?= Yii::app()->createUrl($url); ?>">
    <div class="input-group search_form">
        <div class="input-group-btn search-panel">
            <?php
            $value = CHtml::encode((isset($_GET['search'])) ? $_GET['search'] : 'Введите фразу для поиска');
            ?>

            <input class="form-control input_text" name="search" autocomplete="off" type="text" value="<?= $value; ?>"
                   onclick="if($(this).val()=='Введите фразу для поиска') $(this).val('');"
                   onblur="if($(this).val()=='') $(this).val('Введите фразу для поиска');"/>

                    <?php
                    $main = Category::model()->findByPk(Category::DEFAULT_CATEGORY);
                    $categories = $main->children()->findAll();

                    $data_cat_search = CHtml::listData($categories, 'category_id', 'name');
                    $data_cat_search = CMap::mergeArray(array(''=>'Все категории'),$data_cat_search);
                    $data_cat_search['-'] = '------------------------------';
                    $data_cat_search['users'] = 'Пользователи';
                    $data_cat_search['auction'] = 'По номеру лота';

                    $select_cat = (isset($_GET['cat'])) ? $_GET['cat'] : '';
                    echo CHtml::dropDownList(
                        'cat',
                        $select_cat,
                        $data_cat_search,
                        array(
                            'class' => 'form-control select_cat',
                            'tabindex' => 1,
                            'autocomplete' => 'off'
                        )
                    );
                    ?>

            <div class="input-group-btn">
                <input type="submit" class="btn btn-default" value="Найти"/>
            </div>
        </div>
    </div>
    </form>

    
