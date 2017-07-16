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
Yii::app()->clientScript->registerScript('index', ' 
$("#myCarousel").carousel({
		interval:   10000
	});
$(".goods_section").hover(
    function(){
        $(this).css("box-shadow", "0 0 10px 5px rgba(221, 221, 221, 1)");
    },
    function() {
    $(this).css("box-shadow", "none");
    }

);
', CClientScript::POS_END);

$webUser = Getter::webUser();

$condition = new CDbCacheDependency('SELECT MAX(`update`) FROM auction');
$auctionRandom = Yii::app()->db
        ->cache(1000, $condition)
        ->createCommand()
        ->select('au.*, bid.price as current_bid')
        ->from('auction au')
        ->leftJoin('bids bid', 'bid.bid_id = au.current_bid')
        ->where('au.status=1')
        ->order(array('RAND()'))
        ->limit('15')
        ->queryAll();
?>



<div class="row main_slider">
    <div class="col-xs-9">

        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->


            <!-- Wrapper for slides -->
            <div class="carousel-inner">

                <div class="item active">
                    <img src="/bootstrap/img/main_sl1.jpg">
                    <div class="carousel-caption">
                    </div>
                </div><!-- End Item -->

                <div class="item">
                    <img src="/bootstrap/img/main_sl2.jpg">
                    <div class="carousel-caption">
                    </div>
                </div><!-- End Item -->
                <div class="item">
                    <img src="/bootstrap/img/main_sl3.jpg">
                    <div class="carousel-caption">
                    </div>
                </div><!-- End Item -->
                <div class="item">
                    <img src="/bootstrap/img/main_sl4.jpg">
                    <div class="carousel-caption">
                    </div>
                </div><!-- End Item -->

            </div><!-- End Carousel Inner -->

            <!-- Controls -->
            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        </div><!-- End Carousel -->

        <!--    <a data-target="#myCarousel" data-slide-to="0" class="active">First slide</a>
            <a data-target="#myCarousel" data-slide-to="1">Second slide</a>
            <a data-target="#myCarousel" data-slide-to="2">Third slide</a>
        -->

    </div>
    <div class="col-xs-3 right_block">
        <a href="http://molotoksoftware.com"><img class="simple_img" src="/bootstrap/img/right_slide.png"></a></div>
</div>
<hr class="horizontal_line">
<div class="row main_h1">
    <div class="col-xs-12">
        <h1><?= Yii::t('basic', 'Recomended') ?>
            <small><a href="/auction?filter=oll&sort=numBids.desc"><?= Yii::t('basic', 'bidding now');?></a></small>
            <small><a href="/auction?period=1d"><?= Yii::t('basic', 'new lots');?></a></small>
            <small><a href="/auction?filter=oll&sort=viewed.desc"><?= Yii::t('basic', 'most popular');?></a></small></h1>
    </div>
</div>

<div class="row goods">  
    <div class="col-xs-12">
        <?php foreach ($auctionRandom as $item): ?>
            <div class="col15-xs-3 goods_section">
                <div class="goods_item">
                    <div class="img_section">
                        <?php
                        $img = Yii::app()->getBasePath() . '/www/i2/' . $item['owner'] . '/thumbs/large_' . $item['image'];
                        if (is_file($img)) {
                            $img_part = '/i2/' . $item['owner'] . '/thumbs/large_' . $item['image'];
                        } else {
                            $img_part = '/img/nofoto.jpg';
                        }
                        ?>
                        <a href="/auction/<?= $item['auction_id'] ?>">
                            <img style="width:100%;" src="<?= $img_part ?>">
                        </a>
                    </div>
                    <div class="title_section">
                        <div class="name_lot">
                            <a href="/auction/<?= $item['auction_id'] ?>"><?= $item['name'] ?></a></div>
                        <div class="cost_lot">
                            <div style="float:right">
                                <span class="date"><?= Item::getTimeLeftSimple($item); ?></span>
                            </div>
                            <div>
                                <?php $price = Item::getStaticPriceValue($item, false); ?>
                                <span class="span_cost">
                                    <?= PriceHelper::formate($price); ?>

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
<hr class="horizontal_line">
<div class="row main_h1">
    <div class="col-xs-12">
        <h1><?= Yii::t('basic', 'Last news');?>
            <small><a href="<?php echo Yii::app()->createUrl('/news/index'); ?>"><?= Yii::t('basic', 'see all');?></a></small>
        </h1>
    </div>
</div>


<div class="row main_news">
    <?php $this->widget('frontend.widgets.news.NewsWidget'); ?>
</div>



