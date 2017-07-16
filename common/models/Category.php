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
 * This is the model class for table "category".
 *
 * @property string $category_id
 * @property string $name
 * @property string $alias
 * @property string $description
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property integer $status
 * @property integer $parent_id
 * @property integer $update
 *
 */
class Category extends CActiveRecord
{

    public $parent_id;
    public $url;
    public $applyToChild;

    const DEFAULT_CATEGORY = 1;
    const ST_ACTIVE = 1;
    const ST_NO_ACTIVE = 0;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'category';
    }

    public function defaultScope()
    {
        return array(
            'alias' => 'cat',
            'order' => 'lft',
        );
    }

    public function scopes()
    {
        return array(
            'allowed' => array(
                'condition' => 'category_id not in (' . self::DEFAULT_CATEGORY . ')'
            )
        );
    }

    public function behaviors()
    {
        return array(
            'nestedSet' => array(
                'class' => 'common.extensions.behaviors.nestedSet.NestedSetBehavior',
            ),
            'translit' => array(
                'class' => 'common.extensions.behaviors.translit.CategoryTranslitBehavior',
                'sourceAttribute' => 'name',
                'aliasAttribute' => 'alias'
            ),
            'seo' => array(
                'class' => 'common.extensions.seo.SeoBehavior',
            ),
        );
    }

    /**
     * @param array $categories
     *
     * @return mixed
     */

    public static function recalcAuctionCount($cat_id) {
        
        $main = Category::model()->findByPk($cat_id);
        
        if ($main->auction_count == 0) {

            $sql = 'UPDATE category c SET auction_count = (SELECT COUNT(*) FROM auction a WHERE status = 1 AND a.category_id = c.category_id) WHERE c.category_id = '.$cat_id;

            $command= Yii::app()->db->createCommand($sql);
            $command->query(); // execute a query SQL
            
            $main2 = Category::model()->findByPk($cat_id);


            if ($cat_id != 1) {
                $parent = $main->parent;
                $sql2 = 'UPDATE category c SET auction_count = auction_count+'.$main2->auction_count.' WHERE c.category_id = '.$parent->category_id;
                $command= Yii::app()->db->createCommand($sql2);
                $command->query(); // execute a query SQL

            }
        } else {
            if ($cat_id != 1) {
                $parent = $main->parent;
                $sql2 = 'UPDATE category c SET auction_count = auction_count+'.$main->auction_count.' WHERE c.category_id = '.$parent->category_id;
                $command= Yii::app()->db->createCommand($sql2);
                $command->query(); // execute a query SQL
            }
        }

    }

    public function relations()
    {
        return array(
            'count' => array( // UPDATE category c SET auction_count = (SELECT COUNT(*) FROM auction a WHERE status = 1 AND a.category_id = c.category_id)
                self::STAT,
                'BaseAuction',
                'category_id',
                'condition' => 'status=:status',
                'params' => array(
                    ':status' => Auction::ST_ACTIVE
                )
            ),
            'favourites_attribute' => array(
                self::HAS_MANY,
                'CategoryAttributes',
                'category_id',
                'select' => 'attribute_id'
            ),
        );
    }

    public function rules()
    {
        return array(
            array('name', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('alias', 'unique'),
            array('applyToChild', 'boolean'),
            array('name, alias, description', 'length', 'max' => 255),
            array('category_id, name, alias, description, status', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'category_id' => 'Category',
            'parent_id' => 'Родительская категория',
            'name' => 'Название',
            'alias' => 'SEO URL',
            'description' => 'Описание',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'status' => 'Активен',
        );
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     *
     */
    public function search()
    {

        $criteria = new CDbCriteria;
        $criteria->order = 'lft';
        $criteria->scopes = array('allowed');

        $criteria->compare('category_id', $this->category_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('alias', $this->alias, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 100
            ),
        ));
    }

    /**
     * @param string $className active record class name.
     * @return Category the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */

    /**
     *
     * @return array
     */
    public static function getListAllCategories()
    {
        $categoryList = array();

        $category = Category::model()->allowed()->findAll();
        foreach ($category as $item) {
            $tab = ($item->level <= 2) ? 0 : 2;
            $level = str_repeat("&nbsp", $tab * ($item->level - 1));
            $label = $level . $item->name;

            $categoryList[$item->category_id] = $label;
        }
        return $categoryList;
    }

    /**
     *
     * @return array
     */
    public static function getListAllCategoriesNorm()
    {
        $categoryList = array();
        /**
         * @todo Добавить кэш
         */
        $category = Category::model()->allowed()->findAll();
        foreach ($category as $item) {
            $tab = ($item->level <= 2) ? 0 : 2;
            $level = str_repeat("&nbsp", $tab * ($item->level - 1));
            $label = $level . $item->name;

            $categoryList[$item->category_id] = $label;
        }
        return $categoryList;
    }

    /**
     * возвращает названия для таблицы
     * @return string
     */
    public function getNameForTable()
    {

        $result = array();
        //$dependency = new CDbCacheDependency('SELECT MAX(`update`) FROM category');
        //->cache(Yii::app()->params['cache_duration'], $dependency, 5)
        $category = Category::model()->findByPk($this->category_id);
        if ($category) {
            $ancestors = $category->ancestors()->findAll('category_id!=:id',array(':id'=>self::DEFAULT_CATEGORY));
        }


        foreach ($ancestors as $ancestor) {
            $result[]= $ancestor->name;
        }

        array_push($result, $category->name);
        return implode(' / ', $result);
    }

    /**
     * возвращает статус для таблицы
     * @return string
     */
    public function getStatusForTable()
    {
        $label = '*';
        if ($this->status == self::ST_ACTIVE) {
            $label = '<span class="label label-green"> Да </div>';
        } elseif ($this->status == self::ST_NO_ACTIVE) {
            $label = '<span class="label label-red"> Нет </div>';
        }
        return $label;
    }

    /**
     * возвращает масив [id] атрибутов для текущей категории
     * @return array
     */
    public function getFavAttrForSelect()
    {
        $data = array();
        foreach ($this->favourites_attribute as $val) {
            $data[] = $val->attribute_id;
        }
        return $data;
    }

    /**
     * возвращает категории для списка
     * @param type $cat_id
     * return array
     */
    public static function getCategoriesForSelect($cat_id = null)
    {
        if (is_null($cat_id)) {
            $cat_id = self::DEFAULT_CATEGORY;
        }
        if (empty($cat_id)) {
            return array();
        }

        $category = Category::model()->findByPk($cat_id);


        $descendants = $category->children()->findAll();


        return CHtml::listData($descendants, 'category_id', 'name');
    }

    /*
     * Получить категорию по ID (будет последняя в массиве) и всех ее предков в одном запросе
     * Category::model()->byIdAndAncestors(75)->findAll()
     */
    public function byIdAndAncestors($id) {
        $db=$this->getDbConnection();
        $criteria=$this->getDbCriteria();
        $alias=$db->quoteColumnName($this->getTableAlias());

        $criteria->mergeWith(array(
            'join' => 'LEFT JOIN category c ON c.category_id='.intval($id),
            'condition'=> '('.$alias.'.'.$db->quoteColumnName('lft').'< c.lft'.
                ' AND '.$alias.'.'.$db->quoteColumnName('rgt').'> c.rgt) OR '.$alias.'.category_id = '.intval($id),
            'order'=>$alias.'.'.$db->quoteColumnName('rgt').'DESC',
        ));

        return $this;
    }

    /**
     * @param array $ids
     *
     * @return $this
     * @throws CDbException
     */
    public function byIdsAndAncestors(array $ids) {
        $db=$this->getDbConnection();
        $criteria=$this->getDbCriteria();

        $ids[] = 0;

        $ids = array_map('intval', $ids);
        $idsStr = implode(',', $ids);

        $alias=$db->quoteColumnName($this->getTableAlias());

        $criteria->mergeWith(array(
            'join' => 'LEFT JOIN category c2 ON c2.category_id IN ('.$idsStr.') LEFT JOIN category c ON c.category_id = c2.category_id',
            'condition'=> '('.$alias.'.'.$db->quoteColumnName('lft').'< c.lft'.
                ' AND '.$alias.'.'.$db->quoteColumnName('rgt').'> c.rgt) OR '.$alias.'.category_id = c2.category_id',
            'order'=>$alias.'.'.$db->quoteColumnName('lft'),
        ));

        return $this;
    }

    /**
     * Возвращает массив родительских категорий для Breadcrumbs.
     *
     * example array('cat1' => array('path/path1'),
     *               'cat2' => array('path/path2'),
     *               'cat2'
     *              );
     *
     * @param       $id
     * @param array $params
     *
     * @return array
     */
    public static function getAncestorCategoryByBreadcrumbs($id, $params = [])
    {
        $cache = Yii::app()->getCache();
        $key = CacheHelper::KEY_PARENT_CATEGORIES_FOR_BREADCRUMB_FOR_CATEGORY_ID . '_' . $id;

        $urlRoute = !empty($params['url']['route']) ? $params['url']['route'] : '/auction/index';
        $urlParams = !empty($params['url']['params']) ? $params['url']['params'] : [];

        $result = $cache->get($key);
        if (false === $result) {
            $result = [];
            /** @var Category[] $ancestors */
            $ancestors = Category::model()->byIdAndAncestors($id)->findAll();
            foreach ($ancestors as $ancestor) {
                if ($ancestor->category_id != Category::DEFAULT_CATEGORY) {
                    $_urlParams = $urlParams;
                    $_urlParams['path'] = $ancestor->getPath('/');
                    $result["$ancestor->name"] = Yii::app()->createUrl($urlRoute, $_urlParams);
                }
            }

            $lastUpdateCategory = $cache->get(CacheHelper::KEY_LAST_TIMESTAMP_UPDATE_CATEGORY_TABLE);
            if (false === $lastUpdateCategory) {
                $lastUpdateCategory = Yii::app()
                    ->getDb()
                    ->createCommand('SELECT MAX(`update`) FROM category')
                    ->queryScalar();
                $cache->set(CacheHelper::KEY_LAST_TIMESTAMP_UPDATE_CATEGORY_TABLE, $lastUpdateCategory);
            }

            /** @var Category $lastUpdateCategoryCache */
            $lastUpdateCategoryCache = Category::model()->cache(60 * 2)->findBySql('SELECT MAX(`update`) FROM category');

            $isNotUpdated = strtotime($lastUpdateCategory) < strtotime($lastUpdateCategoryCache->update);
            if (!$isNotUpdated) {
                $cache->set(CacheHelper::KEY_LAST_TIMESTAMP_UPDATE_CATEGORY_TABLE, $lastUpdateCategoryCache->update);
            }

            $dependency = new CExpressionDependency(
                $isNotUpdated ? 'true' : 'false'
            );

            $cache->set($key, $result, 60 * 60, $dependency);
        }

        return $result;
    }

    public static function getPathName($id)
    {
        $result = array();
        //$dependency = new CDbCacheDependency('SELECT MAX(`update`) FROM category');
        //->cache(Yii::app()->params['cache_duration'], $dependency, 5)
        $category = Category::model()->findByPk($id);
        if ($category) {
            $ancestors = $category->ancestors()->findAll();
        }

        foreach ($ancestors as $ancestor) {
            if ($ancestor->category_id != Category::DEFAULT_CATEGORY) {
                $result[] = $ancestor->name;
            }
        }
        array_push($result, $category->name);

        return $result;
    }

    public function beforeDelete()
    {
        $items = BaseAuction::model()->findAll(
            'category_id=:category_id',
            array(
                ':category_id' => $this->category_id
            )
        );

        foreach ($items as $item) {
            $item->delete();
        }

        return parent::beforeDelete();
    }

    /**
     *
     * @param integer $id
     * @return string
     * @throws Exception
     */
    public static function getCatLinkById($id, $htmlOptions)
    {
        $cat = self::model()->findByPk($id);
        if (is_null($cat)) {
            throw new Exception('not found category');
        }

        return CHtml::link($cat->name, array('/auction/index', 'path' => $cat->getPath()), $htmlOptions);
    }

    public function getAncestorCategoryId()
    {
        $data = array();
        $ancestors = $this->ancestors()->findAll();
        foreach ($ancestors as $ancestor) {
            if ($ancestor->category_id != Category::DEFAULT_CATEGORY) {
                $data[] = $ancestor->category_id;
            }
        }
        array_push($data, $this->category_id);
        return $data;
    }

    protected static function fixCount($items) {
        foreach ($items as $keyPreLvl2 => $valuePreLvl2) {
            foreach ($valuePreLvl2 as $keyLvl2 => $valueLvl2) {
                #$items[$keyPreLvl2][$keyLvl2] = 7;
                if (isset($valueLvl2['items'])) {
                    foreach ($valueLvl2['items'] as $keyLvl3 => $valueLvl3) {
                        if (isset($valueLvl3['items'])) {
                            foreach ($valueLvl3['items'] as $keyLvl4 => $valueLvl4) {
                                $items[$keyPreLvl2][$keyLvl2]['items'][$keyLvl3]['count'] +=$valueLvl4['count'];
                            }
                        }
                    }
                }
            }
        }
        return $items;
    }

    /**
     * возвращает путь для ссылки
     *
     * @param type $separator
     * @return string
     */
    public function getPath($separator = '/')
    {
        $data = array();
        $ancestors = $this->ancestors()->findAll();
        foreach ($ancestors as $ancestor) {
            if ($ancestor->category_id != Category::DEFAULT_CATEGORY) {
                $data[] = $ancestor->alias;
            }
        }
        array_push($data, $this->alias);
        return implode($separator, $data);
    }

    protected static function normalizeMenuDataArray($items, $parent_alias) {
        $cnt = count($items);

        for($i = 0; $i < $cnt; $i++) {
            $path = strlen($parent_alias) > 0 ? $parent_alias . '/' . $items[$i]['alias'] : $items[$i]['alias'];

            if(is_array($items[$i]['url']))
                $items[$i]['url']['path'] = $path;
            else
                $items[$i]['url'] = $path;

            if (isset($items[$i]['items'])) {
                $items[$i]['items'] = self::normalizeMenuDataArray($items[$i]['items'], $path);
            }
        }

        return $items;
    }

    /**
     * @param            $prefix
     * @param            $countRelationName
     * @param Category[] $categories
     * @param int        $activeCategory
     * @param array      $params
     *
     * @return array
     */
    public static function getMenuData($prefix, $countRelationName, array $categories = null, $activeCategory = null, $params = [])
    {
        $main_categories = Category::DEFAULT_CATEGORY;
        $customCategories = $categories !== null;
        if ($categories === null) {
            $main = Category::model()->findByPk($main_categories);
            $categories = $main->descendants()->findAll();
        }
        if (empty($categories)) {
            return array();
        }


        $level = 2;
        $result = array();
        $categoryHasUrl = false;
        foreach ($categories as $category) {
            $categoryName = $category->name;
            /*$maxCategoryNameLength = 30;
            if (mb_strlen($categoryName, 'utf-8') > $maxCategoryNameLength) {
                $categoryName = mb_strcut($categoryName, 0, 50, 'utf-8') . '...';
            }*/
            if ($category->level > $level) {
                $category_path = array();
                $result[$category->level] = & $result[$level][count($result[$level]) - 1]['items'];
            }
            $category_path[] = $categoryName;

            if (!$categoryHasUrl && !empty($category->url)) {
                $categoryHasUrl = true;
            }


            $item = array(
                'label' => $categoryName,
                'url' => empty($category->url) ? array('/' . $prefix . '/index', 'path' => '') : $category->url,
                'count' => ($category->level > 2) ? ($category->auction_count) : null,
                'num' => ($category->level == 3) ? 1 : null,
                'spec' => $category->category_id,
                'level' => $category->level,
                'alias' => $category->alias
            );
            if (is_numeric($activeCategory)) {
                $item['active'] = $category->category_id == $activeCategory;
            }
            $result[$category->level][] = $item;



            if (($category->lft + 1) != $category->rgt) {
                current($result);
            }

            $level = $category->level;
        }

        if (!$customCategories || !$categoryHasUrl) {
            $result[2] = self::normalizeMenuDataArray($result[2], '');
        }
        $result = self::fixCount($result);

        if (isset($result)) {
            return $result[2];
        } else {
            return array();
        }
    }

    /**
     * @param                 $prefix
     * @param                 $countRelationName
     * @param null|Category[] $categories
     * @param null            $activeCategory
     * @param null|string     $widgetCacheKey
     * @param array           $params
     *
     * @return array
     */
    public static function getMenuArray($prefix, $countRelationName, $categories = null, $activeCategory = null, $widgetCacheKey = null, $params = []) {
        if ($widgetCacheKey === null) {
            $widgetCacheKey = 'categoryMenu_' . $prefix;
        }

        $menu = Yii::app()->cache->get($widgetCacheKey);
        #$menu = false;
        if ($menu === false || empty($params['cacheMenuItems'])) {
            $menu = self::getMenuData($prefix, $countRelationName, $categories, $activeCategory, $params);
            $depndency = new CDbCacheDependency('SELECT MAX(`update`) FROM category');
            Yii::app()->cache->set(
                $widgetCacheKey,
                $menu,
                Yii::app()->params['cache_duration'],
                $depndency
            );
        }

        return $menu;
    }

    /**
     * @param int  $userId
     * @param int  $status
     * @param bool $returnIds TODO
     *
     * @return array|Category[]
     */
    public static function getUserCategoriesHavingLotsByStatus($userId, $status, $returnIds = false) {
        // Товары юзера.
        /** @var CDbCommand $userProductsDbComm */
        $userProductsDbComm = Yii::app()->db->createCommand();
        $userAuctions = $userProductsDbComm
            ->select('a.category_id')
            ->from('auction a')
            ->where(
                'a.status=:status and a.owner=:owner',
                array(
                    ':owner' => $userId,
                    ':status' => $status,
                )
            )->queryAll();

        $userAuctionsArrays = array_chunk($userAuctions, 1000);
        $categoryIds = array();
        $categoryLots = array();
        foreach ($userAuctionsArrays as $userAuctions) {
            foreach ($userAuctions as $eachAuction) {
                $categoryIds[$eachAuction['category_id']] = $eachAuction['category_id'];
                if (!isset($categoryLots[$eachAuction['category_id']])) {
                    $categoryLots[$eachAuction['category_id']] = 0;
                }
                $categoryLots[$eachAuction['category_id']]++;
            }
        }

        /** @var Category[] $userCategories */
        $userCategories = Category::model()
            ->byIdsAndAncestors($categoryIds)
            ->findAll('cat.level > 1');

        $userCategories = ArrayHelper::index($userCategories, 'category_id');
        // Добавляем категориям кол-во лотов юзера.
        foreach ($userCategories as $i => $each) {
            $userCategories[$i]->auction_count = 0;
        }
        foreach ($categoryLots as $eachCatId => $eachLots) {
            if (isset($userCategories[$eachCatId])) {
                $userCategories[$eachCatId]->auction_count = $eachLots;
            }
        }

        return $userCategories;
    }

    /**
     * @param Category[] $models
     *
     * @return array
     */
    public static function categoriesToFormattedDropDownArray($models)
    {
        $result = [];
        foreach ($models as $eachModel) {
            $text = str_repeat('&nbsp;&nbsp;', $eachModel->level - 2) . $eachModel->name;
            if (!empty($eachModel->auction_count)) {
                $text .= ' (' . $eachModel->auction_count . ')';
            }
            $result[$eachModel->getPrimaryKey()] = $text;
        }
        return $result;
    }

    /*
     * Возвращает массив id дочерних элементов
     */
    public static function getCategoryIds($category_id)
    {
        $items = Category::getMenuArray('auction', 'count');
        $res = [];

        $item = self::findRecursiveById($items, $category_id);

        if ($item) {
            $res[] = $item['spec'];
            $res = array_merge($res, self::recursiveChildrenId($item));
        }

        return $res;
    }

    public static function findRecursiveById($items, $category_id)
    {
        foreach ($items as $item) {
            if ($item['spec'] == $category_id) {
                return $item;
            }

            if (isset($item['items'])) {
                $i = self::findRecursiveById($item['items'], $category_id);
                if ($i) return $i;
            }
        }

        return null;
    }

    public static function recursiveChildrenId($item)
    {
        if (isset($item['items'])) {
            $items = $item['items'];
            $res = [];

            foreach ($items as $item) {
                $res[] = $item['spec'];
                $res = array_merge($res, self::recursiveChildrenId($item));
            }

            return $res;
        }

        return [];
    }

    public function getAllDependents()
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'category_id';

        $descendants = $this->descendants()->findAll($criteria);
        $d = [];
        if (count($descendants) > 0) {
            foreach ($descendants as $value) {
                $d[] = $value->category_id;
            }
        } else {
            $d[] = $this->category_id;
        }
        return $d;
    }
}
