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

<!-- HEADER INFO -->
<div class="container-fluid">
    <div class="row-fluid">
        <?php if (isset($this->header_info) && count($this->header_info) > 0): ?>
            <div class="area-top clearfix">
                <div class="pull-left header">
                    <h3 class="title">
                        <?php if (isset($this->header_info['icon']) && !empty($this->header_info['icon'])): ?>
                            <i class="<?= $this->header_info['icon']; ?>"></i>
                        <?php endif; ?>
                        <?php if (isset($this->header_info['title']) && !empty($this->header_info['title'])): ?>
                            <?= $this->header_info['title']; ?>
                        <?php endif; ?>
                    </h3>
                   </div>
                <div class="ajax-loading"></div>
            </div>
        <?php endif; ?>
    </div>
</div>   
<!-- END HEADER INFO -->


<!-- BREDCRUMBS -->
<?php if (count($this->breadcrumbs) > 0): ?>
    <div class="container-fluid padded-mini">
        <div class="row-fluid">
            <div id="breadcrumbs">
                <?php
                $this->widget('ex-bootstrap.widgets.Breadcrumbs', array(
                    'links' => $this->breadcrumbs,
                    'tagName' => 'div',
                    'separator' => '',
                ));
                ?>   
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- BREDCRUMBS -->