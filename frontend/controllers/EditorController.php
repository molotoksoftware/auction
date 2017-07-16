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
 * Edit item
 *
 * EditorController class
 *
 */
class EditorController extends FrontController
{

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'imageSave',
                    'dynamicCategoriesForSelect',
                    'landmarkName',
                    'getOptions',
                    'success',
                ),
                'users' => array('*')
            ),
            array(
                'allow',
                'actions' => array('lot', 'longTermCompleted', 'removeTrading', 'setAuctionCity', 'setAutoRepublish', 'massAutoRepub'),
                'users' => array('@')
            ),
            array('deny'),
        );
    }

    public $defaultAction = 'new';


    public function actionLot($id)
    {

        /** @var Auction $model */
        $request = Yii::app()->getRequest();
        $model = Auction::model()->findByPk($id);
        if (is_null($model) || $model->owner !== Yii::app()->user->id) {
            throw new CHttpException(404);
        }
        $this->pageTitle = Yii::t('basic', 'Edit').' "' . $model->name . '"';


        $sql = <<<SQL
        select
            a.name,
            a.attribute_id,
            acv.auction_id,
            a.type,
            a.mandatory,
            acv.value,
            value_id
        from attribute a
        left join category_attributes ca on ca.attribute_id=a.attribute_id
        left join auction_attribute_value acv on acv.attribute_id=a.attribute_id and acv.auction_id=:auction_id
        where ca.category_id=:id and a.type<>:type
        group by a.attribute_id
        order by ca.sort ASC
SQL;

        $options = Yii::app()->db->createCommand($sql)->queryAll(
            true,
            array(
                ':id' => $model->category_id,
                ':auction_id' => $id,
				':type' => Attribute::TYPE_CHILD_ELEMENT
            )
        );


        $this->performAjaxValidation($model, 'form-create-lot');

        $this->save($model);

        $this->render(
            'lot',
            [
                'model'                   => $model,
                'ItemOptions'             => $options,
            ]
        );
    }

    protected function refreshAuction(&$model)
    {

        $model->current_bid = 0;

        //bids
        Yii::app()->db->createCommand()
            ->delete(
                'bids',
                'lot_id=:lot_id',
                array(
                    ':lot_id' => (int)$model->auction_id
                )
            );


        $user = User::model()->findByPk($model->owner);
        $user->rating -= 1;
        $user->update(array('rating'));


    }

    public function setEndingDate(&$model, $refresh)
    {
        /**
         * @var $model CActiveRecord
         */
        if ($model->isNewRecord || $refresh == false) {
            return;
        }

        $date = new DateTime();
        $model->created = $date->format('Y-m-d H:i:s');
        $interval_spec = Auction::getDateSpecForDuration($model->duration);
        $date->add(new DateInterval($interval_spec));
        $model->bidding_date = $date->format('Y-m-d H:i:s');
        $model->status = BaseAuction::ST_ACTIVE;
        $model->current_bid = 0;
        $model->bid_count = 0;
        /* remove tables */

        //bids
        Yii::app()->db->createCommand()->delete('bids', 'lot_id=:lot_id', array(':lot_id' => (int)$model->auction_id));
        AutoBid::model()->deleteAllByAttributes(array('auction_id' => $model->auction_id));
    }

    /**
     * @param $model BaseAuction
     * @throws CHttpException
     * @throws Exception
     */
    public function save(&$model)
    {
        /** @var CHttpRequest $request */
        $class = get_class($model);
        if (Yii::app()->request->isPostRequest && isset($_POST[$class])) {
            $refresh = (isset($_POST['refresh'])) ? (boolean)$_POST['refresh'] : false;
            
            
            if($model->status == BaseAuction::ST_SAME) {
                $to_status = (isset($_POST['to_status']) &&  $_POST['to_status']==4)?4:1;
                $model->status = $to_status;
                if($to_status == 4) {
                    $model->bidding_date = date('Y-m-d H:i:s', time());
                } else $refresh = true;
            }

            if ($refresh == false) {
                unset($_POST[$class]['duration']);
            }


            $model->attributes = $_POST[$class];

            // $model->viewed = 0;

            $text_item = str_replace("<a href", '<a rel="nofollow" href', $model->text);

            $text_item = preg_replace_callback(
                '/<a(.*?)>(.*?)<\/a>/',
                function($m) 
                {
                    $posit = strripos($m[2], 'img');

                    if ($posit === false) 
                    {
                        $num = iconv_strlen($m[2], 'UTF-8');
                        if ($num > 23)
                        {
                            $m[2] = mb_substr($m[2],0,23,"UTF-8");
                            return '<a'.$m[1].'>'.$m[2].'...</a>';
                        }
                        else {return '<a'.$m[1].'>'.$m[2].'</a>';}
                    }
                    else
                    {
                        return '<a'.$m[1].'>'.$m[2].'</a>';
                    }
                },
            $text_item);

			$model->text = $text_item;

            $model->owner = Yii::app()->user->id;

            if ($model->type == BaseAuction::TYPE_AUCTION) {
                $this->setEndingDate($model, $refresh);
            }

            if (!$model->isNewRecord && $model->type == BaseAuction::TYPE_AUCTION) {
                if ($model->hasBids() && $model->status == BaseAuction::ST_ACTIVE) {
                    $this->refreshAuction($model);
                }
            }

            if ($model->save()) {
                $this->imageSave($model);

                $transaction = $model->dbConnection->beginTransaction();
                try {
                    if (isset($_POST['options'])) {
                        if (!$model->isNewRecord) {
                            Yii::app()->db->createCommand()
                                ->delete(
                                    'auction_attribute_value',
                                    'auction_id=:auction_id',
                                    array(':auction_id' => $model->auction_id)
                                );
                        }
                        if (!empty($_POST['options'][0])) {
                            foreach ($_POST['options'][0] as $key => $value) {
                                //checkbox list
                                if (is_array($value)) {
                                    foreach ($value as $i => $item) {
                                        Yii::app()->db->createCommand()
                                            ->insert(
                                                'auction_attribute_value',
                                                array(
                                                    'auction_id' => $model->auction_id,
                                                    'attribute_id' => $key,
                                                    'value_id' => $item,
                                                )
                                            );
                                    }
                                } else {
                                    Yii::app()->db->createCommand()
                                        ->insert(
                                            'auction_attribute_value',
                                            array(
                                                'auction_id' => $model->auction_id,
                                                'attribute_id' => $key,
                                                'value_id' => $value,
                                            )
                                        );
                                }
                            }
                        }

                        if (!empty($_POST['options'][1])) {
                            foreach ($_POST['options'][1] as $key => $value) {
                                Yii::app()->db->createCommand()
                                    ->insert(
                                        'auction_attribute_value',
                                        array(
                                            'auction_id' => $model->auction_id,
                                            'attribute_id' => $key,
                                            'value' => $value,
                                        )
                                    );
                            }
                        }
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                $img_count = Yii::app()->db->createCommand()->select('COUNT(*)')->from('images')
				    ->where('item_id=:item_id', array(':item_id' => $model->auction_id))->queryScalar();
                Yii::app()->db->createCommand()
				    ->update('auction', array('image_count' => $img_count), 'auction_id=:auction_id', array(':auction_id' => $model->auction_id));

                $this->redirect(array('/auction/view', 'id' => $model->auction_id));
            }
        }
    }

    protected function removeImageByItem($itemId)
    {
        $images = Yii::app()->db->createCommand()
            ->select('image ')
            ->from('images')
            ->where('item_id=:item_id', array(':item_id' => $itemId))
            ->queryAll();

        $save_path = Yii::getPathOfAlias('frontend') . '/www/i/';
        if (count($images) > 0) {
            foreach ($images as $img) {
                @unlink($save_path . $img['image']);
                //delete resize
                $type = get_class($this);
                foreach ($type::$versions as $v_name => $v_params) {
                    @unlink($save_path . 'thumbs' . DIRECTORY_SEPARATOR . $v_name . '_' . $img['image']);
                }
                //end delete resize
            }
        }
        Yii::app()->db->createCommand()
            ->delete('images', 'item_id=:item_id', array(':item_id' => $itemId));
    }

    public function imageSave($model)
    {

        /** ---------- IMAGES ------------------* */
        if (isset($_POST['identifier'])) {

            Yii::import('backend.extensions.imageUploader.ImageUploaderHelper');
            $images = ImageUploaderHelper::getFilesById($_POST['identifier'], 'frontend');
            if (!empty($images)) {
                $sorted = $_POST['sort'];
                foreach ($images as $img) {

                    $splitFileName = explode('.',basename($img['file']));
                    $imageHash = md5(uniqid() . $model->auction_id . $splitFileName[0]);

                    $imageModel = new ImageAR();
                    $imageModel->item_id = $model->auction_id;
                    $imageModel->image = $imageHash;
                    $imageModel->sort = (isset($sorted[$img['id']])) ? $sorted[$img['id']] : 0;
                    $imageModel->type = 0;
                    $imageModel->save(false);

                    $file = $splitFileName[0]. "_" . $imageModel->getPrimaryKey() . "." . $splitFileName[1];

                    $imageModel->image = $file;
                    $imageModel->update(['image']);

                    //resize
                    $type = get_class($model);
                    foreach ($type::$versions as $v_name => $v_params) {
                        if ($v_name == 'big') {
                            $imageComp = Getter::imageHandler()->load($img['file']);
                            $args = array_values(array_values($v_params)[0]);
                            $imageComp->thumb($args[0], $args[1]);
                            if ($v_name == 'big') {
                                $imageComp->watermark(Yii::getPathOfAlias('frontend.www.img') . '/watermark.png', 10, 10);
                            }
                            $imageComp->save(
                                ImageAR::getImageSavePath($model->owner, true, $v_name . '_' . $file)
                            );
                        } else {
                            $org_image = Getter::image()->load($img['file']);
                            $method = key($v_params);
                            $args = array_values($v_params);
                            call_user_func_array(
                                array($org_image, $v_name == 'big' ? 'maxWidth' : $method),
                                is_array($args[0]) ? $args[0] : array($args[0])
                            );
                            if ($v_name == 'big') {
                                $org_image->watermark(
                                    Yii::getPathOfAlias('frontend.www.img') . '/watermark.png',
                                    10,
                                    10
                                );
                            }
                            $org_image->save(ImageAR::getImageSavePath($model->owner, true, $v_name . '_' . $file));
                        }
                    }
                }
            }
        }

        if (isset($_POST['sort']) && !empty($_POST['sort'])) {
            foreach ($_POST['sort'] as $id => $sort) {
                if($idImg = ImageAR::getImageIdByName($id)) {
                    ImageAR::model()->updateByPk($idImg,['sort' => $sort]);
                }
            }
        }


        if(isset($_POST['identifier']) || isset($_POST['sort'])) {
            $model->updateMainImg();
        }
    }

    public function actionLongTermCompleted($id)
    {
        /**
         * @var $command LotCommand
         */
        Yii::import('console.commands.LotCommand');
        $command = new LotCommand('', '');

        $lot = Yii::app()->db->createCommand()
            ->select('*')
            ->from('auction')
            ->where(
                'status=:status and auction_id=:auction_id',
                array(
                    ':auction_id' => $id,
                    ':status' => Auction::ST_ACTIVE,
                    //':type' => BaseAuction::TYPE_AUCTION
                )
            )
            ->queryRow();


        if ($lot == false) {
            throw new CHttpException(404);
        }

        if ($command->checkExistBets($lot['auction_id'])) {
            if (($winner = $command->getWinnerAuction($lot['current_bid'])) !== false) {
                $command->giveLotWinner($lot, $winner['user_id']);
            }
        } else {
            Yii::app()->db->createCommand()
                ->update(
                    'auction',
                    array(
                        'status' => BaseAuction::ST_COMPLETED_EXPR_DATE,
                        'bidding_date' => date('Y-m-d H:i:s', time()),
                        'current_bid' => 0,
                        'sales_id' => 0
                    ),
                    'auction_id=:auction_id',
                    array(
                        ':auction_id' => (int)$id
                    )
                );
        }

        $this->redirect(array('/auction/view', 'id' => $id));
    }

    public function actionSetAuctionCity($id_city) {
        $city = City::model()->findByPk($id_city);

        Auction::model()->updateAll(array(
            'id_city' => $city->id_city,
            'id_region' => $city->id_region,
            'id_country' => $city->id_country
        ), 'owner=:user_id', array(':user_id' => Yii::app()->user->id));

        echo "ok";
    }

    public function actionSetAutoRepublish($id, $auto_republish) {
        /** @var Auction $auction */
        $auction = Auction::model()->findByPk($id);

        if($auction && $auction->owner == Yii::app()->user->id) {
            $auction->is_auto_republish = $auto_republish ? 1 : 0;
            $auction->update(['is_auto_republish']);

            echo "ok";
        } else {
            echo "not found";
        }
    }

    public function actionMassAutoRepub()
    {
        if (isset($_POST['Auction'])) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition('auction_id', $_POST['Auction']);
            $criteria->compare('owner', Yii::app()->user->id);

            Auction::model()->updateAll(['is_auto_republish' => 1], $criteria);
        }

        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionRemoveTrading($id, $type)
    {
        switch ($type) {
            case 'item':
                $item = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('auction')
                    ->where(
                        'owner=:owner and auction_id=:auction_id',
                        array(
                            ':owner' => Yii::app()->user->id,
                            ':auction_id' => (int)$id
                        )
                    )
                    ->queryRow();
                if ($item != false) {


                    Yii::app()->db->createCommand()
                        ->update(
                            'auction',
                            array(
                                'status' => BaseAuction::ST_COMPLETED_EXPR_DATE,
                                'bidding_date' => date('Y-m-d H:i:s', time()),
                                'current_bid' => 0,
                                'sales_id' => 0,
                                'bid_count' => 0
                            ),
                            'auction_id=:auction_id',
                            array(
                                ':auction_id' => (int)$id
                            )
                        );

                    Yii::app()->db->createCommand()
                        ->delete(
                            'bids',
                            'lot_id=:lot_id',
                            array(
                                ':lot_id' => (int)$id
                            )
                        );

                    if ($item['type'] == BaseAuction::TYPE_AUCTION && $item['current_bid'] != 0) {
                        $user = User::model()->findByPk($item['owner']);
                        $user->rating -= 1;
                        $user->update(array('rating'));
                    }

                    $returnUrl = Yii::app()->request->getParam('returnUrl', null);
                    if (!is_null($returnUrl)) {
                        if ($returnUrl == 'activeItems') {
                            $this->redirect('/user/sales/activeItems');
                        } elseif ($returnUrl == 'myAdverts') {
                            $this->redirect('/user/sales/myAdverts');
                        }
                    } else {
                        $this->redirect(array('/auction/view', 'id' => $id));
                    }
                } else {
                    throw new CHttpException('Not found Item');
                }
                break;
        }
    }


}
