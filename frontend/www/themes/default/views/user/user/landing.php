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

<?php

$cs = Yii::app()->clientScript;

$cs->registerScript('landing', ' 
$("#socialLinks").hover(function(){ 
    	$(this).css("opacity",1); 
	}, function(){ 
        $(this).css("opacity",0.5);
        }); 
$(".goods_section").hover(
    function(){
        $(this).css("box-shadow", "0 0 10px 5px rgba(221, 221, 221, 1)");
    },
    function() {
    $(this).css("box-shadow", "none");
    }

);
', CClientScript::POS_READY);

$cs->registerScript(
    'track_owner', '

    $("#add_track").click(function()
    {
        var owner = $(this).data("idItem");
        var fav = $(this);

        $.ajax({
            url      : "' . Yii::app()->createUrl('/auction/track_owner') . '",
            data     : {"owner": owner},
            type     : "GET",
            dataType : "json",
            success : function(data) 
            {
                if (data.response.data.stat == 0) {
                   $(fav).text("'.Yii::t('basic', 'Following this seller').'");
                } else {
                    $(fav).text("'.Yii::t('basic', 'Follow this seller').'");
                }
            }
        });

        return false;
    });
    ',
	CClientScript::POS_READY
);

$reviewRandom = Yii::app()->db
                ->cache(3600)
                ->createCommand()
                ->select('*')
                ->from('reviews')
                ->where('user_to = :user_id', [':user_id' => $model->user_id])
                ->order(array('RAND()'))
                ->limit('1')
                ->queryRow();

$condition = new CDbCacheDependency('SELECT MAX(`update`) FROM auction WHERE owner='.$model->user_id);
$auctionRandom = Yii::app()->db
                ->cache(1000, $condition)
                ->createCommand()
                ->select('au.*, bid.price as current_bid')
                ->from('auction au')
                ->leftJoin('bids bid', 'bid.bid_id = au.current_bid')
                ->where('au.owner = :user_id AND au.status=1', [':user_id' => $model->user_id])
                ->order(array('RAND()'))
                ->limit('15')
                ->queryAll();


$countReviews = UserDataHelper::getPercentRewiews($model->user_id);

$reviews = UserDataHelper::getCountReviews($model->user_id);
$countR = $reviews['positive']+$reviews['negative'];

$isserReviews = $reviews['negative'] + $reviews['positive'];

$userPlace = false;

if ($model->id_city)
    $userPlace = UserDataHelper::getCityCountryUser($model->id_city, $model->id_country);

function getCurrentPrice ($item) {
    if ($item['current_cost']) {
        echo round($item['current_cost'], 0, PHP_ROUND_HALF_UP);
    } else {
        echo round(($item['starting_price']!=0.00)?$item['starting_price']:$item['price'], 0, PHP_ROUND_HALF_UP);
    }
}

$tr = TrackOwners::model()->count('owner=:owner AND id_user=:id_user', array(':owner' => $model->user_id, ':id_user' => Yii::app()->user->id));
if ($tr == 0) {$tr_text = Yii::t('basic', 'Follow this seller');} else {$tr_text = Yii::t('basic', 'Following this seller');}


$webUser = Getter::webUser();

?>
<div class="landing_page">
    <div class="landing_background_page">
        
    </div>
<div class="row label_sub_background">
    <div class="col-xs-10 col-xs-offset-1 label_user">
        <div class="part_top">
            <div class="left_area">
                  <?php $test_img = $this->user->uploadedFile->getImage('avatar'); if ($test_img != 'http://placehold.it/208x208'): ?>
                      <img alt="<?= $this->user->getFullName(); ?>" width="150" height="150" src="<?=$test_img?>">
                  <?php else: ?>
                      <img alt="<?= $this->user->getFullName(); ?>" width="150" height="150" src="/img/landing/noimage.png">
                  <?php endif; ?>
            </div>
            <div class="right_area">
                <div class="row margin_0px">
                    <div class="col-xs-12 top_line">
                        <div style="float:right;">
                            <a class="btn btn-link btn-sm" href="/user/page/<?=$model->login?>">
                                <span class="glyphicon glyphicon-shopping-cart"></span> <?= Yii::t('basic', 'Items for sale')?>
                            </a>

                        </div>
                        <?php $this->widget(
                                'frontend.widgets.user.UserInfo',
                                ['userModel' => $model, 'scope' => UserInfo::SCOPE_USER_PROFILE_PAGE]
                        ); ?>
                        <?php if ($model->ban == 1):  ?>
                            <span class="label label-warning">
                               <?= Yii::t('basic', 'User has been banned') ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="col-xs-12 margint_top_30">
                        <div class="row">
                            <div class="col-xs-4">
                                <?php if ($model->ban != 1):  ?>
                                <a type="button" class="btn btn-info" id="add_track"data-id-item="<?php echo $model->user_id; ?>"><span class="glyphicon glyphicon-plus"></span> <?php echo $tr_text; ?></a>
                                <?php endif; ?>
                            </div>
                            <div class="col-xs-8 span_town">
                                <?= Yii::t('basic', 'Date of Signup') ?>: <?=Yii::app()->dateFormatter->format("d MMMM yyyy", strtotime($model->createtime));?> г.<br />
                                <?= Yii::t('basic', 'Last visit') ?>: <b><?=$model->getTimeLastVisit(); ?></b><br>
                                <i style="color:gray;">
                                <?php
                                 if ($userPlace) {
                                     echo '<img style="height:12px;" src="/img/pin_map.png"> '.$userPlace;
                                 }?>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="part_down">
            <div class="row">
                <div class="col-xs-8"><strong><?= Yii::t('basic', 'Reviews') ?></strong>
                    <div class="row margint_top_30">
                        <div class="col-xs-3 col-xs-offset-2 text-center">
                            <a href="/user/reviews/view/login/<?=$model->login?>/value/positive"><img src="/img/revup.png"></a>
                            <b><?=$reviews['positive']?></b>
                            <div><small><?= Yii::t('basic', 'Positive') ?></small></div>
                        </div>
                        <div class="col-xs-4  text-center">
                            <a href="/user/reviews/view/login/<?=$model->login?>/value/negative"><img src="/img/revdown.png"></a>
                            <b><?=$reviews['negative']?></b>
 
                            <div><small><?= Yii::t('basic', 'Negative') ?></small></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4 text-right border_left_rev"><a class="btn btn-link btn-sm" href="/user/reviews/view/login/<?=$model->login?>">
                    <span class="glyphicon glyphicon-stats"></span> <?= Yii::t('basic', 'See all reviews') ?></a>
                    <?php if ($isserReviews): ?>
                    <div class="row">
                        <div class="col-xs-2">
                            <img style="width:20px;" src="/img/rev<?=$reviewRandom['value']==1?'down':'up'?>.png">
                        </div>
                        <div class="col-xs-10 span_town text-left">
                            <span><?=mb_strimwidth($reviewRandom['text'], 0, 100, "...", 'UTF-8');?></span><br>
                            <span style="color:gray;"><?=Yii::app()->dateFormatter->format("d MMMM yyyy", strtotime($reviewRandom['date']))?></span>
                        </div>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-10 col-xs-offset-1">
        <div id="socialLinks">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,viber,whatsapp" data-image="<?=$test_img?>" data-size="s"></div>
        </div>
    </div>
</div>

<div class="row main_h1">
    <div class="col-xs-12 margint_top_80">
        <h1><?= Yii::t('basic', 'Items for sale') ?>
        <small>(<a href="/user/page/<?=$model->login?>"><?=UserDataHelper::countLot($model->user_id)?></a>)</small></h1>
    </div>
</div>
<hr class="top10 horizontal_line">
    
<div class="row goods">       
    <div class="col-xs-12"> 
        <?php foreach ($auctionRandom as $item): ?>
        <div class="col15-xs-3 goods_section">
            <div class="goods_item">
                <div class="img_section">
                    <?php 
                        $img = Yii::app()->getBasePath().'/www/i2/'.$item['owner'].'/thumbs/large_'.$item['image'];
                        if (is_file($img)) {
                            $img_part = '/i2/'.$item['owner'].'/thumbs/large_'.$item['image'];
                        } else {
                            $img_part = '/img/nofoto.jpg';
                        }
                    ?>
                    <a href="/auction/<?=$item['auction_id']?>">
                        <img style="width:100%;" src="<?=$img_part?>">
                    </a>
                </div>
                <div class="title_section">
                    <div class="name_lot">
                        <a href="/auction/<?=$item['auction_id']?>"><?=$item['name']?></a></div>
                    <div class="cost_lot">
                        <div style="float:right">
                            <span class="date"><?=Item::getTimeLeftSimple($item);?></span>
                        </div>
                        <div>
                            <?php $price = Item::getStaticPriceValue($item, false); ?>
                            <span class="span_cost ">
                            <?=$price;?>
                            </span>
                        </div>
                        <div class="clear"></div>
                    </div>
                    
                </div> 
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if(UserDataHelper::countLot($model->user_id)): ?>
<div class="row">
    <div class="col-xs-12 text-center margint_top_80">
        <a type="button" class="btn btn-default" href="/user/page/<?=$model->login?>"> <?= Yii::t('basic', 'See all items') ?> </a>
    </div>
   
</div>
<?php endif;?>
    

</div>











