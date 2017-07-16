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

<h3><?= Yii::t('basic', 'Favorite sellers') ?> (<?= count($prod_all) ?>)</h3>

<?php if (!empty($prod_all)): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php foreach ($prod_all as $item): ?>
                <div class="btn-group">
                    <a target="_blank" href="<?= Yii::app()->createUrl('/' . $item['login']); ?>"
                       class="btn btn-default btn-sm">
                        <?= User::outUName($item['nick'], $item['login']); ?>
                    </a>
                    <a href="/user/lenta/del/owner/<?php echo $item['user_id']; ?>" class="btn btn-danger btn-sm"><span
                                class="glyphicon glyphicon-remove"></span></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php

$dataProvider = new CSqlDataProvider($sql_fav->text, array(
    'totalItemCount' => $all_count,
    'keyField' => 'auction_id',
    'params' => $params,
    'sort' => array(
        'defaultOrder' => 'auction_id DESC',
    ),
    'pagination' => array(
        'pageSize' => $num_page_size
    ),
));
?>

<?php
function getMainInfoRow($data)
{
    return Yii::app()->controller->renderPartial('/favorites/_info_row_item', array('data' => $data), true);
}

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'table-items',
    'emptyText' => Yii::t('basic', 'You didn\'t add any sellers for track'),
    'dataProvider' => $dataProvider,
    'template' => $template,
    'htmlOptions' => array('class' => ''),
    'cssFile' => false,
    'itemsCssClass' => 'table selected_table',
    'columns' => array(
        array(
            'header' => Yii::t('basic', 'Photo'),
            'type' => 'raw',
            'name' => 'auction_id',
            'value' => 'Table::getImageColumn($data)',
            'headerHtmlOptions' => array('class' => ''),
            'htmlOptions' => array('style' => 'width: 115px;')
        ),

        array(
            'header' => Yii::t('basic', 'Information'),
            'type' => 'raw',
            'name' => 'auction_id',
            'value' => 'getMainInfoRow($data)',
            'headerHtmlOptions' => array('class' => ''),
            'htmlOptions' => array('class' => '')
        )
    ),
));
?>

<?php if ($all_count > 0): ?>
    <div class="row bottom_auctions">
        <div class="col-xs-6">
            <?php
            $this->widget(
                'frontend.widgets.sizerList.SizerListCookieWidget',
                array(
                    'dataProvider' => $dataProvider,
                    'sizerCssClass' => 'pagination',
                    //    'sizerHeader' => '',
                    'sizerAttribute' => 'size',
                    'sizerVariants' => array(25, 50, 100)
                )
            );
            ?>
        </div>
        <div class="col-xs-6 text-right">
            <?php
            $this->widget(
                'CLinkPager',
                array(
                    'pages' => $dataProvider->getPagination(),
                    'maxButtonCount' => 5,
                    'firstPageLabel' => Yii::t('basic', 'First page'),
                    'lastPageLabel' => Yii::t('basic', 'Last page'),
                    'selectedPageCssClass' => 'active',
                    'prevPageLabel' => '&lt; ',
                    'nextPageLabel' => ' &gt;',
                    'header' => '',
                    'footer' => '',
                    'cssFile' => false,
                    'htmlOptions' => array(
                        'class' => 'pagination'
                    )
                )
            );
            ?>
        </div>
    </div>


<?php endif; ?>
