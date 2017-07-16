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

/** @var bool $sold_out */
/** @var User $user */

$js_1 = "var result = confirm('".Yii::t('basic', 'Do you really want to place bid?')."'); if (result) {return true;} else {return false;}";

$js_2 = "$(function(){
                var res = confirm('".Yii::t('basic', 'Do you really want to buy this item?')."');
                if (res) {return true;} else {event.preventDefault(); event.stopPropagation();}
            });";


$tr = TrackOwners::model()->count('owner=:owner AND id_user=:id_user', array(':owner' => $base['owner'], ':id_user' => Yii::app()->user->id));
if ($tr == 0) {
    $tr_text = Yii::t('basic', 'Follow this seller');
} else {
    $tr_text = Yii::t('basic', 'Following this seller');
}

$bidClosingDate = false;
if (!empty($sales)) {
    $bidClosingDate = $sales['date'];
} elseif (isset($base['bidding_date']) && !empty($base['bidding_date'])) {
    $bidClosingDate = $base['bidding_date'];
}


?>

<div class="row">
    <div class="col-xs-12">
        <?= Yii::t('basic', 'Seller') ?>:
        <?php $this->widget(
            'frontend.widgets.user.UserInfo',
            ['userModel' => $author, 'scope' => UserInfo::SCOPE_USER_SIMPLE]
        );
        ?>
        <div class="sub_seller_action">
            <span class="glyphicon glyphicon-shopping-cart"></span>
            <a href="<?= Yii::app()->createUrl('/user/page/' . $author['login']); ?>"><?= Yii::t('basic', 'See other items') ?></a>
            <span class="glyphicon glyphicon-plus"></span>
            <a id="add_track"
               data-id-item="<?php echo $base['owner']; ?>" <?= ($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE) ? 'style="pointer-events: none"' : '' ?>><?php echo $tr_text; ?></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="bet_container lot_action">

            <dl class="dl-horizontal dl_dt_block">

                <?php if (empty($sold_out)): ?>

                    <dt><?= Yii::t('basic', 'Time left') ?>:</dt>
                    <dd>
                        <div class="pull-right" style="margin-right:30px;color:gray;"><?= Yii::t('basic', 'Item') ?>
                            # <?php echo $base['auction_id']; ?></div>
                        <?php
                        $close_str_time = strtotime($base['bidding_date']);
                        if ($close_str_time - time() < 3600 * 24) {
                            $style = 'style="font-weight:bold;color:#323232 !important" id="sample_countdown"';
                        } else {
                            $style = 'style="color: black !important;"';
                        }
                        ?>

                        <span <?php echo $style; ?>><?php echo Item::getTimeLeftSimple($base); ?></span>

                        <script type="text/javascript">
                            function simple_timer(sec, block, direction, endText) {
                                var time = sec;
                                var endText = endText != undefined ? endText : '';
                                direction = direction || false;

                                var hour = parseInt(time / 3600);
                                if (hour < 1) hour = 0;
                                time = parseInt(time - hour * 3600);
                                if (hour < 10) hour = '0' + hour;

                                var minutes = parseInt(time / 60);
                                if (minutes < 1) minutes = 0;
                                time = parseInt(time - minutes * 60);
                                if (minutes < 10) minutes = '0' + minutes;

                                var seconds = time;
                                if (seconds < 10) seconds = '0' + seconds;
                                if (parseInt(hour) == 0 && parseInt(minutes) == 0 && parseInt(seconds) == 0 && endText) {
                                    block.innerHTML = endText;
                                } else {
                                    block.innerHTML = hour + ':' + minutes + ':' + seconds;
                                }

                                if (direction) {
                                    sec++;

                                    setTimeout(function () {
                                        simple_timer(sec, block, direction);
                                    }, 1000);
                                } else {
                                    sec--;

                                    if (sec >= 0) {
                                        setTimeout(function () {
                                            simple_timer(sec, block, direction, endText);
                                        }, 1000);
                                    }
                                }
                            }

                            <?php if ($close_str_time - time() < 60 * 30): ?>
                            function start_countdown() {
                                var block = document.getElementById('sample_countdown');
                                simple_timer(<?php echo $close_str_time - time(); ?>, block, false, '<?= Yii::t('basic', 'Bidding has ended')?>');
                            }
                            start_countdown();
                            <?php endif; ?>

                        </script>
                        <div class="info_auc_gr"><?= date('d.m.Y, H:i', strtotime($bidClosingDate)); ?></div>
                    </dd>
                    <?php if ($base['type_transaction'] == Auction::TP_TR_STANDART || $base['type_transaction'] == Auction::TP_TR_START_ONE): ?>
                        <?php
                        //get starting price
                        $starting_price = $base['starting_price'];
                        if (!is_null($base['current_bid'])) {
                            $starting_price = $base['current_bid'];
                        }
                        $minStepValueFloat = Yii::app()->params['minStepRatePercentage'] * $starting_price / 100;
                        $minStepValue = round($minStepValueFloat, 2);

                        ?>
                        <dt><?= Yii::t('basic', 'Current price') ?>:</dt>
                        <dd>
                <span class="price-field">
                <?= PriceHelper::formate($starting_price) ?>
                </span>
                        <dt></dt>
                        <dd>
                            <div class="row">
                                <div class="col-xs-12 min_stap">
                                    <?= Yii::t('basic', 'Minimal step') ?>:&nbsp;<span
                                            id="min_stap"><?php echo PriceHelper::formate($minStepValue); ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <form action="" id="bid-form" class="form-group">
                                    <input type="hidden" name="start" value="<?= $starting_price; ?>"/>
                                    <input type="hidden" name="lotId" value="<?= $base['auction_id']; ?>"/>
                                    <?php
                                    $nextStep = round(($base['current_bid'] ? Yii::app()->params['minStepRatePercentage'] * $starting_price / 100 : 0) + $starting_price, 2);
                                    ?>
                                    <div class="col-xs-5">
                                        <input type="text" name="price" id="value_stap"
                                               value="<?= str_replace(' ', '', $nextStep); ?>"
                                               class="bet_text form-control" <?= ($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE) ? 'disabled' : '' ?>/>
                                    </div>
                                    <div class="col-xs-7">
                                        <input type="submit" name="submit"
                                               onclick="<? echo Yii::app()->user->isGuest ? '' : $js_1; ?>"
                                               class="bet_sub btn btn-primary but_lot"
                                               value="<?= Yii::t('basic', 'Place bid') ?>" <?= ($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE) ? 'disabled' : '' ?>/>
                                    </div>
                                </form>
                            </div>
                        </dd>
                    <?php endif; ?>
                    <?php if ($base['price'] > 0): ?>
                        <dt><?= Yii::t('basic', 'Buy Now') ?></dt>
                        <dd>


                            <div class="row buy_now_container">
                                <form id="bid-blitz-form" action="" class="form-group">
                                    <input type="hidden" name="lotId" value="<?= $base['auction_id']; ?>"/>
                                    <input type="hidden" id="pr_num" name="priceValue" value="<?= $base['price']; ?>"/>
                                    <div class="col-xs-5">
                        <span>
                                <b id="price-block">
                                    <?= PriceHelper::formate($base['price']); ?>
                                </b>
                        </span>
                                    </div>
                                    <div class="col-xs-7">
                                        <input type="submit" name="name" class="btn btn-primary but_lot"
                                               onclick="<? echo Yii::app()->user->isGuest ? '' : $js_2; ?>"
                                               value="<?= Yii::t('basic', 'Buy Now') ?>" <?= ($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE) ? 'disabled' : '' ?>/>
                                    </div>
                                </form>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 min_stap">
                                    <?php if ($base['quantity'] > 1): ?>
                                        <?php
                                        $inStock = 0;
                                        if ($base['quantity'] > 100) {
                                            if ($base['quantity'] > 500) {
                                                $inStock = '500 +';
                                            } else {
                                                $inStock = floor(($base['quantity'] / 100)) * 100;
                                            }
                                        } else {
                                            $inStock = $base['quantity'];
                                        }
                                        ?>
                                        <?= Yii::t('basic', 'Available') ?>: <?= $inStock; ?><?php if ($base['quantity_sold'] >= 1): ?>[<?= Yii::t('basic', 'Sold') ?>: <?= $base['quantity_sold']; ?>]<?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </dd>
                    <?php endif; ?>
                    <dt></dt>
                    <dd>

                        <a id="add_to_fav" data-id-type="1"
                           data-id-item="<?= $base['auction_id']; ?>" <?= ($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE) ? 'style="pointer-events: none"' : '' ?>/><span
                                class="glyphicon glyphicon-star"></span> <?php if (is_null($base['favorite_id'])): ?>
                        <?= Yii::t('basic', 'Add to favorites') ?><?php else: ?><?= Yii::t('basic', 'Remove from favorites') ?><?php endif; ?></a>

                    </dd>
                <?php else: ?>
                    <dt><?= Yii::t('basic', 'Status') ?>:</dt>
                    <dd>
                        <div class="pull-right" style="margin-right:30px;color:gray;">
                            <?= Yii::t('basic', 'Item #')?><?php echo $base['auction_id']; ?></div>
                        <?php
                        $sales = Yii::app()->db->createCommand()
                            ->select('*')
                            ->from('sales')
                            ->where('sale_id=:id', [':id' => $base['sales_id']])
                            ->queryRow();

                        $date_sales = strtotime($sales['date']);
                        ?>

                        <?php
                        $auctionSalePriceText = '';
                        if (in_array($base['status'], [Auction::ST_SOLD_BLITZ, Auction::ST_COMPLETED_SALE]) && !empty($base['sales_id'])) {
                            $auctionSalePrice = Auction::getSoldPrice($base['sales_id']);
                            if ($auctionSalePrice) {
                                $auctionSalePriceText = ' '.Yii::t('basic', 'for').' ' . Item::getPriceFormat($auctionSalePrice);
                            }
                        }
                        ?>
                        <strong>
                            <?php if ($base['status'] == Auction::ST_COMPLETED_SALE || $base['status'] == Auction::ST_SOLD_BLITZ): ?>
                                <?= Yii::t('basic', 'Sold for {sum}',
                                    [
                                            '{sum}' => $auctionSalePriceText
                                    ]) ?>
                            <?php else: ?>
                                <?= Yii::t('basic', 'Bidding has ended') ?>
                            <?php endif; ?>
                        </strong>
                        <div class="info_auc_gr">
                            <?= date('d.m.Y, H:i', $date_sales); ?><br>
                        </div>
                    </dd>
                <?php endif; ?>

            </dl>
        </div>
        <?php
        if (Yii::app()->user->id) {
            $ab = AutoBid::model()->findByAttributes(['auction_id' => $base['auction_id'], 'user_id' => Yii::app()->user->id]);
            $leader_bid = BidAR::model()->findByPk($base['current_bid_id']);

            if ($ab && $leader_bid) {
                ?>
                <div class="lot_action_bottom text-center"
                     style="background-color:<?= ($leader_bid->owner == Yii::app()->user->id) ? '#BFFFC1' : '#FFBFBF'; ?>">

                    <?php if ($leader_bid->owner == Yii::app()->user->id): ?>
                        <?= Yii::t('basic', 'You are leader') ?>
                        (<?= Yii::t('basic', 'max. bid') ?>: <span id="min_stap"><?= $ab->price; ?></span>)

                    <?php else: ?>
                        <div style="color: red;">
                            <?= Yii::t('basic', 'Your bid {bid} has been outbid', ['{n1}' => '<span id="min_stap">' . $ab->price . '</span>']) ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php }
        } ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="delivery_block">
            <table>
                <?php if (isset($base['id_city']) || isset($base['id_region'])): ?>
                    <tr>
                        <td class="left_td">
                            <?= Yii::t('basic', 'Item location') ?>:
                        </td>
                        <td class="right_td">
                            <div class="delivery_block_text">
                              <span>
                              <?php
                              $strArr = [];
                              $isMoscow = !empty($base['id_city']) && City::isMoscowCityId($base['id_city']);
                              if (isset($base['id_city'])) {
                                  $strArr['city'] = '<strong>' . City::getNameById($base['id_city']) . '</strong>';
                              }
                              if (isset($base['id_region']) && !$isMoscow) {
                                  $strArr['region'] = (isset($strArr['city']) ? '<br />' : '') . Region::getNameById($base['id_region']);
                              }
                              if (!empty($base['id_country'])) {
                                  $strArr['country'] = (isset($base['id_city']) ? '<br />' : '') . Country::getNameById($base['id_country']);
                                  if (!$isMoscow) {
                                      $strArr['country'] = " (" . Country::getNameById($base['id_country']) . ")";
                                  }
                              }
                              $strArr = array_filter($strArr);
                              ?>
                              <?php echo implode($strArr); ?>
                              </span>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>


