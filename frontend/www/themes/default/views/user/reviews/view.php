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

/** @var int $role */
$countReviews = UserDataHelper::getCountReviews($this->user->user_id);

?>
<?php $issetLot = UserDataHelper::issetLot($this->user->user_id); ?>

<? $this->widget('frontend.widgets.user.UserPageLabel', [
    'user'       => $this->user,
]); ?>

<? $this->widget('frontend.widgets.user.UserPageTabs', array(
        'user' => $this->user,
)); ?>

<div style="background-color: white;padding-top:20px;">
    <div class="reviews_header">
        <div class="btn-group btn-group-sm">
            <a type="button" class="btn btn-default <?=(!$role AND !$value)?'active':''?>" href="/user/reviews/view/login/<?=$this->user->login?>"><?= Yii::t('basic', 'All')?></a>
            <a type="button" class="btn btn-default <?=$role == Reviews::ROLE_SELLER?'active':''?>" href="/user/reviews/view/login/<?=$this->user->login?>/role/1"><?= Yii::t('basic', 'From sellers')?> <span class="label label-default"><?=$countReviews['roleBuyer']?></span></a>
            <a type="button" class="btn btn-default <?=$role == Reviews::ROLE_BUYER?'active':''?>" href="/user/reviews/view/login/<?=$this->user->login?>/role/2"><?= Yii::t('basic', 'From buyers')?> <span class="label label-default"><?=$countReviews['roleSaller']?></span></a>
            <a type="button" class="btn btn-default <?=$value == Reviews::VALUE_POSITIVE?'active':''?>" href="/user/reviews/view/login/<?=$this->user->login?>/value/positive"><?= Yii::t('basic', 'Only positive')?></a>
            <a type="button" class="btn btn-default <?=$value == Reviews::VALUE_NEGATIVE?'active':''?>" href="/user/reviews/view/login/<?=$this->user->login?>/value/negative"><?= Yii::t('basic', 'Only negative')?></a>
        </div>
    </div>
    <div class="private_review_content">
        <?php
        $this->renderPartial('_elements', array(
            'items' => $items,
            'count' => $count,
            'pages' => $pages,
        ));
        ?>
    </div>
</div>

