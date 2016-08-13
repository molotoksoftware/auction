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


class ToolsCommand extends CConsoleCommand
{

    public function run($args)
    {
        $this->attribute();
        die();
//        $this->category();

        $lots = Yii::app()->db->createCommand()
            ->select('*')
            ->from('auction')
            ->queryAll();
        echo "count=" . count($lots) . "\n";

        foreach ($lots as $lot) {
            echo $lot['name'] . "\n";


            $date = new DateTime();
            $created = $date->format('Y-m-d H:i:s');
            $interval_spec = Auction::getDateSpecForDuration($lot['duration']);
            $date->add(new DateInterval($interval_spec));
            $bidding_date = $date->format('Y-m-d H:i:s');


            echo $created . "\n";
            echo $bidding_date . "\n";

            Yii::app()->db->createCommand()
                ->update(
                    'auction',
                    array(
                        'bidding_date' => $bidding_date,
                        'created' => $created,
                        'update' => time()
                    ),
                    'auction_id=:auction_id',
                    array(
                        ':auction_id' => $lot['auction_id']
                    )
                );

        }

    }

    public function attribute()
    {
        $attribute = Yii::app()->db->createCommand()
            ->select('name, attribute_id')
            ->from('attribute')
            ->queryAll();

        foreach ($attribute as $attr) {
            Yii::app()->db->createCommand()->update(
                'attribute',
                array(
                    'sys_name' => $attr['name']
                ),
                'attribute_id=:attribute_id',
                array(
                    ':attribute_id' => $attr['attribute_id']
                )
            );
        }
    }

    public function category()
    {

        $categories = array();

        $parent = Category::model()->findByPk(1);
        foreach ($categories as $cat) {
            $category = new Category();
            $category->name = $cat;
            if ($category->appendTo($parent)) {
                echo 'YES';
            } else {
                echo "NO";
            }
        }
    }

}

?>
