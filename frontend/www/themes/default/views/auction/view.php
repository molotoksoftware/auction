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
/** @var bool $sold_out */
?>
<?php Yii::app()->clientScript->registerScriptFile(bu() . '/js/auction_view_owner.js'); ?>
<?php

$this->pageTitle = $base['name'];
$cs = Yii::app()->clientScript;
$cs->registerScriptFile(bu() . '/js/auction.js', CClientScript::POS_END);

$options = [
    'csrfToken'      => Yii::app()->request->csrfToken,
    'csrfTokenName'  => Yii::app()->request->csrfTokenName,
    'bidUrl'         => Yii::app()->createUrl('/auction/newBid'),
    'bidBlitzUrl'    => Yii::app()->createUrl('/auction/bidBlitz'),
    'bidSpecUrl'     => Yii::app()->createUrl('/auction/bidSpec'),
    'bidExchangeUrl' => Yii::app()->createUrl('/auction/bidExchange')
];

$sold_out = ($base['status'] == Auction::ST_SOLD_BLITZ || $base['status'] == Auction::ST_COMPLETED_SALE) ? true : false;
$own = Yii::app()->user->id == $base['owner'];

$buyer = null;
$isOwnerUser = !Getter::webUser()->getIsGuest() && $base['owner'] == Getter::webUser()->getId();

$auctionBidCount = Yii::app()->db->createCommand()
    ->from('bids b')
    ->select('COUNT(*)')
    ->where('b.lot_id=:lot_id')
    ->queryScalar([':lot_id' => $base['auction_id']]);

$opt = CJavaScript::encode($options);
$cs->registerScript(
    'auction-view',
    '
    window.isLotOwnerUser = ' . ($isOwnerUser ? 'true' : 'false') . ';
    window.bidsCount = ' . intval($auctionBidCount) . ';



    var auction = new Auction(' . $opt . ');
    auction.init();

    //event bet
    $("#bid-form").submit(function(){
        auction.bid($(this));
        return false;
    });

    //event bidBlitz
    $("#bid-blitz-form").submit(function(){
        auction.bidBlitz($(this));
        return false;
    });





      $("#myTab a").click(function(e){
        e.preventDefault();
        $(this).tab("show");
      });


',
    CClientScript::POS_END
);

// Добавить лот в список избранных
$cs->registerScript(
    'favorite',
    '
    $("#add_to_fav").click(function()
    {
       var idItem = $(this).data("idItem");
       var type = $(this).data("idType");
       var fav = $(this);

       $.ajax({
            url      : "' . Yii::app()->createUrl('/user/user/addFavorite') . '",
            data     : {"id":idItem, "type":type},
            type     : "GET",
            dataType : "json",
            success : function(data)
            {
                if (data.response.status=="success")
                {
                    if (data.response.data.stat == 0)
                    {
                        alert("Успешно добавлено в избранное");
                        $(fav).text("Удалить из избранного");
                    }
                    else
                    {
                        alert("Успешно удалён из избранного");
                        $(fav).text("Добавить в избранное");
                    }
                }
            }
    });
    return false;
  });
',
    CClientScript::POS_END
);

// Добавить продавца в список отслеживаемых
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
                if (data.response.data.stat == 0)
                {
                    $(fav).text("Отписаться от продавца");
                }
                else
                {
                    $(fav).text("Подписаться на продавца");
                }
            }
        });

        return false;
    });
    


    ',
    CClientScript::POS_READY
);


$author = User::model()->cache(1000)->findByPk($base['owner']);
$user = Getter::userModel();
?>


<div class="row auction">
        <div class="col-xs-9">
            <h2><?=$base['name'];?></h2>
        </div>
</div>
<div class="row auction">
    <div class="col-xs-9">
            <div class="breadcrumbs">
            <?php echo Item::getBreadcrumbs($base, ' - ', CHtml::link('Аукцион', ['/auction/index'])); ?>
            </div>
        </div>
        <div class="col-xs-3 text-right">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter" data-size="s"></div>
        </div>
</div>
    <hr class="top10 horizontal_line">

<div class="row auction_view">
    <div class="col-xs-5">
            <?php
            $this->widget(
                'frontend.widgets.gallery.GalleryWidget',
                [
                    'images'    => $images,
                    'user_id'   => $base['owner']
                ]
            );
            ?>
    </div>

    <div class="col-xs-7">
        <div class="if_no_cat" style="margin-bottom: 50px">
            <?php
            $this->renderPartial('_buy_block', [
                'base'       => $base,
                'user'       => $user,
                'author'     => $author,
                'sold_out'   => $sold_out
            ]);
            ?>
        </div>
        <div style="background-color: <?=($base['status'] == BaseAuction::ST_ACTIVE)?'rgb(186, 217, 255)':'rgb(218, 218, 218)'?>">
        <?php   if (Yii::app()->user->id == $base['owner']): ?>
            <?php
                $this->renderPartial('unit_owner_auction', [
                    'base' => $base, 'images' => $images, 'sold_out' => $sold_out,
                ]);
            ?>
        <?php endif;  ?>
        </div>
    </div>

</div>

<div class="row auction">
    <div class="col-xs-12">

<ul id="myTab" class="nav nav-tabs top_content_lot">
      <li class="active"><a href="#panel1">Описание</a></li>
         <?php if ($base['conditions_transfer']): ?>
             <li><a href="#panel2">Условия передачи</a></li>
         <?php endif; ?>

      <?php if (!empty($auctionBidCount)): ?>
      <li><a href="#panel3">&nbsp;Ставки: <span class="label label-success"><?= $auctionBidCount ?></span></a></li>
      <?php endif; ?>
      <li><a href="#panel4">&nbsp;Задать вопрос</a></li>
      <?php if (!Getter::webUser()->getIsGuest() && $base['owner'] != Getter::webUser()->getId()): ?>
      <div class="pull-right">
      <a href="#" id="getModalBox" class="complaint_link">Сообщить о нарушении</a>
      </div>
      <?php endif; ?>
</ul>
 
<div class="tab-content lot_view_text">
    <div id="panel1" class="content tab-pane fade in active">
        <!-- Характеристики -->
            <?php   if (count($params) > 0): ?>
        <div class="container">
             <div class="row">
                  <div class="col-12-lg">
        <?php
                    foreach ($params as $param) {

                        $name = $param['name'];
                        $value = (is_null($param['value'])) ? $param['av_value'] : $param['value'];

                        if (!empty($value)) {
                            echo "<div class='har_main pull-left'><div class='har_left pull-left'>" . $name . ":</div><div class='har_right pull-left'>" . $value . "</div></div>";
                        }

                        if ($param['type'] == Attribute::TYPE_DEPENDET_SELECT) {


                            $child = Yii::app()->db->createCommand()
                                ->select('av.value, a.name')
                                ->from('auction_attribute_value aav')
                                ->leftJoin('attribute a', 'a.attribute_id=aav.attribute_id')
                                ->leftJoin('attribute_values av', 'av.value_id=aav.value_id')
                                ->where(
                                    'aav.auction_id=:auction_id and aav.attribute_id=:attribute_id',
                                    [
                                        ':auction_id'   => $base['auction_id'],
                                        ':attribute_id' => $param['child_id']
                                    ]
                                )
                                ->queryRow();
                            if (!is_null($child)) {

                                if (!empty($child['value'])) {
                                    echo "<div class='har_main'><div class='har_left pull-left'>" . $child['name'] . ":</div><div class='har_right pull-left'>" . $child['value'] . "</div></div>";
                                }
                            }

                        }
                    } ?>
                 </div>
             </div>
        </div>
        <hr class="top10 horizontal_line">
        <?php endif;  ?>
        <!-- Характеристики -->

        <!-- Описание -->
            <?php
            $text = '<video>' . $base['text'];
            $purifier = new CHtmlPurifier();
            $purifier->setOptions([
                'HTML.SafeIframe'      => true,
                'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
            ]);
            $text = $purifier->purify($text);
            $text = str_replace("[video]", "<video> <source src=\"", $text);
            $text = str_replace("[/video]", "\"></video>", $text);
            echo $text;
            ?>
        <!-- Описание -->
    </div>
    <div id="panel2" class="tab-pane fade content">
       <?php if ($base['conditions_transfer']): ?>
             <?php echo nl2br($base['conditions_transfer']); ?>
       <?php endif; ?>
    </div>
    <div id="panel3" class="tab-pane fade content">
        <!-- Ставки -->
              <div>
                  <?php
                  if ($base['owner'] == Yii::app()->user->id) {
                      $this->renderPartial('_bids_table',['auction_id' => $base['auction_id'],]);
                  } else {
                      $this->renderPartial('_bids_table_all',['auction_id' => $base['auction_id'],]);
                  }
                  ?>
              </div>
         <!-- Ставки -->
    </div>
    <div id="panel4" class="tab-pane fade content">
        <!-- Задать вопрос -->
              <div>
                  <?php if (!Yii::app()->user->isGuest): ?>
                  <div id="form_quest">
                  <p>Введите текст Вашего вопроса</p>
                  <?php $form = $this->beginWidget('CActiveForm',[
                      'id' => 'question-create',
                      ]); ?>
                  
                  <?php echo $form->textArea($questionForm, 'text', ['class' => 'form-control', 'style' => 'width:300px']); ?>
                  <?php echo $form->hiddenField($questionForm, 'auction_id', ['value' => $base['auction_id']]); ?>
                  <?php echo $form->hiddenField($questionForm, 'owner_id', ['value' => $base['owner']]); ?>
                  
                  <?php 
                    echo CHtml::ajaxSubmitButton('Задать вопрос', ['/user/questions/create'], 
                            [
                                'type' => 'POST',
                                'dataType'   => 'json',
                                'success' => 'js: function(data) {
                                    if (data.response.status=="success") {
                                        $("#quest_answer").html("<div class=\"alert alert-success\">Ваш вопрос успешно отправлен. Продавец ответит на Ваш e-mail адрес</div>");
                                        $("#form_quest").empty();
                                    } else {
                                        $("#quest_answer").html("<div class=\"alert alert-danger margint_top_30\">Ошибка отправки вопроса. Попробуйте похже либо обратитесь в администрацию торговой площадки");
                                    }
                                 }'
                            ],
                            ['type' => 'submit',
                             'class' => 'ftlpo btn btn-default margint_top_30',
                            ]

                    );
                ?>
                  
                  <?php $this->endWidget(); ?>
                  </div>
                  <div id="quest_answer">
                      
                  </div>
                  <?php else: ?>
                  <div class="alert alert-info"><a href="/login">Авторизируйтесь</a>, чтобы задать вопрос продавцу</div>
                  <?php endif; ?>
              </div>
         <!-- Задать вопрос -->
    </div>
  </div>
</div>
</div>
    
    
    
<div class="row">
    <div class="col-12-lg text-right small" style="padding-right:20px;">
        Просмотры: <? echo $base['viewed']; ?>
    </div>
</div>
