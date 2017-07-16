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
<table bgcolor="#eaeaea" width="100%" cellpadding="20" cellspacing="0" style="border-collapse:collapse;font-family:Arial;">
<tbody><tr>
<td>
<table align="center" width="640" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-family:Arial;">
<tbody><tr bgcolor="white" style="min-height:84px;">
<td style="padding-top:30px;padding-left:30px;padding-bottom:30px;border-bottom:4px solid #c3c3c3;">
<a href="<?=Yii::app()->params["siteUrl"]?>/" style="border:none;" target="_blank" rel="noopener">
    <img src="<?=Yii::app()->params["siteUrl"]?>/img/logo.png" alt="" border="none" width="192"></a>
</td>
<td align="right" style="padding-left:15px;padding-right:30px;padding-top:30px;padding-bottom:30px;border-bottom:4px solid #c3c3c3;">
<table border="0" cellpadding="5" style="font-family:Arial;">
<tbody><tr>
<td nowrap="" style="padding-left:0;">

</td>
<td nowrap="">
</td>
<td nowrap="">
<a style="white-space:nowrap;display:block;font-family:sans-serif;font-size:12px;color:#1b6178;" target="_blank" href="<?=Yii::app()->params["siteUrl"]?>/user/settings/common" rel="noopener">ЛИЧНЫЙ КАБИНЕТ</a>
</td>
</tr>
</tbody></table>
</td>
</tr>
<tr bgcolor="white">
<td colspan="2" style="padding-left:30px;padding-right:30px;font-family:Arial;">
<h1 style="color:#292b2c;margin-top:25px;font-size:18px;font-weight:bold;font-family:sans-serif;font-style:italic;">Здравствуйте, <?php echo $userName; ?></h1>
</td>
</tr>
<tr bgcolor="white">
    
<td colspan="2" style="padding:30px;padding-bottom:10px;padding-top:0px;font-family:Arial;">
    <?=$message;?>
</td>
</tr>
<tr bgcolor="white">
<td colspan="2" style="padding-left:30px;padding-right:30px;padding-bottom:15px;padding-top:15px;font-weight:bold;font-size:13px;">
С уважением,&nbsp;<a href="<?=Yii::app()->params["siteUrl"]?>" style="white-space:nowrap;font-family:sans-serif;font-size:12px;color:#1b6178;" target="_blank" rel="noopener"><?=Yii::app()->params["siteUrl"]?></a>
</td>
</tr>
<tr bgcolor="white">
<td align="center" colspan="2" style="padding-left:30px;padding-right:30px;height:42px;border-top:4px solid #c3c3c3;">
    <a href="<?=Yii::app()->params["siteUrl"]?>" style="white-space:nowrap;font-family:sans-serif;font-size:12px;color:#1b6178;" target="_blank" rel="noopener"><?=Yii::app()->params["siteUrl"]?></a><span style="white-space:nowrap;font-family:sans-serif;font-size:12px;color:#1b6178;"> - торговая площадка</a>
</td>
</tr>
<tr>
<td align="center" colspan="2" style="padding:0; color:gray;font-size:80%">
    <p>Отписаться от уведомлений можно <a href="<?=Yii::app()->params["siteUrl"]?>/user/settings/notifications">здесь</a>.</p>
</td>
</tr>
</tbody></table>
</td>
</tr>
</tbody></table>