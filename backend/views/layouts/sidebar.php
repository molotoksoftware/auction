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
<div class="sidebar-background">
    <div class="primary-sidebar-background"></div>
</div>
<div class="primary-sidebar">
    <?php
    $items = require(Yii::getPathOfAlias('application.config') . '/menu.php');
    $this->widget('ex-bootstrap.widgets.CoreMenu', array(
        'items' => $items,
        'htmlOptions' => array(
            'class' => 'nav nav-collapse collapse nav-collapse-primary'
        ),
        'linkLabelWrapper' => 'span',
        'activateItems' => true,
        'activateParents' => true,
        'submenuHtmlOptions' => array('class' => 'collapse'),
        'itemTemplate' => "<span class=\"glow\"></span>{menu}"
    ));
    ?>
</div>