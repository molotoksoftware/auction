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


class CategoryController extends BackController
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
                    'moveCopy',
                    'create',
                    'fetchTree',
                    'view',
                    'toggle',
                    'sortable'
                ),
                'roles' => array('admin', 'root'),
            ),
            array('deny'),
        );
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            array(
                'jsTreeBehavior' => array(
                    'class' => 'backend.extensions.jsTree.JsTreeBehavior',
                    'modelClassName' => 'Category',
                    'form_alias_path' => 'application.views.category._form',
                    'view_alias_path' => 'application.views.category.view',
                    'label_property' => 'name',
                    'rel_property' => 'name'
                )
            )
        );
    }

    public function actionToggle($id, $attribute)
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(400, 'Некорректный запрос');
        if (!in_array($attribute, array('type')))
            throw new CHttpException(400, 'Некорректный запрос');

        $model = $this->_loadModel($id);
        $model->$attribute = $model->$attribute ? 0 : 1;
        $model->save();

        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionIndex()
    {
        $categories = new Category('search');
        $categories->unsetAttributes();

        if (isset($_GET['Category'])) {
            $categories->attributes = $_GET['Category'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial(
                '_table_categories',
                array(
                    'model' => $categories,
                )
            );
        } else {
            $this->render(
                'index',
                array(
                    'model' => $categories,
                )
            );
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel('Category', $id, array('with' => array('favourites_attribute')));
        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-category');

        if (Yii::app()->request->isPostRequest && isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];

            if ($model->saveNode()) {
                //options ------------------------------------------------------
                //delete
                CategoryAttributes::model()->deleteAll(
                    'category_id=:category_id',
                    array(':category_id' => $model->category_id)
                );

                if (!empty($_POST['options'])) {

                    //update option for category        
                    foreach ($_POST['options'] as $key => $item) {
                        $ca = new CategoryAttributes();
                        $ca->attribute_id = $item;
                        $ca->category_id = $model->category_id;
                        $ca->sort = $key;
                        $ca->save();
                    }
                }

                if ($model->applyToChild && !empty($_POST['options'])) {
                    $child = $model->descendants()->findAll();

                    foreach ($child as $ch) {
                        CategoryAttributes::model()->deleteAll('category_id='.$ch->category_id);
                        foreach ($_POST['options'] as $key => $item) {
                            CategoryAttributes::addAttributesToCategory($ch->category_id, $key, $item);
                        }
                    }
                }

                Yii::app()->user->setFlash('success', 'Категория успешно сохранена');
                $this->redirect(array('/catalog/category/index'));
            } else {
                die("Erorr save");
            }
        }

        $this->render('update', array('model' => $model));
    }

    public function actionCreate()
    {
        $model = new Category('insert');
        $this->performAjaxValidation($model, 'form-category');

        if (isset($_POST['Category'])) {
            $model->attributes = $_POST['Category'];

            $parent_id = (empty($_POST['Category']['parent_id'])) ? 1 : $_POST['Category']['parent_id'];
            $parent = Category::model()->findByPk($parent_id);
            if (is_null($parent)) {
                throw new CException('not found root category');
            }


            if ($model->appendTo($parent)) {

                //update option for category
                if (!empty($_POST['options'])) {
                    foreach ($_POST['options'] as $key => $item) {
                        CategoryAttributes::addAttributesToCategory($model->category_id, $key, $item);
                    }
                }

                Yii::app()->user->setFlash('success', 'Категория успешно создана');
                if ($_POST['submit'] == 'index') {
                    $this->redirect(array('/catalog/category/index'));
                } else {
                    $this->refresh();
                }
            }
        }

        $this->render('create', array('model' => $model));
    }



    public function actionDelete($id)
    {
        $deleted = $this->loadModel('Category', $id)->deleteNode();
        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошыбка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Категория успешно удалена'));
        }
    }

    public function actionMultipleRemove()
    {
        if (!isset($_POST['data'])) {
            RAjax::error(array('messages' => 'error'));
        }
        $removes = CJSON::decode($_POST['data']);
        if (is_array($removes) && count($removes) > 0) {
            try {
                foreach ($removes as $item) {
                    $c = Category::model()->findByPk((int)$item);
                    if (!is_null($c)) {
                        $c->deleteNode();
                    }
                }
            } catch (Exception $e) {
                RAjax::error(array('messages' => 'Error'));
            }
        }
        RAjax::success(array('messages' => "Выбрание элементы успешно удалены"));
    }

}
