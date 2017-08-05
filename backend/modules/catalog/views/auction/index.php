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

/*
*Лоты и объявления Listings and Announcements
*Лоты и объявления 
*
*
*/


$this->pageTitle = 'Listings and Announcements';
$this->header_info = array(
    'icon' => 'icon-legal icon-2x',
    'title' => 'Listings and Announcements',
);

$this->breadcrumbs = array(
    array(
        'icon' => 'icon-folder-open',
        'label' => 'catalog',
        'url' => array('/catalog/category/index'),
    ),
    array(
        'icon' => 'icon-legal',
        'label' => 'Listings and Announcements',
        'url' => '',
    ),
);
?>

<div class="container-fluid padded">
    <div class="box">
        <div class="box-header">
            <span class="title">Listings</span>
        </div>
        <div class="box-content">
            <?php
            $this->renderPartial('_table_auctions', array(
                'model' => $model
            ));
            ?>
        </div>
    </div>
</div>