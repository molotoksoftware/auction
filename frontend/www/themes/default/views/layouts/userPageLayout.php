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
$user_name = $this->user->nick?$this->user->nick:$this->user->login;
?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="container">

    <div class="row auction">
            <div class="col-xs-9">
                <h2><?=Yii::app()->controller->action->id=='about_me'?'О пользователе '.$user_name:''; ?>
                <?=Yii::app()->controller->id=='reviews'?'Отзывы о  '.$user_name:''; ?></h2>
            </div>
            <div class="col-xs-3 text-right">
                <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
                <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
                <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter" data-size="s"></div>
            </div>
    </div>
    <hr class="top10 horizontal_line">

    <div class ="row">
        <div class="col-xs-3 sidebar_left">
            <?php $this->widget('frontend.widgets.categories.CategoriesWidget', [
                'htmlOptions'              => [
                    'class' => 'main_nav profile_cat_tree list-group',
                ],
                'prefix'                   => 'auction',
                'widgetCacheKey'           => 'auction_user_' . $this->user->user_id . '_hour_' . date('h'),
                'countRelationName'        => 'count',
                'categories'               => $this->categories,
                'activeCategory'           => $this->userSelectedCategory,
                'prependAllCategoriesItem' => [
                    'label'               => 'Все категории',
                    'url'                 => Yii::app()->createUrl(
                        '/user/user/page',
                        ['login' => $this->user->login, 'path' => 'all']
                    ),
                    'count'               => null,
                    'num'                 => null,
                    'spec'                => 0,
                    'level'               => null,
                    'alias'               => '',
                    'isAllCategoriesItem' => true,
                    'active'              => $this->userSelectedCategory === 0,
                    'linkOptions'         => ['class' => 'all-cat-item'],
                ],
                'linkBaseUrl'              => Yii::app()->createUrl('/user/user/page', [
                    'login' => $this->user->login,
                ]),
                'itemCssClass' => 'subcat list-group-item',
                'cacheMenuItems' => false
            ]); ?>


        </div>
        <div class="col-xs-9">
            <?=$content; ?>
        </div>
    </div>

</div>
<?php $this->endContent(); ?>