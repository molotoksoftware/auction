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
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Molotok</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/auction?filter=nulls"><?= Yii::t('basic', 'From') . ' ' . PriceHelper::formate(1) ?></a>
                </li>
                <li><a href="/news"><?= Yii::t('basic', 'News') ?></a></li>
                <li><a href="#contact"><?= Yii::t('basic', 'Community') ?></a></li>
                <!--<li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li class="dropdown-header">Nav header</li>
                    <li><a href="#">Separated link</a></li>
                    <li><a href="#">One more separated link</a></li>
                  </ul>
                </li> -->
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (!Yii::app()->user->isGuest): ?>
                    <?php
                    $user = Getter::userModel();
                    $this->widget('frontend.widgets.user.UserInfo', ['userModel' => $user, 'scope' => UserInfo::SCOPE_TOP_USER_PANEL]);
                    ?>
                <?php else: ?>
                    <li><a href="/login"><?= Yii::t('basic', 'Login') ?></a></li>
                    <li><a href="/registration"><?= Yii::t('basic', 'Signup') ?></a></li>
                <?php endif; ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>

<div class="container">
    <div class="row search">
        <div class="col-xs-3">
            <a href="/"><img src="/bootstrap/img/logo.png"></a>
        </div>
        <div class="col-xs-9 search_box_form">
            <?php $this->widget('frontend.widgets.search.SearchWidget', [
                'searchActionInWidget' => $this->searchAction,
                'userNickInWidget' => $this->userNick,
            ]); ?>
        </div>
    </div>
    <div class="top_menu">
        <div class="row">
            <div class="header_cat but"><a
                        href="/auctions/transport-197"><?= Yii::t('basic', 'Automotive {span}& Industrial{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auctions/telefony-svyaz-i-navigaciya-1285"><?= Yii::t('basic', 'Digital {span}& Prime Music{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auctions/kompyutery-orgtehnika-i-kanctovary-1175"><?= Yii::t('basic', 'Electronics {span}& Computers{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auctions/kollekcionirovanie-970"><?= Yii::t('basic', 'Collectibles {span}& Art{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auctions/antikvariat-971"><?= Yii::t('basic', 'Sports {span}& Outdoors{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auctions/detskiy-mir-648"><?= Yii::t('basic', 'Toys, Kids {span}& Baby{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auctions/krasota-i-zdorove-572"><?= Yii::t('basic', 'Beauty, Health {span}& Grocery{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>
            <div class="header_cat but"><a
                        href="/auction/"><?= Yii::t('basic', 'All {span}categories{span2}', ['{span}' => '<br /><span class="small_t">', '{span2}' => '</span>']) ?></a>
            </div>

        </div>
    </div>
</div>
