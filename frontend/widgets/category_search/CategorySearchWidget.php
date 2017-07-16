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


class CategorySearchWidget extends CWidget
{

    public $auc_id_arr;
    public $userLogin = false;

    private $path;
    private $path_parent;

    private $category_array = [];
    private $empty_cat = false;
    private $descendants = false;
    private $getWithOutCatId;
    private $mass;
    private $cat_alias;
    private $cat_name;
    private $category;

    public function init()
    {
        if (!empty($this->userLogin)) {
            $this->path = '/user/page/'.$this->userLogin.'/';
            $this->path_parent = $this->path;
        } else {
            $this->path = '/auctions/';
            $this->path_parent = '/auction/';
        }
    }

    public function run()
    {
        $this->getWithOutCatId = preg_replace("/\&cat\=[0-9]{1,5}/ui", "", Yii::app()->getRequest()->getQueryString());

        $path = Yii::app()->request->getParam('path', null);
        $categories = explode('/', $path);
        $category_name = array_pop($categories);

        if ($this->category = Category::model()->find('alias=:alias', [':alias' => $category_name])) {

            $parent = $this->category->parent;
            $this->cat_alias = ($parent->alias == 'root') ? $this->path_parent . '?' . $this->getWithOutCatId : $this->path . $parent->alias . '?' . $this->getWithOutCatId;
            $this->cat_name = ($parent->name == 'root') ? Yii::t('basic', 'All categories') : $parent->name;

            $this->descendants = $this->category->descendants()->findAll();

            foreach ($this->descendants as $item) {
                $this->category_array[] = $item->category_id;
            }

            if (empty($this->category_array)) {
                $this->category_array[] = $this->category->category_id;
            } else {
                $this->category->level = $this->category->level + 1;
            }

            $this->empty_cat = true;

        } else {

            $this->category = Category::model()->findByPk(1);
            $this->category->level = $this->category->level + 1;
            $this->cat_alias = $this->path_parent . '?' . $this->getWithOutCatId;
            $this->cat_name = Yii::t('basic', 'All categories');
        }


        $query = Yii::app()->db->createCommand()
            ->select('h.*, COUNT(auc.category_id) AS cnt')
            ->from('category h')
            ->join('category c', 'c.lft >= h.lft AND c.rgt <= h.rgt')
            ->join('auction auc', 'auc.category_id = c.category_id')
            ->where(['in', 'auc.auction_id', $this->auc_id_arr]);

        if ($this->empty_cat) {
            $query->andWhere(['in', 'auc.category_id', $this->category_array]);
        }

        $query->andWhere('h.level = :level', [':level' => $this->category->level])
            ->group('h.category_id, h.name')
            ->order('h.lft, h.name');

        $this->mass = $query->queryAll();

        $this->render('categorySearch', [
            'mass' => $this->mass,
            'category' => $this->category,
            'descendants' => $this->descendants,
            'getWithOutCatId' => $this->getWithOutCatId,
            'cat_alias' => $this->cat_alias,
            'cat_name' => $this->cat_name,
            'path' => $this->path,
        ]);
    }
}