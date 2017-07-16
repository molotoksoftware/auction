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



$px_real = $px*$data['day_viewed']; // weight
if ($px_real < 11) {$px_real = 11;}
?>


<div class="progress">
  <div class="progress-bar align_left_p" role="progressbar" aria-valuenow="150" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $px_real; ?>px;">
   <?php echo date('d.m.Y', strtotime($data['date_viewed'])); ?>:  <strong><?php echo $data['day_viewed']; ?></strong>
  </div>
</div>