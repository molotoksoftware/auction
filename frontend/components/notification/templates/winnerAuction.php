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

История покупок. Поздравляем. Вы стали победителем в торгах по лоту <?= $linkItem; ?>. Пожалуйста свяжитесь с продавцом и выкупите лот.<br/>
<?php if ($quantity>1): ?>
    Стоимость: <?= $lotPrice; ?>. <br/>
    Количество: <?=$quantity; ?><br/>
    Общая сумма: <?=$amount;?><br/>
<?php else: ?>
    Стоимость: <?= $lotPrice; ?>. <br/>
<?php endif; ?>
<br/>
<strong>Контактные данные:</strong><br/>
Продавец: <?= $sellerModel->getLink(); ?><br/>
<?php if (!empty($sellerModel->telephone)): ?>
    Телефон: <?=$sellerModel->telephone; ?><br/>
<?php endif; ?>
E-mail: <?= $sellerModel->email; ?><br/>
Дополнительная контактная информация: <?= $sellerModel->add_contact_info; ?><br/>
<?php echo CHtml::link('Перейти к покупкам', Yii::app()->createAbsoluteUrl('/user/shopping/historyShopping'));
