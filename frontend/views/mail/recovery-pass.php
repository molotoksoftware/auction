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

<div style="width:800px;margin:12px auto;padding:20px;border:10px solid #ccc">
    <div style="border-bottom:1px solid #aaa;padding-bottom:12px;margin-bottom:24px;overflow:hidden">
        <img style="display: block;" width="254" height="49" src="<?=Yii::app()->params["siteUrl"]?>/img/logo.png">

        <h3>Здравствуйте, <?=CHtml::encode($login);?>!</h3>
        <p>Ваш пароль изменен!</p>
        <p> </p>
        <p>Перейдите на сайт и выполните вход в систему со следующими данными:</p>
        <p>Имя пользователя: <?=$login;?></p>
        <p>Пароль: <?=$pass; ?></p>
        <p> </p>
        <p>Вы можете поменять пароль в Вашем <?=CHtml::link(
                'личном кабинете',
                Yii::app()->createAbsoluteUrl('/user/settings/common')
            ); ?>.</p>
                    <p style="font-size:88%;color:gray"><strong>Внимание!</strong> Администрация ресурса никогда не запрашивает пароль для доступа к Вашему аккаунту.</p>
        <p><?php echo Yii::app()->createAbsoluteUrl('/'); ?> - интернет-акцион</p>
    </div>
</div>
