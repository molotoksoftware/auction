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

        if(confirm("' . Yii::t('basic', 'Do you really want to remove bid?') . '")) {
            $.ajax({
                url: "/auction/removeBid/bid_id/" + $(this).attr("data-id"),
                success: function() {
                    self.closest("tr").remove();
                    alert("' . Yii::t('basic', 'Bid has been removed') . '");
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
        <b><?= Yii::t('basic', 'It\'s your item') ?></b><br/>
        <small><?= Yii::t('basic', 'In favorites') ?>: <?= $base['favorites_count']; ?> </small>
        <small><a href="/user/cabinet/viewed/type/0/id/<?= $base['auction_id']; ?>"><?= Yii::t('basic', 'Views') ?>
                : <? echo $base['viewed']; ?></a></small>
    </div>
</div>
<div class="row padding15">
    <div class="col-xs-12">
        <?php if ($base['status'] != 10): ?>
            <?php
            // Sell button. Show, if there are some bids on the item and item has got active status
            if (($base['current_bid'] != 0) && ($base['status'] == BaseAuction::ST_ACTIVE)): ?>
                <?= CHtml::link(Yii::t('basic', 'Sell this item'), ['/editor/longTermCompleted', 'id' => $base['auction_id']],
                    ['class' => 'btn btn-success btn-sm',
                        'onclick' => 'return confirm("' . Yii::t('basic', 'Do you really want to sell this item? The item will be sold at the last price.') . '")',
                        'role' => 'button',
                    ]
                ); ?>
            <?php endif; ?>

            <?php
            // Edit button. Show, if there aren't any bids on the item
            if (($base['current_bid'] == 0) && ($base['status'] != BaseAuction::ST_SOLD_BLITZ_PRICE) && ($base['status'] != BaseAuction::ST_SOLD_SUCCESS_BID)): ?>
                <?= CHtml::link(Yii::t('basic', 'Edit item'), ["/editor/lot", "id" => $base["auction_id"]],
                    ["class" => "btn btn-default btn-sm",
                        "role" => "button"]);
                ?>
            <?php endif; ?>

            <?php
            // Remove from sell button. Show, if item has got active status and check bids,,,
            // If there are some bids on the item, will need to reduce seller's rating
            if ($base['status'] == BaseAuction::ST_ACTIVE): ?>
                <?php $link = Yii::app()->createUrl('/editor/removeTrading', ['type' => 'item', 'id' => $base['auction_id']]); ?>
                <a class="<?= ($base['current_bid'] == 0) ? 'btn btn-default btn-sm' : 'btn btn-warning btn-sm' ?>"
                   onclick='return confirm("<?= ($base['current_bid'] == 0) ? Yii::t('basic', 'Do you really want to remove item from sell?') : Yii::t('basic', 'Do you really want to remove item from sell? There are bids. Your rating will be reduced'); ?>")'
                   href="<?= $link; ?>">
                    <?= Yii::t('basic', 'Remove from sell') ?>
                </a>
            <?php endif; ?>

            <?php
            // Republish item button. Show, if item has got ended status
            if ($base['status'] == BaseAuction::ST_COMPLETED_EXPR_DATE): ?>
                <?php $link = Yii::app()->createUrl('/editor/lot', ['strepub' => 1, 'id' => $base['auction_id']]); ?>
                <a class="btn btn-primary btn-sm" href="<?= $link; ?>">
                    <?= Yii::t('basic', 'Republish item') ?>
                </a>
            <?php endif; ?>

            <?php
            // Publish a similar
            ?>
            <a target="_blank" class="btn btn-default btn-sm"
               href="<?= Yii::app()->baseUrl . '/creator/publishSame/id/' . $base['auction_id'] ?>">
                <?= Yii::t('basic', 'Publish similar')?>
            </a>


        <?php endif; ?>
    </div>
</div>





