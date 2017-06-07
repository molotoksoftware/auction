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

<?php if (empty($items)): ?>

    <?= CHtml::tag('div', ['class' => 'alert alert-info margint_top_30'], Yii::t('basic', 'No reviews')) ?>

<?php else: ?>

<table style="margin:10px 0;color:#242424">
    <tr>
        <td><strong><?= Yii::t('basic', 'All reviews') ?>: <?= $count; ?></strong>
        </td>
        <td align="right">
            <? $this->widget('CLinkPager', array(
                'pages' => $pages,
                'header' => '',
                'prevPageLabel' => '&laquo; ' . Yii::t('basic', 'Newer'),
                'nextPageLabel' => Yii::t('basic', 'Older') . ' &raquo;',
                'maxButtonCount' => 10,
            )) ?>
        </td>
    </tr>
</table>

<table class="table table-hover t_reviews" width="100%">
    <thead>
    <tr>
        <th width="5%">
        </th>
        <th width="50%"><strong><?= Yii::t('basic', 'Reviews') ?></strong>
        </th>
        <th width="25%"><strong><?= Yii::t('basic', 'From') ?></strong>
        </th>
        <th width="20%"><strong><?= Yii::t('basic', 'Date') ?></strong>
        </th>
    </tr>
    </thead>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <img style="width:20px;" src="/img/rev<?= ($item->value == 5) ? 'up' : 'down' ?>.png">

            </td>
            <td>
                <?= $item->text; ?>
                <div>
                    <a class="auction-link"
                       href="<?= Yii::app()->createUrl('/auction/view', array('id' => $item->entity->auction_id)); ?>"><?= $item->entity->name; ?></a>
                </div>
            </td>
            <td>

                <?= $item->role == 2 ? Yii::t('basic', 'Buyer') . ':' : Yii::t('basic', 'Seller') . ':'; ?>

                <?php $this->widget(
                    'frontend.widgets.user.UserInfo',
                    ['userModel' => $item->userFrom, 'scope' => UserInfo::SCOPE_USER_SIMPLE]
                );
                ?>

                <br>
                <?php if (!empty($item->sale->price)): ?>
                    <span class="span_cost">
                        <?= $item->sale->price; ?>
                    </span>
                <?php endif; ?>
            </td>
            <td>
                <?= Yii::app()->dateFormatter->format("HH:mm, d MMMM y", strtotime($item->date)); ?>
            </td>
        </tr>

    <?php endforeach;
    endif;
    ?>
</table>

<table style="margin:10px 0;color:#242424">
    <tr>
        <td>
        </td>
        <td align="right">
            <? $this->widget('CLinkPager', array(
                'pages' => $pages,
                'header' => '',
                'prevPageLabel' => '&laquo; ' . Yii::t('basic', 'Newer'),
                'nextPageLabel' => Yii::t('basic', 'Older') . ' &raquo;',
                'maxButtonCount' => 10,
            )) ?>
        </td>
    </tr>
</table>
