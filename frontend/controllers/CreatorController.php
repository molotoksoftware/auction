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
 * Create item
 */
class CreatorController extends FrontController
{
    public $defaultAction = 'new';

    public function filters()
    {
        return array('accessControl');
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'imageSave', 'dynamicCategoriesForSelect', 'getOptions', 'success', 'lot', 'advert',
                    'getChildValues', 'lots', 'lots_add', 'lots_del', 'workWithLots', 'import', 'avito', 'meshok', 'search_category',
                    'step_crt_lot', 'publishSame'
                ),
                'users' => array('@')
            ),
            array('deny'),
        );
    }

    public function actionLot()
    {
        $this->pageTitle = Yii::t('basic', 'Create item');

        $form = new FormCreateLot('main');

        $sql = <<<SQL
        select a.name, a.attribute_id, a.type, a.mandatory
from attribute a 
left join category_attributes ca on ca.attribute_id=a.attribute_id
where ca.category_id=:id and a.type<>:type
order by ca.sort ASC
SQL;
        $options = Yii::app()->db->createCommand($sql)->queryAll(true, array(
            ':id' => $form->category_id,
            ':type' => Attribute::TYPE_CHILD_ELEMENT
        ));

        $this->performAjaxValidation($form, 'form-create-lot');
        $this->save($form, BaseAuction::TYPE_AUCTION);

        $this->render('lot', [
            'model' => $form,
            'ItemOptions' => $options,
        ]);

    }

    public function save(FormCreateLot &$form, $type)
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();

        $class = get_class($form);

        if ($request->isPostRequest && isset($_POST[$class])) {

            if (Yii::app()->user->isGuest) {
                throw new CException('Error identity user');
            }

            $form->attributes = $_POST[$class];

            if ($form->validate()) {
                if ($type == BaseAuction::TYPE_AUCTION) {
                    $model = new Auction();
                    $model->quantity = $form->quantity;

                } else {
                    throw new CException(sprintf('Unknown type "%s"', $type));
                }

                $model->name = $form->name;
                $model->category_id = $form->category_id;

                if (isset($form->conditions_transfer)) {
                    $model->conditions_transfer = $form->conditions_transfer;
                }
                if (isset($form->contacts)) {
                    $model->contacts = $form->contacts;
                }
                $model->duration = $form->duration;

                //bbcode decode
                $text_item = str_replace("<a href", '<a rel="nofollow" href', $form->description);

                $text_item = preg_replace_callback(
                    '/<a(.*?)>(.*?)<\/a>/',
                    function ($m) {
                        $posit = strripos($m[2], 'img');

                        if ($posit === false) {
                            $num = iconv_strlen($m[2], 'UTF-8');
                            if ($num > 23) {
                                $m[2] = mb_substr($m[2], 0, 23, "UTF-8");
                                return '<a' . $m[1] . '>' . $m[2] . '...</a>';
                            } else {
                                return '<a' . $m[1] . '>' . $m[2] . '</a>';
                            }
                        } else {
                            return '<a' . $m[1] . '>' . $m[2] . '</a>';
                        }
                    },
                    $text_item);

                $model->text = $text_item;

                $model->id_country = $form->id_country;
                $model->id_region = $form->id_region;
                $model->id_city = $form->id_city;

                if (isset($form->is_auto_republish)) {
                    $model->is_auto_republish = $form->is_auto_republish;
                }

                $model->contacts = $form->contacts;

                $model->owner = Yii::app()->user->id;

                $model->type = $type;

                $model->created = date('Y-m-d H:i:s', time());

                $model->type_transaction = $form->type_transaction;
                $model->starting_price = $form->starting_price;

                /* duration date time */
                $date = new DateTime();
                $model->created = $date->format('Y-m-d H:i:s');
                $interval_spec = Auction::getDateSpecForDuration($model->duration);
                $date->add(new DateInterval($interval_spec));
                $model->bidding_date = $date->format('Y-m-d H:i:s');

                $model->price = $form->price;

                if ($model->save()) {
                    $this->imageSave($model);

                    $transaction = $model->dbConnection->beginTransaction();
                    try {
                        if (isset($_POST['options'])) {
                            if (!empty($_POST['options'][0])) {
                                foreach ($_POST['options'][0] as $key => $value) {
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
                                        if (!empty($value)) {
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
    }

    public function imageSave($model)
    {
        if (isset($_POST['identifier'])) {
            Yii::import('backend.extensions.imageUploader.ImageUploaderHelper');
            $images = ImageUploaderHelper::getFilesById($_POST['identifier'], 'frontend');
            if (!empty($images)) {
                $sorted = $_POST['sort'];
                foreach ($images as $img) {

                    $splitFileName = explode('.', basename($img['file']));
                    $imageHash = md5(uniqid() . $model->auction_id . $splitFileName[0]);

                    $imageModel = new ImageAR();
                    $imageModel->item_id = $model->auction_id;
                    $imageModel->image = $imageHash;
                    $imageModel->sort = (isset($sorted[$img['id']])) ? $sorted[$img['id']] : 0;
                    $imageModel->type = 0;
                    $imageModel->save(false);
                    $imageModel->refresh();

                    $file = $splitFileName[0] . "_" . $imageModel->getPrimaryKey() . "." . $splitFileName[1];

                    $imageModel->image = $file;
                    $imageModel->update(['image']);

                    //resize
                    $type = get_class($model);
                    foreach ($type::$versions as $v_name => $v_params) {
                        if ($v_name == 'big') {
                            $imageComp = Getter::imageHandler()->load($img['file']);
                            $args = array_values(array_values($v_params)[0]);
                            $imageComp->thumb($args[0], $args[1]);

                            $imageComp->watermark(Yii::getPathOfAlias('frontend.www.img') . '/watermark.png', 10, 10);

                            $imageComp->save(ImageAR::getImageSavePath($model->owner, true, $v_name . '_' . $file));
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

                $main_img = Yii::app()->db->createCommand()
                    ->select('image')
                    ->from('images')
                    ->order('sort ASC')
                    ->where('item_id=:item_id', array(':item_id' => $model->auction_id))
                    ->limit(1)
                    ->queryColumn();

                if ($main_img) {
                    Yii::app()->db->createCommand()
                        ->update(
                            'auction',
                            array(
                                'image' => $main_img[0]
                            ),
                            'auction_id=:auction_id',
                            array(
                                ':auction_id' => $model->auction_id
                            )
                        );

                }
            }
        }


    }

    public function actionSuccess()
    {
        $this->render('success');
    }

    /**
     * return subcategories Ids
     *
     */
    public function actionDynamicCategoriesForSelect($cat_id, $where_show = 0)
    {

        $categories = Category::getCategoriesForSelect($cat_id, $where_show);

        if (count($categories) > 0) {
            $htmlOptions = array(
                'empty' => Yii::t('basic', '- select category -')
            );
            RAjax::data(
                array(
                    'isSubCategories' => true,
                    'options' => CHtml::listOptions('', $categories, $htmlOptions)
                )
            );
        } else {
            RAjax::data(
                array(
                    'isSubCategories' => false,
                    'options' => ''
                )
            );
        }
    }

    public function actionGetOptions($cat_id)
    {

        $sql = <<<SQL
        select a.name, a.attribute_id, a.type, a.mandatory
from attribute a 
left join category_attributes ca on ca.attribute_id=a.attribute_id
where ca.category_id=:id and a.type<>:type
order by ca.sort ASC
SQL;

        $options = Yii::app()->db->createCommand($sql)->queryAll(true, array(
            ':id' => $cat_id,
            ':type' => Attribute::TYPE_CHILD_ELEMENT
        ));

        RAjax::data(
            array(
                'isOptions' => (empty($options)) ? false : true,
                'options' => $this->renderPartial('_options', array('options' => $options), true)
            )
        );
    }

    public function actionWorkWithLots()
    {
        if (Yii::app()->request->isAjaxRequest && !Yii::app()->user->isGuest) {
            $select_action = $_GET['select_action'];
            $check_lots = $_GET['check_lots'];

            switch ($select_action) {
                case 1: // publish items
                    if (!empty($check_lots)) {
                        foreach ($check_lots as $lot) {
                            if ($lot != 0) {
                                $model = Auction::model()->findByPk($lot);

                                if (isset($model->auction_id) && !empty($model->auction_id) && $model->owner == Yii::app()->user->id) {
                                    $model->update();
                                }
                            }
                        }
                    }
                    break;
                case 2: // remove items
                    if (!empty($check_lots)) {
                        foreach ($check_lots as $lot) {
                            if ($lot != 0) {
                                $model = Auction::model()->findByPk($lot);

                                if (isset($model->auction_id) && !empty($model->auction_id) && $model->owner == Yii::app()->user->id) {
                                    Auction::model()->deleteByPk($lot);
                                    Yii::app()->db->createCommand()
                                        ->delete(
                                            'images',
                                            'item_id=:item_id',
                                            array(
                                                ':item_id' => $lot
                                            )
                                        );
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    public function actionPublishSame($id)
    {
        /** @var Auction $auction */
        $auction = Auction::model()->findByPk($id);

        if (!$auction) throw new CHttpException(404, 'Item not found');

        if ($auction->owner == Yii::app()->user->id) {
            $new_auction = new Auction;
            $new_auction->attributes = $auction->attributes;
            $new_auction->bid_count = 0;
            $new_auction->current_bid = 0;
            $new_auction->viewed = 0;
            $new_auction->sales_id = 0;
            $new_auction->status = 7;
            $new_auction->bidding_date = $auction->bidding_date;
            $new_auction->type = $auction->type;

            if ($new_auction->price == $new_auction->starting_price) {
                $new_auction->price += 1;
            }

            if ($new_auction->save()) {
                ImageAR::copyImages($auction, $new_auction, ImageAR::TYPE_AUCTION);
                Yii::app()->db->createCommand('INSERT INTO auction_attribute_value(auction_id, attribute_id, value_id, value) SELECT ' . $new_auction->auction_id . ' AS auction_id, attribute_id, value_id, value FROM auction_attribute_value WHERE auction_id = ' . $auction->auction_id)->execute();

                $this->redirect(array('editor/lot', 'id' => $new_auction->auction_id));
            } else {
                Yii::log(sprintf(
                    'Save item, errors: %s, attrs: %s',
                    CVarDumper::dumpAsString($new_auction->errors),
                    CVarDumper::dumpAsString($new_auction->attributes)
                ), CLogger::LEVEL_ERROR);

                throw new CHttpException(500, 'Save item error');
            }
        } else {
            throw new CHttpException(403, 'You isn\'t item\'s owner');
        }
    }
}