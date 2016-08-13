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

<div class="panel panel-default">
     <div class="panel-body">
        <?php
            $tr = TrackOwners::model()->count('owner=:owner AND id_user=:id_user', array(':owner' => $user->user_id, ':id_user' => Yii::app()->user->id));
            if ($tr == 0) {$tr_text = 'Подписаться на продавца';} else {$tr_text = 'Отписаться от продавца';}
        ?>
        <div style="float:right;">
            <a class="btn btn-link btn-sm" id="add_track" data-id-item="<?php echo $user->user_id; ?>">
                <span class="glyphicon glyphicon-plus"></span> <?php echo $tr_text; ?></a>
            <a class="btn btn-link btn-sm" href="#">
                <span class="glyphicon glyphicon-envelope"></span> Связаться</a>
        </div>
        <?php
        /** @var User $owner */
        $this->widget(
              'frontend.widgets.user.UserInfo',
              ['userModel' => $user, 'scope' => UserInfo::SCOPE_USER_PROFILE_PAGE]
        ); ?>

        <?php if ($user->ban == 1):  ?>
            <span class="label label-warning">
               Пользователь заблокирован. Рассчетные операции невозможны
            </span><br />
        <?php endif; ?>

        <small>Зарегистрирован: <b><?php echo date('d.m.Y', strtotime($user->createtime)); ?></b></small>

        <?php
        // Последнее посещение
        $visit = strtotime($user->lastvisit);
        $min5 = time() - 300;
        $today = strtotime(date('d-m-Y'));
        $yesterday = $today - 3600*24;
        $time_text = date('d.m.Y H:i', $visit);

        if ($visit >= $min5) {$time_text = '<span style="color: green;">на сайте</span>';}
        if ($visit >= $today && $visit < $min5) {$time_text = 'сегодня в '.date('H:i', $visit);}
        if ($visit >= $yesterday && $visit < $today && $visit < $min5) {$time_text = 'вчера в '.date('H:i', $visit);}
        ?>
        <small>Последнее посещение: <b><?php echo $time_text; ?></b></small>
     </div>
</div>