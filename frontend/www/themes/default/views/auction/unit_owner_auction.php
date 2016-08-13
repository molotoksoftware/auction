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

/** @var Controller $this */
/** @var array $base */
/** @var null|bool $sold_out */

Yii::app()->clientScript->registerScript(
    'fix_min_width',
    '
    $(".remove-bid").on("click", function() {
        var self = this;

        if(confirm("Вы уверены, что хотите удалить ставку?")) {
            $.ajax({
                url: "/auction/removeBid/bid_id/" + $(this).attr("data-id"),
                success: function() {
                    self.closest("tr").remove();
                    alert("Ставка удалена");
                }
            })
        }

        return false;
    });
    ',
    CClientScript::POS_END
);
?>

<div class="row padding15">
    <div class="col-xs-12">
        <b>Это Ваш лот</b><br />
        <small>В избранном: <?=$base['favorites_count']; ?> </small>
        <small><a href="/user/cabinet/viewed/type/0/id/<?=$base['auction_id']; ?>">Просмотры: <? echo $base['viewed']; ?></a></small>
    </div>
</div>
<div class="row padding15">
    <div class="col-xs-12">
        <?php if ($base['status'] != 10): ?>

            <?php 
                // Кнопка "Завершить досрочно". Только если по лоту имеются ставки и лот активен
                if (($base['current_bid'] != 0) && ($base['status'] == BaseAuction::ST_ACTIVE)): ?>
                <?= CHtml::link('Завершить досрочно',['/editor/longTermCompleted', 'id' => $base['auction_id']],
                        ['class' => 'btn btn-success btn-sm',
                         'onclick' => 'return confirm("Вы действительно хотите завершить торги досрочно? Лот будет продан по последней наивысшей ставке.")',
                         'role' => 'button',
                        ]
                ); ?>
            <?php endif; ?>

            <?php 
                // Кнопка "Редактировать". Только если по лоту НЕТ ставок и он не продан
                if (($base['current_bid'] == 0) && ($base['status'] != BaseAuction::ST_SOLD_BLITZ_PRICE) && ($base['status'] != BaseAuction::ST_SOLD_SUCCESS_BID)): ?>
                <?= CHtml::link("Редактировать лот",["/editor/lot", "id" => $base["auction_id"]],
                        ["class" => "btn btn-default btn-sm", 
                         "role" => "button"]); 
                ?>
            <?php endif; ?>

            <?php 
                // Кнопка "Снять с торгов". Только если лот активен, при этом проверим, есть ли по лоту ставки
                if ($base['status'] == BaseAuction::ST_ACTIVE): ?>
                <?php $link = Yii::app()->createUrl('/editor/removeTrading',['type' => 'item','id' => $base['auction_id']]);?>
                <a title='Cнять с торгов'
                   class="<?= ($base['current_bid'] == 0)?'btn btn-default btn-sm':'btn btn-warning btn-sm'?>"
                   onclick='return confirm("<?= ($base['current_bid'] == 0) ? 'Вы действительно хотите снять лот с торгов?' : 'Вы действительно хотите снять лот с торгов? По лоту имеются ставки, Ваш рейтинг будет уменьшен на единицу.'; ?>")'
                   href="<?= $link; ?>">Снять с торгов</a>
            <?php endif; ?>

            <?php 
                // Кнопка "Перевыставить лот". Только если лот имеет статус завершенного
                if ($base['status'] == BaseAuction::ST_COMPLETED_EXPR_DATE): ?>
                <?php $link = Yii::app()->createUrl('/editor/lot',['strepub' => 1,'id' => $base['auction_id']]);?>
                <a class="btn btn-primary btn-sm" title='Перевыставить лот' href="<?= $link; ?>">Перевыставить лот</a>
            <?php endif; ?>

            <?php 
                // Кнопка "Выставить похожий". Активна всегда ?>
                <a target="_blank" class="btn btn-default btn-sm" title='Выставить похожий' href="<?= Yii::app()->baseUrl.'/creator/publishSame/id/'.$base['auction_id'] ?>">Выставить похожий</a>


        <?php endif; ?>
    </div>
</div>





