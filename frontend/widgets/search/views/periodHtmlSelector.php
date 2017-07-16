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
 * @var $periods            array
 * @var $currentPeriod      string
 * @var $currentPeriodTitle string
 */
cs()->registerScript(
    'periodHtmlSelector',
    '
         $(".periodHtmlSelector a").on("click", function (e) {
            e.preventDefault();
            window.location.href = appendUrlParam(window.location.href, "period", $(this).data("period"));
        });

    ',
    CClientScript::POS_END
);
?>


<div class="dropdown periodHtmlSelector">
 <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
     <?= $currentPeriodTitle ?>
     <span class="caret"></span>
</button>
<ul class="dropdown-menu">

    <?php foreach ($periods as $period => $title): ?>

    <li class="<?= $period == $currentPeriod ? 'active' : '' ?>">
                    <?= CHtml::link($title, '#', ['data-period' => $period]) ?>
                </li>
    <?php endforeach; ?>
</ul>
</div>






