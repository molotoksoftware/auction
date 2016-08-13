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

/**
 * @var $this Controller
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
          <a class="navbar-brand" href="/">Molotok 1.0</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/auction?filter=nulls">Лоты от 1 рубля</a></li>
            <li><a href="/news">Новости площадки</a></li>
            <li><a href="#contact">Обсуждения</a></li>
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
                <?php if (!Yii::app()->user->isGuest):?>
                <?php $user = Getter::userModel(); 
                $this->widget('frontend.widgets.user.UserInfo',['userModel' => $user, 'scope' => UserInfo::SCOPE_TOP_USER_PANEL]); ?>
                <?php else: ?>
                <li><a href="/login">Войти</a></li>
                <li><a href="/registration">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

<div class="container">
    <div class="row search">
        <div class="col-xs-2">
            <a href="/"><img src="/bootstrap/img/logo.png"></a>
        </div>
        <div class="col-xs-8 search_box_form">
            <?php $this->widget('frontend.widgets.search.SearchWidget',array());?>
        </div>
        <div class="col-xs-2"></div>
    </div>
    <div class="top_menu">
        <div class="row">
            
            <div class="header_cat but"><a href="/auctions/transport-197">Авто<br><span class="small_t">и мото</span></a></div>
            <div class="header_cat but"><a href="/auctions/telefony-svyaz-i-navigaciya-1285">Телефоны<br><span class="small_t">и смартфоны</span></a></div>
            <div class="header_cat but"><a href="/auctions/kompyutery-orgtehnika-i-kanctovary-1175">Ноутбуки<br><span class="small_t">ПК и планшеты</span></a></div>
            <div class="header_cat but"><a href="/auctions/kollekcionirovanie-970">Коллекции</a></div>
            <div class="header_cat but"><a href="/auctions/antikvariat-971">Искусство<br><span class="small_t">и антиквариат</span></a></div>
            <div class="header_cat but"><a href="/auctions/detskiy-mir-648">Детский<br><span class="small_t">мир</span></a></div>
            <div class="header_cat but"><a href="/auctions/krasota-i-zdorove-572">Мода<br><span class="small_t">и красота</span></a></div>
            <div class="header_cat but"><a href="/auction/">Все<br><span class="small_t">категории</span></a></div>

        </div>
    </div>
</div>

<?php if (Getter::webUser()->hasFlash('success')): ?>
    <?php cs()->registerScript(
        'show-success-flash',
        "frontend.popupMessage.showSuccess('" . Getter::webUser()->getFlash('success') . "');",
        CClientScript::POS_LOAD
    ); ?>
<?php endif; ?>

<?php if (Getter::webUser()->hasFlash('info')): ?>
    <?php cs()->registerScript(
        'show-info-flash',
        "frontend.popupMessage.showInfo('" . Getter::webUser()->getFlash('info') . "');",
        CClientScript::POS_LOAD
    ); ?>
<?php endif; ?>

<?php if (Getter::webUser()->hasFlash('error')): ?>
    <?php cs()->registerScript(
        'show-error-flash',
        "frontend.popupMessage.showError('" . Getter::webUser()->getFlash('error') . "');",
        CClientScript::POS_LOAD
    ); ?>
<?php endif; ?>

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', [
    'id'      => 'success-alert',
    'options' => [
        'title'     => '',
        'autoOpen'  => false,
        'modal'     => true,
        'resizable' => false,
        'draggable' => false,
        'open'      => "js:function(event, ui) {
            $('.ui-widget-overlay').bind('click', function(){
                $(\"#success-alert\").dialog('close');
            });
        }",
    ],
]); ?>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', [
    'id'      => 'error-alert',
    'options' => [
        'title'     => '',
        'autoOpen'  => false,
        'modal'     => true,
        'resizable' => false,
        'draggable' => false,
        'open'      => "js:function(event, ui) {
            $('.ui-widget-overlay').bind('click', function(){
                $(\"#error-alert\").dialog('close');
            });
        }",
    ],
]); ?>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

<?php $this->beginWidget('zii.widgets.jui.CJuiDialog', [
    'id'      => 'info-alert',
    'options' => [
        'title'     => '',
        'autoOpen'  => false,
        'modal'     => true,
        'resizable' => false,
        'draggable' => false,
        'open'      => "js:function(event, ui) {
            $('.ui-widget-overlay').bind('click', function(){
                $(\"#info-alert\").dialog('close');
            });
        }",
    ],
]); ?>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>