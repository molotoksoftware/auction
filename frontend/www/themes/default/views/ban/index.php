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
s

?>

<div style="margin: 0 auto; width: 300px; margin-top: 200px;">
    <p style="font-size: 24px; margin: 0px;"><?= Yii::t('basic', 'Your account has been banned') ?></p>
    <p style="line-height: 1.4; text-align: justify;">
        <?= Yii::t('basic', 'If you dont know reason, please contact administrator') ?>
        <b><?= Yii::app()->params["adminEmail"] ?></b>
    </p>
</div>