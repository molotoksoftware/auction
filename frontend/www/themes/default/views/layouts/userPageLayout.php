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
$user_name = $this->user->nick ? $this->user->nick : $this->user->login;
?>
<?php $this->beginContent('//layouts/main'); ?>
    <div class="container">

        <div class="row auction">
            <div class="col-xs-9">
                <h2>
                    <?= Yii::app()->controller->action->id == 'about_me' ? Yii::t('basic', 'About') . ' ' . $user_name : ''; ?>
                    <?= Yii::app()->controller->id == 'reviews' ? Yii::t('basic', 'Reviews about') . ' ' . $user_name : ''; ?>
                </h2>
            </div>
            <div class="col-xs-3 text-right">
                <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"
                        charset="utf-8"></script>
                <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
                <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter"
                     data-size="s"></div>
            </div>
        </div>
        <hr class="top10 horizontal_line">

        <div class="row">
            <div class="col-xs-3 sidebar_left">
                <?php $this->widget('frontend.widgets.category_search.CategorySearchWidget', [
                    'auc_id_arr' => $this->auc_id_arr,
                    'userLogin' => $this->user->login,
                ]); ?>

            </div>
            <div class="col-xs-9">
                <?= $content; ?>
            </div>
        </div>

    </div>
<?php $this->endContent(); ?>