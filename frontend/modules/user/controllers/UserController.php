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


class UserController extends FrontController
{

	protected $user;

	public function filters()
	{
		return array(
			'accessControl',
			array(
				'ESetReturnUrlFilter - login, logout, recovery'
			)
		);
	}

	public function accessRules()
	{
		return [
			[
				'allow',
				'actions' => [
					'login',
					'logout',
					'registration',
					'recovery',
					'page',
					'about_me',
                                        'landing',
				],
				'users'   => ['*'],
			],
			[
				'allow',
				'actions' => ['addFavorite'],
				'users'   => ['@'],
			],
			['deny'],
		];
	}

	public function actions()
	{
		return array(
			'login' => array(
				'class' => 'frontend.modules.user.controllers.actions.LoginAction'
			),
			'registration' => array(
				'class' => 'frontend.modules.user.controllers.actions.RegistrationAction'
			),
			'recovery' => array(
				'class' => 'frontend.modules.user.controllers.actions.RecoveryAction'
			)
		);
	}

	/**
	 * @param $login
	 *
	 * @throws CException
	 * @throws CHttpException
	 */
    public function actionPage($login)
    {
        $pageSize = $this->getPageSize();

        $ownerUser = User::getByLogin($login);
        $this->pageTitle = Yii::t('basic', 'Items of')." " . $ownerUser->getNickOrLogin();
        $this->layout = '//layouts/auction';
        $this->user = $ownerUser;
        $this->searchAction = '/user/page/'.$ownerUser->login;
        $this->userNick = $ownerUser->getNickOrLogin();

        $auction = new Auction;
        if (isset($_GET['Auction'])) {
            $auction->attributes = $_GET['Auction'];
        }
        if (!isset($_GET['Auction']['type_transaction'])) {
            $auction->type_transaction = null;
        }

        $gridViewAjaxUrl = Yii::app()->createUrl('/'.$login);

        $params = [
            ':owner'  => $ownerUser->user_id,
            ':status' => Auction::ST_ACTIVE,
        ];

        /** @var CDbCommand $sql */
        $sql = Yii::app()->db->createCommand()
            ->select('a.*,
                  bid.price as current_bid')
            ->from('auction a')
            ->leftJoin('bids bid', 'bid.bid_id=a.current_bid')
            ->where('a.status=:status and a.owner=:owner');

        $sqlCount = Yii::app()->db->createCommand()
            ->select('COUNT(*)')
            ->from('auction a')
            ->where('a.status=:status and a.owner=:owner');

        if (isset($auction) && is_numeric($auction->type_transaction)) {
            $sql->andWhere('a.type_transaction = :type_transaction');
            $sqlCount->andWhere('a.type_transaction = :type_transaction');
            $params[':type_transaction'] = $auction->type_transaction;
        }


        $filter = new Filter();
        if (isset($_GET['Filter'])) {
            $filter->filters = $_GET['Filter'];
        }

            $search = isset($_GET['search'])?strip_tags($_GET['search']):'';

            $result = Item::searchHelper($search, false, $ownerUser->user_id);

            if (count($result) > 0) {
                foreach ($result as $item) {
                    $auc_id_arr[] = intval($item['auction_id']);
                }

                $auc_list = implode(",", $auc_id_arr);

                $sql->andWhere("a.auction_id IN ($auc_list)");
                $sqlCount->andWhere("a.auction_id IN ($auc_list)");

            } else {
                $sql->andWhere("a.auction_id=0");
                $sqlCount->andWhere("a.auction_id=0");
                $auc_id_arr = [];
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
            $sqlCount->andWhere($q);
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
            $sqlCount->andWhere($q);
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
                $sqlCount->join('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id');

                $optionsChildWhereConditions = [];
                if ($optionChildren) {
                    foreach ($optionChildren as $parentOptionValue => $childAttributes) {
                        foreach ($childAttributes as $childAttributeKey => $childAttributeOptions) {
                            if (!isset($joinedOption[$childAttributeKey])) {
                                $sql->join('auction_attribute_value as aav_' . $childAttributeKey, 'aav_' . $childAttributeKey . '.auction_id=a.auction_id');
                                $sqlCount->join('auction_attribute_value as aav_' . $childAttributeKey, 'aav_' . $childAttributeKey . '.auction_id=a.auction_id');
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
                    $sqlCount->andWhere($where, $c_prm);
                }
            }
        }

        if (isset($_GET['Filter']['option'][1]) && count($_GET['Filter']['option'][1]) > 0) {
            foreach ($_GET['Filter']['option'][1] as $key => $value) {
                if (preg_match("/^[0-9]+$/", $key) && ((isset($value['from']) && $value['from'] > 0) || (isset($value['to']) && $value['to'] > 0))) {
                    $sql->leftJoin('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id AND aav_' . $key . '.attribute_id=' . $key);
                    $sqlCount->leftJoin('auction_attribute_value as aav_' . $key, 'aav_' . $key . '.auction_id=a.auction_id AND aav_' . $key . '.attribute_id=' . $key);

                    if (isset($value['from']) && preg_match("/^[0-9]+$/", $value['from']) && $value['from'] > 0) {
                        $from = $value['from'];
                        $sql->andWhere('aav_' . $key . '.value >= ' . $from);
                        $sqlCount->andWhere('aav_' . $key . '.value >= ' . $from);
                    }

                    if (isset($value['to']) && preg_match("/^[0-9]+$/", $value['to']) && $value['to'] > 0) {
                        $to = $value['to'];
                        $sql->andWhere('aav_' . $key . '.value <= ' . $to);
                        $sqlCount->andWhere('aav_' . $key . '.value <= ' . $to);
                    }
                }
            }
        }

        if (!empty($_GET['Auction']['type_transaction'])) {
            $typeTransaction = $_GET['Auction']['type_transaction'];
            if ($typeTransaction == 'default') {
                $sqlCount->andWhere(' a.starting_price != 0');
                $sql->andWhere('a.starting_price != 0');
            }
            if ($typeTransaction == 'buynow') {
                $sqlCount->andWhere('a.price !=0');
                $sql->andWhere('a.price !=0');
            }
            if ($typeTransaction == 'nulls') {
                $sqlCount->andWhere('a.type_transaction=' . Auction::TP_TR_START_ONE);
                $sql->andWhere('a.type_transaction=' . Auction::TP_TR_START_ONE);
            }
        }

        $selectedCategoryModel = null;
        $path = Yii::app()->request->getParam('path', null);

        if ($path != 'all') {
            //find by path
            $categoryNames = explode('/', $path);
            $categoryName = array_pop($categoryNames);
            $selectedCategoryModel = Category::model()->find('alias=:alias', [':alias' => $categoryName]);
            $this->searchAction .= '/'.$categoryName;
        } elseif ($filter->cat !== '') {
            $selectedCategoryModel = Category::model()->find('category_id=:category_id', [':category_id' => $filter->cat]);
        }

        if (!empty($_GET['cat']) && $_GET['cat'] != '-') {
            $selectedCategoryModel = Category::model()->find('category_id=:category_id', [':category_id' => $_GET['cat']]);
        }

        $attributeOptions = [];
        if ($selectedCategoryModel) {
            $_GET['path'] = $selectedCategoryModel->getPath();
            $d = $selectedCategoryModel->getAllDependents();

            if (count($d) == 0) {
                $d[0] = 0;
            }

            $sql->andWhere(['in', 'category_id', $d]);
            $sqlCount->andWhere(['in', 'category_id', $d]);

            $sqlOptions = '
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
            ';
            $attributeOptions = Yii::app()->db->createCommand($sqlOptions)->queryAll(
                true, [':id' => $selectedCategoryModel->category_id]
            );
        }

        switch (Yii::app()->request->getQuery('period')) {
            case '3h':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 HOUR)');
                $sqlCount->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 HOUR)');
                break;
            case '12h':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 HOUR)');
                $sqlCount->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 12 HOUR)');
                break;
            case '1d':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY)');
                $sqlCount->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY)');
                break;
            case '3d':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 DAY)');
                $sqlCount->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 3 DAY)');
                break;
            case '1w':
                $sql->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 WEEK)');
                $sqlCount->andWhere('a.created > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 WEEK)');
                break;
        }

        $auctionCount = $sqlCount->queryScalar($params);

        $dataProvider = new CSqlDataProvider($sql->text, [
            'totalItemCount' => $auctionCount,
            'keyField'       => 'auction_id',
            'params'         => $params,
            'sort'           => [
                'multiSort'    => false,
                'attributes'   => [
                    'price'   => [
                        'asc'     => 'IF(current_bid=0, IF (a.starting_price = 0, a.price, a.starting_price), current_bid) ASC',
                        'desc'    => 'IF(current_bid=0, IF (a.starting_price = 0, a.price, a.starting_price), current_bid) DESC',
                        'label'   => 'Item Price',
                        'default' => 'desc',
                    ],
                    'date'    => [
                        'asc'     => 'created',
                        'desc'    => 'created  DESC',
                        'label'   => 'Item date',
                        'default' => 'desc',
                    ],
                    'viewed'  => [
                        'asc'     => 'viewed',
                        'desc'    => 'viewed DESC',
                        'label'   => 'Item viewed',
                        'default' => 'desc',
                    ],
                    'numBids' => [
                        'asc'  => 'bid_count ASC',
                        'desc' => 'bid_count DESC',
                    ],
                    'dateEnd' => [
                        'asc'     => 'bidding_date',
                        'desc'    => 'bidding_date DESC',
                        'default' => 'asc',
                    ],
                ],
                'defaultOrder' => 'auction_order DESC',
            ],
            'pagination'     => [
                'pageSize' => $pageSize,
            ],
        ]);
        $auctions = $dataProvider->getData();

        $cityIds = array_filter(ArrayHelper::getColumn($auctions, 'id_city'));
        $cityIds[] = 0;
        $cities = ArrayHelper::index(
            City::model()->with('region', 'country')->findAllByPk($cityIds), 'id_city'
        );

        $attributeOptions = AttributeHelper::makeNestedDependentExpanded(
            Models::indexBy($attributeOptions, 'attribute_id')
        );

        $auctionsImages = AuctionHelper::getImagesByIds(
            ArrayHelper::getColumn($auctions, 'auction_id')
        );

        $this->render(
            'page',
            [
                'showHeaderTabs'           => true,
                'ownerModel'               => $ownerUser,
                'auction'                  => $auction,
                'gridCssClass'             => 'lots_table_border_top_bottom table_with_filter',
                'gridViewPager'            => ['class' => 'CLinkPager', 'header' => ''],
                'gridViewSummaryText'      => Yii::t('basic', 'Showed {start} to {end}. All {count}'),
                'scope'                    => 'user-page',
                'gridViewAjaxUrl'          => $gridViewAjaxUrl,
                'showRecommendedContainer' => empty($_GET['sort']),
                'filter'                   => $filter,
                'category'                 => $selectedCategoryModel,
                'attributeOptions'         => $attributeOptions,
                'countForTitle'            => $auctionCount,
                'cities'                   => $cities,
                'dataProvider'             => $dataProvider,
                'auctionsImages'           => $auctionsImages,
                'auc_id_arr'               => $auc_id_arr,
            ]
        );
    }

    public function actionAbout_me($login)
	{
		$user = User::getByLogin($login);

        $user_name = $user->getNickOrLogin();

		$this->pageTitle = Yii::t('basic', 'About').' '.$user_name;
		$this->layout = '//layouts/userPageLayout';
		$this->user = $user;

        $this->prepareUserCategoriesTreeData($user->user_id);
        $this->searchAction = '/user/page/'.$user->login;
        $this->userNick = $user_name;

		$this->render('aboutMe', array('model' => $user));
        }

    public function actionLanding($login)
	{
		$user = User::getByLogin($login);

        $this->searchAction = '/user/page/' . $user->login;
        $this->userNick = $user->getNickOrLogin();
        $this->pageTitle = Yii::t('basic', 'User').' ' . $this->userNick;
        $this->layout = '//layouts/auction';
        $this->user = $user;

                $imageLink = '/images/users/thumbs/avatar_'.$user->avatar;
                $this->addMetaTag(['property' => 'og:image', 'content' => $imageLink]);
                $this->addMetaTag(['itemprop' => 'image', 'content' => $imageLink]);

		$this->render('landing', array('model' => $user));
	}

	public function actionAddFavorite($id, $type)
	{
		if (!Favorite::hasFavorite($id, $type, Yii::app()->user->id)) {
			if (Favorite::createFavorite($id, $type, Yii::app()->user->id)) {
				RAjax::success(array('id' => Yii::app()->db->lastInsertID, 'stat' => 0));
			} else {
				RAjax::error(array('message' => 'error save'));
			}
		}
        else
        {
			Favorite::deleteFavorite($id, $type, Yii::app()->user->id);
            RAjax::success(array('stat' => 1));
        }
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}


}
