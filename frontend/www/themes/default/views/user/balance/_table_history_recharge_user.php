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
 * @var $model BalanceHistory
 */
function getDateFormat($date)
{
    $time = strtotime($date);
    $str = '<p>' . date('d.m.Y', $time) . '</p>';
    $str .= '<p>' . date('H:i', $time) . '</p>';
    return $str;
}

function getSuma(BalanceHistory $data)
{
    if ($data->type == BalanceHistory::STATUS_ADD or $data->type == BalanceHistory::STATUS_RETURN) {
        $ico = '+';
        $style = 'green';
    } elseif (in_array($data->type, [BalanceHistory::STATUS_SUB, BalanceHistory::STATUS_COMMISSION_SALE_LOT])) {
        $ico = '-';
        $style = 'red';
    } else {
        return '';
    }

    $price = $data->summa;

    return '<p><span style="font-weight:bold;color:' . $style . '">' . $ico . '' . $price . '</span></p>';
}

function getDescription($data)
{
    $isCommission = strstr($data->description, Yii::t('basic', 'Commission'));
    if ($isCommission) {
        $lot = preg_replace("/[^0-9]/", '', $data->description);
        return CHtml::link($data->description, "/auction/" . $lot);
    } else
        return CHtml::tag('p', array(), $data->description);
}

?>
<table align="right" style="margin:10px 0;color:#242424">
    <tr>
        <td align="right">
            <?php
            $this->widget('CLinkPager', array(
                'pages' => $pages,
                'header' => '',
                'prevPageLabel' => '&laquo; ' . Yii::t('basic', 'Newer'),
                'nextPageLabel' => Yii::t('basic', 'Older') . ' &raquo;',
                'maxButtonCount' => 5,
            ))
            ?>
        </td>
    </tr>
</table>

<table class="table table-hover t_reviews" width="100%">
    <thead>
    <tr>
        <th width="25%"><strong><?= Yii::t('basic', 'Date') ?></strong>
        </th>
        <th width="25%"><strong><?= Yii::t('basic', 'Amount') ?></strong>
        </th>
        <th width="50%"><strong><?= Yii::t('basic', 'Description') ?></strong>
        </th>
    </tr>
    </thead>
    <?php foreach ($balance as $item): ?>

        <tr>
            <td>
                <?= getDateFormat($item['created_on']); ?>
            </td>
            <td>
                <?= getSuma($item); ?>
            </td>
            <td>
                <?= getDescription($item); ?>
            </td>

        </tr>

    <?php endforeach;
    ?>
</table>

<table align="right" style="margin:10px 0;color:#242424">
    <tr>
        <td>
        </td>
        <td align="right">
            <?php
            $this->widget('CLinkPager', array(
                'pages' => $pages,
                'header' => '',
                'prevPageLabel' => '&laquo; ' . Yii::t('basic', 'Newer'),
                'nextPageLabel' => Yii::t('basic', 'Older') . ' &raquo;',
                'maxButtonCount' => 5,
            ))
            ?>
        </td>
    </tr>
</table>
