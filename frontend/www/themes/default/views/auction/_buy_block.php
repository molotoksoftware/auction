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

$js_1 = "var result = confirm('Вы действительно хотите сделать ставку?'); if (result) {return true;} else {return false;}";

$js_2 = "$(function(){
                var qu_num = $('#qu_num').size() ? $('#qu_num').val() : 1;
                var pr_num = $('#pr_num').val();
                var res = confirm('Вы действительно хотите купить ' + qu_num + ' единиц(ы) лота за ' + pr_num * qu_num + ' " . Getter::webUser()->getCurrencySymbol() . "?');
                if (res) {return true;} else {event.preventDefault(); event.stopPropagation();}
            });";


$tr = TrackOwners::model()->count('owner=:owner AND id_user=:id_user', array(':owner' => $base['owner'], ':id_user' => Yii::app()->user->id));
    if ($tr == 0) {
$tr_text = 'Подписаться на продавца';
} else {
    $tr_text = 'Отписаться от продавца';
}

// определяем дату завершения торгов
$bidClosingDate = false;
if (!empty($sales)) {
    $bidClosingDate = $sales['date'];
} elseif (isset($base['bidding_date']) && !empty($base['bidding_date'])) {
    $bidClosingDate = $base['bidding_date'];
}


?>

<div class="row">
    <div class="col-xs-12">
        Продавец: 
        <?php $this->widget(
            'frontend.widgets.user.UserInfo',
            ['userModel' => $author, 'scope' => UserInfo::SCOPE_USER_SIMPLE]
        );
        ?>
        <div class="sub_seller_action">
            <span class="glyphicon glyphicon-shopping-cart"></span> 
            <a href="<?= Yii::app()->createUrl('/user/page/'.$author['login']); ?>">Посмотреть другие товары</a> 
            <span class="glyphicon glyphicon-plus"></span> 
            <a id="add_track" data-id-item="<?php echo $base['owner']; ?>" <?=($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE)?'style="pointer-events: none"':''?>><?php echo $tr_text; ?></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
    <div class="bet_container lot_action">

        <dl class="dl-horizontal dl_dt_block">
            
            <?php  if (empty($sold_out)): ?>

            <dt>Осталось времени:</dt>
            <dd>
                <div class="pull-right" style="margin-right:30px;color:gray;">Лот № <?php echo $base['auction_id']; ?></div>
                <?php
                $close_str_time = strtotime($base['bidding_date']);
                if ($close_str_time - time() < 3600*24)
                {
                    $style = 'style="font-weight:bold;color:#323232 !important" id="sample_countdown"';}
                else
                {
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

                    <?php if ($close_str_time - time() < 60*30): ?>
                        function start_countdown() {
                            var block = document.getElementById('sample_countdown');
                            simple_timer(<?php echo $close_str_time - time(); ?>, block, false, 'Торги окончены');
                        }
                        start_countdown();
                    <?php endif; ?>

                </script>
                <div class="info_auc_gr"><?php echo date('d.m.Y в H:i', strtotime($bidClosingDate)); ?></div>
            </dd>
        <?php if ($base['type_transaction'] == Auction::TP_TR_STANDART || $base['type_transaction'] == Auction::TP_TR_START_ONE): ?>
            <?php
            //get starting price
            $starting_price = $base['starting_price'];
            if (!is_null($base['current_bid'])) {
                $starting_price = $base['current_bid'];
            }
            $minStepValueFloat = FrontBillingHelper::getUserPrice(
                Yii::app()->params['minStepRatePercentage'] * $starting_price / 100, false
            );
            if ($minStepValueFloat > 1) {
                $minStepValue = ceil($minStepValueFloat);
            } else {
                $minStepValue = round($minStepValueFloat, 2);
            }
             ?>
            <dt>Текущая стоимость:</dt>
            <dd>
                <span class="price-field <?= !Getter::webUser()->getCurrencyIsRUR() ? 'not-rur-currency' : '' ?>">
                <?= FrontBillingHelper::getUserPriceWithCurrency($starting_price, ['rurCurrencySign' => false]) ?>
                </span>
            <dt></dt>
            <dd>
                <div class="row">
                    <div class="col-xs-12 min_stap">
                        Минимальный шаг:&nbsp;<span id="min_stap"><?php echo $minStepValue; ?></span>
                    </div>
                </div>
                <div class="row">
                    <form action="" id="bid-form" class="form-group">
                    <input type="hidden" name="start" value="<?=$starting_price;?>"/>
                    <input type="hidden" name="lotId" value="<?=$base['auction_id'];?>"/>
                    <?php
                        $nextStepRUR = ceil(($base['current_bid'] ? Yii::app()->params['minStepRatePercentage'] * $starting_price / 100 : 0) + $starting_price);
                        $nextStep = FrontBillingHelper::getUserPrice($nextStepRUR, false);
                        ?>
                    <div class="col-xs-5">
                        <input type="text" name="price" id="value_stap" value="<?= str_replace(' ', '', $nextStep); ?>" class="bet_text form-control" <?=($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE)?'disabled':''?>/>
                    </div>
                    <div class="col-xs-7">
                        <input type="submit" name="submit" onclick="<? echo $js_1; ?>" class="bet_sub btn btn-primary but_lot" value="Сделать ставку" <?=($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE)?'disabled':''?>/>
                    </div>
                    </form>
                </div>
            </dd>
                <?php endif; ?>
                <?php if ($base['price'] > 0): ?>
                <dt>Купить сейчас:</dt>
                <dd>


                <div class="row buy_now_container">
                    <form id="bid-blitz-form" action="" class="form-group">
                    <input type="hidden" name="lotId" value="<?=$base['auction_id'];?>"/>
                     <input type="hidden" id="pr_num" name="priceValue" value="<?= FrontBillingHelper::calculateUserPrice($base['price'], true) ?>"/>
                    <div class="col-xs-5">
                        <span class="<?= !Getter::webUser()->getCurrencyIsRUR() ? 'not-rur-currency' : '' ?>">
                                <b id="price-block">
                                    <?= FrontBillingHelper::getUserPriceWithCurrency($base['price'], ['rurCurrencySign' => false]) ?>
                                </b>
                        </span>
                    </div>
                    <div class="col-xs-7">
                        <input type="submit" name="name" class="btn btn-primary but_lot" onclick="<? echo $js_2; ?>" value="Купить сейчас" <?=($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE)?'disabled':''?>/>
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
                            Доступно сейчас: <?= $inStock; ?> <?php if ($base['quantity_sold'] >= 1): ?>[Продано: <?= $base['quantity_sold']; ?>]<?php endif; ?>
                        <?php endif;?>
                    </div>
                </div>
                </dd>
                <?php endif; ?>
                <dt></dt>
                <dd>
                    
                    <a id="add_to_fav" data-id-type="1" data-id-item="<?= $base['auction_id']; ?>" <?=($base['owner'] == Yii::app()->user->id OR $base['status'] != BaseAuction::ST_ACTIVE)?'style="pointer-events: none"':''?>/><span class="glyphicon glyphicon-star"></span> <?php if (is_null($base['favorite_id'])): ?>
                        Добавить в избранное<?php else: ?>Удалить из избранного<?php endif; ?></a>

                </dd>
        <?php else: ?>
            <dt>Статус:</dt>
            <dd>
                <div class="pull-right" style="margin-right:30px;color:gray;">Лот № <?php echo $base['auction_id']; ?></div>
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
                        $auctionSalePriceText = ' за ' . Item::getPriceFormat($auctionSalePrice);
                    }
                }
                ?>
                <strong>
                    <?php if ($base['status'] == Auction::ST_SOLD_BLITZ): ?>
                        Продан по блиц-цене<?php echo $auctionSalePriceText; ?>
                    <?php elseif ($base['status'] == Auction::ST_COMPLETED_SALE): ?>
                        Продан<?php echo $auctionSalePriceText; ?>
                    <?php else: ?>
                        Торги завершены
                    <?php endif; ?>
                </strong>
                <div class="info_auc_gr">
                    <?=date('j', $date_sales);?>
                    <?=HelperDate::getMonthName(date('n', $date_sales));?>
                    <?=date('Y, H:i', $date_sales);?>
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
            <div class="lot_action_bottom text-center" style="background-color:<?=($leader_bid->owner == Yii::app()->user->id)?'#BFFFC1':'#FFBFBF';?>">

                <?php if ($leader_bid->owner == Yii::app()->user->id): ?>
                        Вы лидер торгов
                        (макс. ставка: <span id="min_stap"><?=FrontBillingHelper::getUserPrice($ab->price, false); ?></span> <?= Getter::webUser()->getCurrencySymbol() ?>)

                <?php else: ?>
                    <div style="color: red;">
                        Ваша ставка (<span id="min_stap"><?=FrontBillingHelper::getUserPrice($ab->price, false); ?></span> <?= Getter::webUser()->getCurrencySymbol() ?>) перебита
                    </div>
                <?php endif; ?>

            </div>
        <?php } } ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="delivery_block">
            <table>
                <?php if (isset($base['id_city']) || isset($base['id_region'])): ?>
                    <tr>
                        <td class="left_td">
                            Местонахождение:
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






    


         <?php // дата размещения лота, если нужно echo date('d.m.Y в H:i', strtotime($base['created'])); ?>


