<?php


/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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


class AuctionController extends BackController
{

    public $defaultAction = 'index';

    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + delete, MultipleRemove'
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                    'MultipleRemove',
                    'delete',
                    'update',
                    'create',
                    'view',
                    'getOptions',
                    'dynamicCategoriesForSelect',
                    'createAdvert',
                    'createLot',
                    'sortable',
                    'removeBid'
                ),
                'roles' => array('admin', 'root'),
            ),
            array('deny'),
        );
    }

    public function actionIndex()
    {
        $auctions = new BaseAuction('search');
        $auctions->unsetAttributes();

        if (isset($_GET['BaseAuction'])) {
            $auctions->attributes = $_GET['BaseAuction'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial(
                '_table_auctions',
                array(
                    'model' => $auctions,
                )
            );
        } else {
            $this->render(
                'index',
                array(
                    'model' => $auctions,
                )
            );
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel('BaseAuction', $id);
        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-auction');
        $this->save($model);

        $sql = <<<SQL
        select
            a.name,
            a.attribute_id,
            acv.auction_id,
            a.type,
            acv.value,
            value_id
        from attribute a 
        left join category_attributes ca on ca.attribute_id=a.attribute_id
        left join auction_attribute_value acv on acv.attribute_id=a.attribute_id and acv.auction_id=:auction_id
        where ca.category_id=:id
        group by a.attribute_id
        order by ca.sort ASC
        
SQL;


        $options = Yii::app()->db->createCommand($sql)->queryAll(
            true,
            array(
                ':id' => $model->category_id,
                ':auction_id' => $id
            )
        );


        if ($model->type == BaseAuction::TYPE_AUCTION) {

            //-- options
            $this->render(
                'update_lot',
                array(
                    'model' => $model,
                    'options' => $options,
                )
            );
        } else {
            throw new CException('Неизвестный тип');
        }
    }


    public function setEndingDate(&$model, $refresh)
    {
        if (!$model->isNewRecord && $refresh == false) {
            return;
        }

        $date = new DateTime();
        $model->created = $date->format('Y-m-d H:i:s');
        $interval_spec = Auction::getDateSpecForDuration($model->duration);
        $date->add(new DateInterval($interval_spec));
        $model->bidding_date = $date->format('Y-m-d H:i:s');
        $model->status = BaseAuction::ST_ACTIVE;

        /* remove tables */

        //bids
        Yii::app()->db->createCommand()
            ->delete(
                'bids',
                'lot_id=:lot_id',
                array(
                    ':lot_id' => (int)$model->auction_id
                )
            );


    }

    public function save($model)
    {
        $type = get_class($model);

        if (Yii::app()->request->isPostRequest && isset($_POST[$type])) {

            $model->name = $_POST[$type]['name'];
            $model->text = $_POST[$type]['text'];
            $model->status = $_POST[$type]['status'];
            if ($model->status == 4) $model->bidding_date = date('Y-m-d H:i:s', time());
            $model->category_id = $_POST[$type]['category_id'];
            if (isset($_POST[$type]['conditions_transfer'])) {
                $model->conditions_transfer = $_POST[$type]['conditions_transfer'];
            }
            if (isset($_POST[$type]['contacts'])) {
                $model->contacts = $_POST[$type]['contacts'];
            }

            $refresh = (isset($_POST['refresh'])) ? (boolean)$_POST['refresh'] : false;
            
            if ($refresh === true) {$model->viewed = 0;}

            $model->duration = $_POST[$type]['duration'];

            if ($type == 'Auction') {
                $model->type = BaseAuction::TYPE_AUCTION;
                $model->quantity = $_POST[$type]['quantity'];


                /* duration date time */
                $this->setEndingDate($model, $refresh);

                $model->type_transaction = $_POST[$type]['type_transaction'];
                $model->starting_price = $_POST[$type]['starting_price'];
            } 


            $model->price = $_POST[$type]['price'];

            if (isset($_POST['Longitude'])) {
                $model->longitude = str_replace(',', '.', $_POST['Longitude']);
            }

            if (isset($_POST['Latitude'])) {
                $model->latitude = str_replace(',', '.', $_POST['Latitude']);
            }

            $model->owner = $_POST[$type]['owner'];

            if ($model->save()) {



                /** ---------- IMAGES ------------------* */
                if (isset($_POST['identifier'])) {
                    $save_path = Yii::getPathOfAlias('frontend') . '/www/i/';

                    if (!is_dir($save_path . 'thumbs')) {
                        if ((@mkdir($save_path . 'thumbs')) == false) {
                            throw new CException('отсутствует каталог Thumbs');
                        }
                    }

                    Yii::import('backend.extensions.imageUploader.ImageUploaderHelper');
                    $images = ImageUploaderHelper::getFilesById($_POST['identifier'], 'backend');
                    if (!empty($images)) {
                        $sorted = $_POST['sort'];
                        foreach ($images as $img) {
                            $file = basename($img['file']);
                            copy($img['file'], realpath($save_path) . DIRECTORY_SEPARATOR . $file);

                            Yii::app()->db->createCommand()
                                ->insert(
                                    'images',
                                    array(
                                        'item_id' => $model->auction_id,
                                        'image' => $file,
                                        'sort' => (isset($sorted[$img['id']])) ? $sorted[$img['id']] : 0
                                    )
                                );

                            //resize
                            $org_image = Yii::app()->image->load($img['file']);
                            $type = get_class($model);
                            foreach ($type::$versions as $v_name => $v_params) {
                                $method = key($v_params);
                                $args = array_values($v_params);
                                call_user_func_array(
                                    array($org_image, $method),
                                    is_array($args[0]) ? $args[0] : array($args[0])
                                );

                                if ($v_name == 'large' || $v_name == 'big') {
                                    $org_image->watermark(
                                        Yii::getPathOfAlias('frontend.www.img') . '/watermark.png',
                                        10,
                                        10
                                    );
                                }

                                $org_image->save($save_path . 'thumbs' . DIRECTORY_SEPARATOR . $v_name . '_' . $file);
                            }
                        }
                    }
                }
                ////////////////////////////////////////////////////////////////
                //update sort
                if (isset($_POST['sort']) && !empty($_POST['sort'])) {
                    foreach ($_POST['sort'] as $id => $sort) {
                        Yii::app()->db->createCommand()
                            ->update('images', array('sort' => $sort), 'image=:image', array(':image' => $id));
                    }
                }

                //cache main image
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


                $transaction = $model->dbConnection->beginTransaction();
                try {

                    //сохраняем параметры
                    if (isset($_POST['options'])) {
                        //удаляем текущие атрибуты
                        if (!$model->isNewRecord) {
                            Yii::app()->db->createCommand()
                                ->delete(
                                    'auction_attribute_value',
                                    'auction_id=:auction_id',
                                    array(':auction_id' => $model->auction_id)
                                );
                        }

                        //сохранить выбранные значения
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

                        //сохранить текстовые значения
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

                // Обновляем счетчик изображений
                $img_count = Yii::app()->db->createCommand()->select('COUNT(*)')->from('images')
				    ->where('item_id=:item_id', array(':item_id' => $model->auction_id))->queryScalar();
                Yii::app()->db->createCommand()
				    ->update('auction', array('image_count' => $img_count), 'auction_id=:auction_id', array(':auction_id' => $model->auction_id));


                //save
                Yii::app()->user->setFlash('success', 'Лот успешно сохранен');
                if ($_POST['submit'] == 'index') {
                    $this->redirect(array('/catalog/auction/index'));
                } else {
                    $this->refresh();
                }
            }
        }
    }

    /**
     * возвращает подкатегории выбраной категории для select
     * @param type $cat_id
     */
    public function actionDynamicCategoriesForSelect($cat_id, $where_show = 0)
    {

        $categories = Category::getCategoriesForSelect($cat_id, $where_show);

        if (count($categories) > 0) {
            $htmlOptions = array(
                'empty' => '- выберите категорию -'
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
        select a.name,
            a.attribute_id,
            a.type
        from attribute a 
        left join category_attributes ca on ca.attribute_id=a.attribute_id
        where ca.category_id=:id
        order by ca.sort ASC
SQL;

        $options = Yii::app()->db->createCommand($sql)->queryAll(true, array(':id' => $cat_id));
        RAjax::data(
            array(
                'isOptions' => (empty($options)) ? false : true,
                'options' => $this->renderPartial(
                    '_options',
                    array(
                        'options' => $options
                    ),
                    true
                )
            )
        );
    }

    public function actionDelete($id)
    {
        $deleted = $this->loadModel('Auction', $id)->delete();
        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошыбка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Лот успешно удален'));
        }
    }

    public function actionMultipleRemove()
    {
        $this->multipleRemove('Auction');
    }

    public function  actionRemoveBid($id)
    {
        if (Bid::remove($id)) {
            RAjax::success(array('messages' => 'Успешно удалено'));
        } else {
            RAjax::error(array('messages' => 'Ошыбка при удалении'));
        }
    }

}
