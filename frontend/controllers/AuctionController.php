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
 * Class AuctionController
 */
class AuctionController extends FrontController
{
    public function filters()
    {
        return [
            'accessControl',
            [
                'ESetReturnUrlFilter - bidBlitz, newBid',
            ],
            ['frontend.filters.XssFilter - view,newBid,bidBlitz,showBidsTable,getCity2,get_filter_city,removeBid',
                'clean' => 'all',
            ],
        ];
    }

    public function accessRules()
    {
        return [
            [
                'allow',
                'actions' => [
                    'index', 'view', 'newBid', 'bidBlitz', 'showBidsTable',
                    'getCity2', 'track_owner',
                    'get_filter_city', 'removeBid', 'sliderPopupInfo', 'popupSliderHtml',
                ],
                'users' => ['*'],
            ],
            ['deny'],
        ];
    }

    public function behaviors()
    {
        return [
            'seo' => [
                'class' => 'common.extensions.seo.SeoControllerBehavior',
                'defaultAttributeTitle' => 'name',
                'titleAttribute' => 'meta_title',
                'descriptionAttribute' => 'meta_description',
                'keywordsAttribute' => 'meta_keywords',
            ],
        ];

    }

    public function actions()
    {
        return [
            'newBid' => [
                'class' => 'frontend.controllers.auction.NewBidAction',
            ],
            'removeBid' => [
                'class' => 'frontend.controllers.auction.RemoveBidAction',
            ],
            'bidBlitz' => [
                'class' => 'frontend.controllers.auction.BidBlitzAction',
            ],
        ];
    }

    public function searchAuction()
    {
        $auction = Auction::model()->findByAttributes(['auction_id' => $_GET['search']]);

        if ($auction) {
            $this->redirect('/auction/' . $auction->auction_id);
        }
    }

    public function searchUser()
    {
        $user = User::model()->findByAttributes(['login' => $_GET['search']]);

        if (!$user) {
            $user = User::model()->findByAttributes(['nick' => $_GET['search']]);

            if ($user)
                $this->redirect(['/user', 'page' => $user->login]);
        } else
            $this->redirect(['/user', 'page' => $user->login]);
    }

    public function actionIndex()
    {
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search_active = true;

            $number_lot = trim(CHtml::encode($_GET['search']));

            if (is_numeric($number_lot)) {
                $res = Yii::app()->db->createCommand()
                    ->select('auction_id')
                    ->from('auction')
                    ->where('auction_id = :id', [':id' => $number_lot])
                    ->queryScalar();

                if (!empty($res)) {
                    $this->redirect('/auction/' . $res);
                }
            }
        } else {
            $search_active = false;
        }

        $num_page_size = $this->getPageSize();
        $path = Yii::app()->request->getParam('path', null);
        $this->layout = 'auction';
        $this->pageTitle = Yii::t('basic', 'Auction');

        if (!isset($_GET['filter'])) {
            $_GET['filter'] = 'oll';
        }
        $to_search_filter = $_GET['filter'];

        $auc_id_arr = [];
        $options = [];
        $category = false;
        $params = [];

        $status = Auction::ST_ACTIVE;
        $statusQuery = 'a.status=:status';

        $params[':status'] = $status;

        $sqlSelect = 'a.*, bid.price as current_bid';
        $sql = Yii::app()->db->createCommand()
            ->from('auction a')
            ->andWhere($statusQuery);
        $count_sql = Yii::app()->db->createCommand()->select('count(*)')->from('auction a')->andWhere($statusQuery);

        //filter
        $filter = new Filter();
        if (isset($_GET['Filter'])) {
            $filter->filters = $_GET['Filter'];
        }

        // Regions and Cities
        if ($filter->id_country && intval($filter->id_country) > 0) {
            $sql->andWhere('a.id_country=:id_country');
            $count_sql->andWhere('a.id_country=:id_country');
            $params[':id_country'] = $filter->id_country;
        }

        if ($filter->id_region && intval($filter->id_region) > 0) {
            $sql->andWhere('a.id_region=:id_region');
            $count_sql->andWhere('a.id_region=:id_region');
            $params[':id_region'] = $filter->id_region;
        }

        if ($filter->id_city && intval($filter->id_city) > 0) {
            $sql->andWhere('a.id_city=:id_city');
            $count_sql->andWhere('a.id_city=:id_city');
            $params[':id_city'] = $filter->id_city;
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = CHtml::encode($_GET['search']);

            $result = Item::searchHelper($search, $to_search_filter);

            if (count($result) > 0) {
                foreach ($result as $item) {
                    $auc_id_arr[] = intval($item['auction_id']);
                }

                $auc_list = implode(",", $auc_id_arr);

                $sql->andWhere("a.auction_id IN ($auc_list)");
                $count_sql->andWhere("a.auction_id IN ($auc_list)");

            } else {
                $sql->andWhere("a.auction_id=0");
                $count_sql->andWhere("a.auction_id=0");
            }

        }

        if ($filter->price_min == !'') {
            $q = '
                CASE 
                WHEN current_bid=0
                THEN 
                    CASE 
                    WHEN a.starting_price = 0
                    THEN a.price>=:price_min
                    ELSE a.starting_price>=:price_min
                    END
                ELSE current_bid>=:price_min
                END
            ';
            $sql->andWhere($q);
            $count_sql->andWhere($q);
            $params[':price_min'] = $filter->price_min;
        }
        if ($filter->price_max == !'') {
            $q = '
                CASE 
                WHEN current_bid=0
                THEN 
                    CASE 
                    WHEN a.starting_price = 0
                    THEN a.price<=:price_max
                    ELSE a.starting_price<=:price_max
                    END
                ELSE current_bid<=:price_max
                END
            ';
            $sql->andWhere($q);
            $count_sql->andWhere($q);
            $params[':price_max'] = $filter->price_max;
        }

        if (isset($_GET['Filter']['option'][0]) && (count($_GET['Filter']['option'][0]) > 0)) {

            $c_prm = [];
            $attributeOptions = $_GET['Filter']['option'][0];

            $joinedOption = [];
            foreach ($attributeOptions as $key => $value) {
                if ($value === '') {
                    continue;
                }
                if (isset($joinedOption[$key])) {
                    continue;
                }

                $optionChildren = [];
                if (is_array($value) && count($value) > 0) {
                    foreach ($value as $optionValue) {
                        if (strpos($optionValue, '_') === false) {
                            foreach ($attributeOptions as $key1 => $value1) {
                                if ($key != $key1) {
                                    if (is_array($value1) && $value1) {
                                        foreach ($value1 as $optionValue1Key => $optionValue1) {
                                            if (strpos($optionValue1, '_') !== false) {
                                                list($childOptionValue, $childParentOptionValue) = explode('_', $optionValue1);
                                                if (!isset($optionChildren[$childParentOptionValue])) {
                                                    $optionChildren[$childParentOptionValue] = [];
                                                }
                                                $optionChildren[$childParentOptionValue][$key1][$childOptionValue] = $childOptionValue;
                                                unset($attributeOptions[$key1][$optionValue1Key]);
                                                if (empty($attributeOptions[$key1])) {
                                                    unset($attributeOptions[$key1]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $sql->join('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id');
                $count_sql->join('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id');

                $optionsChildWhereConditions = [];
                if ($optionChildren) {
                    foreach ($optionChildren as $parentOptionValue => $childAttributes) {
                        foreach ($childAttributes as $childAttributeKey => $childAttributeOptions) {
                            if (!isset($joinedOption[$childAttributeKey])) {
                                $sql->join('auction_attribute_value as aav_' . $childAttributeKey, 'aav_' . $childAttributeKey . '.auction_id=a.auction_id');
                                $count_sql->join('auction_attribute_value as aav_' . $childAttributeKey, 'aav_' . $childAttributeKey . '.auction_id=a.auction_id');
                                $joinedOption[$childAttributeKey] = 1;
                            }
                            $optionsChildWhereConditions[$parentOptionValue] = [];
                            foreach ($childAttributeOptions as $childAttributeOptionValue) {
                                $optionsChildWhereConditions[$parentOptionValue][] = '(aav_' . $childAttributeKey . '.attribute_id=:attr_id_' . $childAttributeOptionValue . ' and aav_' . $childAttributeKey . '.value_id=:val_id_' . $childAttributeOptionValue . ')';
                                $params[':attr_id_' . $childAttributeOptionValue] = $childAttributeKey;
                                $params[':val_id_' . $childAttributeOptionValue] = $childAttributeOptionValue;
                                //count sql params
                                $c_prm[':attr_id_' . $childAttributeOptionValue] = $childAttributeKey;
                                $c_prm[':val_id_' . $childAttributeOptionValue] = $childAttributeOptionValue;
                            }
                        }
                    }
                }

                $where = '';
                if (is_array($value) && count($value) > 0) {
                    foreach ($value as $i) {
                        if (!empty($i) || $i == '0') {
                            $childCondition = ' ';
                            if ($i > 0 && !empty($optionsChildWhereConditions[$i])) {
                                $childCondition = ' AND (' . implode(' OR ', $optionsChildWhereConditions[$i]) . ') ';
                            }

                            if (!empty($where)) {
                                $where .= ' OR  (aav_' . $key . '.attribute_id=:attr_id_' . $i . ' and aav_' . $key . '.value_id=:val_id_' . $i . $childCondition . ' ) ';
                            } else {
                                $where .= '(aav_' . $key . '.attribute_id=:attr_id_' . $i . ' and aav_' . $key . '.value_id=:val_id_' . $i . $childCondition . ' ) ';
                            }

                            $params[':attr_id_' . $i] = $key;
                            $params[':val_id_' . $i] = $i;
                            //count sql params
                            $c_prm[':attr_id_' . $i] = $key;
                            $c_prm[':val_id_' . $i] = $i;
                        }
                    }
                    $sql->andWhere($where);
                    $count_sql->andWhere($where, $c_prm);
                }
            }
        }

        if (isset($_GET['Filter']['option'][1]) && count($_GET['Filter']['option'][1]) > 0) {
            foreach ($_GET['Filter']['option'][1] as $key => $value) {
                if (preg_match("/^[0-9]+$/", $key) && ((isset($value['from']) && $value['from'] > 0) || (isset($value['to']) && $value['to'] > 0))) {
                    $sql->leftJoin('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id AND aav_' . $key . '.attribute_id=' . $key);
                    $count_sql->leftJoin('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id AND aav_' . $key . '.attribute_id=' . $key);

                    if (isset($value['from']) && preg_match("/^[0-9]+$/", $value['from']) && $value['from'] > 0) {
                        $from = $value['from'];
                        $sql->andWhere('aav_' . $key . '.value >= ' . $from);
                        $count_sql->andWhere('aav_' . $key . '.value >= ' . $from);
                    }

                    if (isset($value['to']) && preg_match("/^[0-9]+$/", $value['to']) && $value['to'] > 0) {
                        $to = $value['to'];
                        $sql->andWhere('aav_' . $key . '.value <= ' . $to);
                        $count_sql->andWhere('aav_' . $key . '.value <= ' . $to);
                    }
                }
            }
        }
        //end filter

        //category----------------------------------------------------------------------
        if ($path != 'all') {
            //find by path
            $categories = explode('/', $path);
            $category_name = array_pop($categories);
            $category = Category::model()->find('alias=:alias', [':alias' => $category_name]);

        }

        if (!empty($_GET['cat']) && $_GET['cat'] != '-') {
            $category = Category::model()->find('category_id=:category_id', [':category_id' => $_GET['cat']]);

            if ($_GET['cat'] == 'users') {
                $this->searchUser();
                $sql->andWhere("auction_id = 0");
                $count_sql->andWhere("auction_id = 0");
            }

            if ($_GET['cat'] == 'auction') {
                $this->searchAuction();
                $sql->andWhere("auction_id = 0");
                $count_sql->andWhere("auction_id = 0");
            }

        }

        $d = [];
        if ($category) {
            $_GET['path'] = $category->getPath();
            $d = $category->getAllDependents();
            if (count($d) == 0) {
                $d[0] = 0;
            }
            $count_sql->andWhere(['in', 'category_id', $d]);
            $sql->andWhere(['in', 'category_id', $d]);

            //OPTIONS --------------------------------------------------------------
            $sql_options = <<<SQL
        select
            a.name,
            a.attribute_id,
            a.type,
            a.child_id,
            a.show_expanded
        from attribute a
        left join category_attributes ca on ca.attribute_id=a.attribute_id
        where ca.category_id=:id
        group by a.attribute_id
        order by ca.sort ASC
SQL;
            $options = Yii::app()->db->createCommand($sql_options)->queryAll(
                true,
                [':id' => $category->category_id]
            );
        }
        //end category

        //filter type_transaction
        if (isset($_GET['filter'])) {
            if ($_GET['filter'] == 'default') {
                $count_sql->andWhere(' a.starting_price != 0');
                $sql->andWhere('a.starting_price != 0');
            }
            if ($_GET['filter'] == 'buynow') {
                $count_sql->andWhere('a.price != 0');
                $sql->andWhere('a.price != 0');
            }
            if ($_GET['filter'] == 'nulls') {
                $count_sql->andWhere('a.type_transaction=' . Auction::TP_TR_START_ONE);
                $sql->andWhere('a.type_transaction=' . Auction::TP_TR_START_ONE);
            }
        }

        $createdForValue = Yii::app()->request->getQuery('period');
        switch ($createdForValue) {
            case '3h':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 HOUR)');
                $count_sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 HOUR)');
                break;
            case '12h':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 HOUR)');
                $count_sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 HOUR)');
                break;
            case '1d':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY)');
                $count_sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY)');
                break;
            case '3d':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 DAY)');
                $count_sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 DAY)');
                break;
            case '1w':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 WEEK)');
                $count_sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 WEEK)');
                break;
        }

        $cacheKey = 'auction_index_' . $_SERVER['REQUEST_URI'];
        $count = Yii::app()->cache->get($cacheKey);

        if ($count === false) {
            $count = $count_sql->queryScalar($params);
            Yii::app()->cache->set($cacheKey, $count, 60 * 3);
        }

        $sql->leftJoin('bids bid', 'bid.bid_id=a.current_bid');
        $sql->select($sqlSelect);

        $dataProvider = new CSqlDataProvider($sql->text, [
            'totalItemCount' => $count,
            'keyField' => 'auction_id',
            'params' => $params,
            'sort' => [
                'multiSort' => false,
                'attributes' => [
                    'price' => [
                        'asc' => 'IF(current_bid=0, IF (a.starting_price = 0, a.price, a.starting_price), current_bid) ASC',
                        'desc' => 'IF(current_bid=0, IF (a.starting_price = 0, a.price, a.starting_price), current_bid) DESC',
                        'label' => 'Item Price',
                        'default' => 'desc',
                    ],
                    'date' => [
                        'asc' => 'created',
                        'desc' => 'created  DESC',
                        'label' => 'Item date',
                        'default' => 'desc',
                    ],
                    'viewed' => [
                        'asc' => 'viewed',
                        'desc' => 'viewed DESC',
                        'label' => 'Item viewed',
                        'default' => 'desc',
                    ],
                    'numBids' => [
                        'asc' => 'bid_count ASC',
                        'desc' => 'bid_count DESC',
                    ],
                    'dateEnd' => [
                        'asc' => 'bidding_date',
                        'desc' => 'bidding_date DESC',
                        'default' => 'asc',
                    ],
                ],
                'defaultOrder' => 'auction_order DESC',
            ],
            'pagination' => [
                'pageSize' => $num_page_size,
            ],
        ]);

        if ($count == 0) {
            $search_active = false;
        }

        $options = AttributeHelper::makeNestedDependentExpanded(
            Models::indexBy($options, 'attribute_id')
        );

        $showRecommendedContainer = empty($_GET['sort']);

        $auctions = $dataProvider->getData();

        // Select all seller for using in views
        $userIds = ArrayHelper::getColumn($auctions, 'owner');
        $userIds = array_filter($userIds, function ($id) {
            return $id > 0;
        });
        $users = User::getByIds(
            $userIds,
            'user_id, pro, rating, login, nick, online, certified',
            'user_id'
        );

        // Select all cities for using in views
        $cityIds = array_filter(ArrayHelper::getColumn($auctions, 'id_city'));
        $cityIds[] = 0;
        $cities = ArrayHelper::index(
            City::model()->with('region', 'country')->findAllByPk($cityIds), 'id_city'
        );

        $auctionsImages = AuctionHelper::getImagesByIds(
            ArrayHelper::getColumn($auctions, 'auction_id')
        );

        $this->render(
            'index',
            [
                'category' => $category,
                'dataProvider' => $dataProvider,
                'users' => $users,
                'cities' => $cities,
                'filter' => $filter,
                'options' => $options,
                'search_active' => $search_active,
                'showRecommendedContainer' => $showRecommendedContainer,
                'auctionsImages' => $auctionsImages,
                'auc_id_arr' => $auc_id_arr,
            ]
        );
    }

    public static function getAllDependentsCategory($category)
    {
        if (is_null($category)) {
            throw new CHttpException(404);
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'category_id';

        $descendants = $category->descendants()->findAll($criteria);
        $d = [];
        if (count($descendants) > 0) {
            foreach ($descendants as $value) {
                $d[] = $value->category_id;
            }
        } else {
            $d[] = $category->category_id;
        }
        return $d;
    }

    public function actionView($id)
    {

        $this->layout = 'auction';

        //  $dependency = new CDbCacheDependency('SELECT `update` FROM auction WHERE auction_id='.$id);
        $data = Yii::app()->db->createCommand()
            ->select(
                'a.*, bid.price as current_bid, bid.bid_id as current_bid_id, u.login as user_login, u.pro as user_pro, u.rating as user_rating, u.user_id, f.favorite_id'
            )
            ->from('auction a')
            ->leftJoin('bids bid', 'bid.bid_id=a.current_bid')
            ->leftJoin('users u', 'u.user_id=a.owner')
            ->leftJoin(
                'favorites f',
                'f.item_id=a.auction_id and f.user_id=:f_user_id',
                [':f_user_id' => Yii::app()->user->id]
            )
            ->where('auction_id=:auction_id', [':auction_id' => $id])
            ->queryRow();

        if ($data == false) {
            throw new CHttpException(404);
        }

        $isOwnerUser = Getter::userModel() && Getter::userModel()->user_id == $data['owner'];

        if (!$isOwnerUser) {
            Auction::model()->updateCounters(['viewed' => 1], 'auction_id=:id', [':id' => $id]);
        }

        $today = date('Y-m-d');
        $isset_counter_for_day = Yii::app()->db->createCommand()->select('day_viewed, viewed_count_id')->from('viewed_count')
            ->where('auction_id=:auction_id AND date_viewed=:date_viewed', [':auction_id' => $id, ':date_viewed' => $today])->queryRow();

        if (!$isOwnerUser) {
            if (!isset($isset_counter_for_day['viewed_count_id'])) {
                Yii::app()->db->createCommand()
                    ->insert(
                        'viewed_count',
                        [
                            'auction_id' => $id,
                            'day_viewed' => 1,
                            'type' => 0,
                            'date_viewed' => $today,
                        ]
                    );
            } else {
                $new_counter = $isset_counter_for_day['day_viewed'] + 1;
                Yii::app()->db->createCommand()
                    ->update(
                        'viewed_count',
                        ['day_viewed' => $new_counter],
                        'auction_id=:auction_id AND date_viewed=:date_viewed',
                        [':auction_id' => $id, ':date_viewed' => $today]
                    );
            }
        }

        //  $dependency2 = new CDbCacheDependency('SELECT MAX(`update`) FROM auction_attribute_value WHERE auction_id='.$id);
        $params = Yii::app()->db->createCommand()
            ->select('a.name, ca.sort, av.value as av_value, acv.value as value, a.type, a.child_id')
            ->from('auction_attribute_value acv')
            ->leftJoin('attribute a', 'a.attribute_id=acv.attribute_id')
            ->join(
                'category_attributes ca',
                'ca.category_id=:category_id and  ca.attribute_id=acv.attribute_id',
                [':category_id' => $data['category_id']]
            )
            ->leftJoin('attribute_values av', 'av.attribute_id=acv.attribute_id and av.value_id=acv.value_id')
            ->where('auction_id=:auction_id AND a.type!=8', [':auction_id' => $id])
            ->order('ca.sort ASC')
            ->queryAll();

        $images = Yii::app()->db->createCommand()
            ->select('image')
            ->from('images')
            ->where('item_id=:item_id', [':item_id' => $data['auction_id']])
            ->order('sort')
            ->queryColumn();

        $socialAuctionImageUrl = AuctionHelper::getSocialImageUrl($data);
        $this->addMetaTag(['property' => 'og:image', 'content' => $socialAuctionImageUrl]);
        $this->addMetaTag(['itemprop' => 'image', 'content' => $socialAuctionImageUrl]);

        $questionForm = new FormQuestion();

        $this->render(
            'view',
            [
                'base' => $data,
                'params' => $params,
                'images' => $images,
                'questionForm' => $questionForm,
            ]
        );
    }

    public function actionShowBidsTable($auction_id)
    {
        $auction = Auction::model()->findByPk($auction_id);

        if ($auction->owner == Yii::app()->user->id) {
            $this->renderPartial(
                '_bids_table',
                [
                    'auction_id' => $auction_id,
                ]
            );
        } else {
            $this->renderPartial(
                '_bids_table_all',
                [
                    'auction_id' => $auction_id,
                ]
            );
        }
    }

    public function actionTrack_owner($owner)
    {
        if (!Yii::app()->user->isGuest && Yii::app()->request->isAjaxRequest) {
            $usr = Yii::app()->user->id;
            $model = User::model()->findByPk($owner);

            if (isset($model->user_id)) {
                $tr = TrackOwners::model()->count('owner=:owner AND id_user=:id_user', [':owner' => $model->user_id, ':id_user' => $usr]);

                if ($tr == 0) {
                    $tr_new = new TrackOwners();
                    $tr_new->owner = $model->user_id;
                    $tr_new->id_user = $usr;
                    $tr_new->crt_date = time();
                    $tr_new->save();

                    RAjax::success(['stat' => 0]);
                } else {
                    TrackOwners::model()->deleteAll('owner=:owner AND id_user=:id_user', [':owner' => $model->user_id, ':id_user' => $usr]);
                    RAjax::success(['stat' => 1]);
                }
            }
        } else {
            throw new CHttpException(404);
        }
    }

    public function actionGet_filter_city($id)
    {
        if (Yii::app()->request->isAjaxRequest && preg_match("/^[0-9]+$/", $id)) {
            $goroda = Yii::app()->db->createCommand()
                ->select('*')
                ->from('locations')
                ->where('locations_main_id=:locations_main_id AND status=0', [':locations_main_id' => $id])
                ->queryAll();

            echo '
                <label>' . Yii::t('basic', 'City') . '</label>
                <p class="city_f">
                <select tabindex="1" autocomplete="off" name="Filter[city]" id="Filter_city">
                <option value="">' . Yii::t('basic', 'select a city') . '</option>
            ';

            if (!empty($goroda)) {
                foreach ($goroda as $gorod) {
                    echo '<option value="' . $gorod['location_id'] . '">' . $gorod['name'] . '</option>';
                }
            }

            echo '</select></p>';
        }
    }
}
