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


class AttributeController extends BackController
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
                    'sortable',
                    'getChildValues',
                    'toggle'
                ),
                'roles' => array('admin', 'root'),
            ),
            array('deny'),
        );
    }
    
    public function actionToggle($id, $attribute)
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(400, 'Некорректный запрос');
        if (!in_array($attribute, array('mandatory')))
            throw new CHttpException(400, 'Некорректный запрос');

        $model = $this->_loadModel($id);
        $model->$attribute = $model->$attribute ? 0 : 1;
        $model->save();

        if (!Yii::app()->request->isAjaxRequest)
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionIndex()
    {
        $attributes = new Attribute('search');
        $attributes->unsetAttributes();

        if (isset($_GET['Attribute'])) {
            $attributes->attributes = $_GET['Attribute'];
        }

        if (isset($_GET['ajax'])) {
            $this->renderPartial(
                '_table_attributes',
                array(
                    'model' => $attributes,
                )
            );
        } else {
            $this->render(
                'index',
                array(
                    'model' => $attributes,
                )
            );
        }
    }


    /**
     * @param $parentId int|null
     * @param $values array
     * @param $attrId int
     * удалят атрибуты из базы которые были удалены при редактировании
     */
    protected function  deleteDiffValues($values, $attrId, $parentId = null)
    {
        $tmp = array();
        foreach ($values as $indexValue => $value) {
            if (is_int($indexValue)) {
                $tmp[] = $indexValue;
            }
        }
        if (!empty($tmp)) {
            $oll_sql = Yii::app()->db->createCommand()
                ->select('value_id')
                ->from('attribute_values')
                ->where('attribute_id=:attribute_id', array(':attribute_id' => $attrId));

            if (!is_null($parentId)) {
                $oll_sql->andWhere('parent_id=:parent_id', array(':parent_id' => $parentId));
            }

            $oll_av = $oll_sql->queryColumn();


            $deletes = array_diff($oll_av, $tmp);

            if (!empty($deletes)) {
                foreach ($deletes as $i) {
                    $av = AttributeValues::model()->findByPk($i);
                    if (!is_null($av)) {
                        $av->delete();
                    }
                }
            }
        }

    }


    protected function updateDependentValues($values, $attributeId)
    {
        foreach ($values as $attributeIndex => $values) {
            $sort = 0;
            foreach ($values as $valueIndex => $v) {
                if (is_int($valueIndex)) {
                    $ca = AttributeValues::model()
                        ->find(
                            'value_id=:value_id and attribute_id=:attribute_id',
                            array(
                                ':attribute_id' => $attributeIndex,
                                ':value_id' => $valueIndex,
                            )
                        );
                    if (!is_null($ca)) {
                        $ca->value = $v;
                        $ca->sort = $sort;
                        $ca->update(array('sort', 'value'));
                    } else {
                        $at_value = new AttributeValues();
                        $at_value->attribute_id = $attributeIndex;
                        $at_value->value = $v;
                        $at_value->sort = $sort;
                        $at_value->save();
                    }

                } else {
                    $at_value = new AttributeValues();
                    $at_value->attribute_id = $attributeIndex;
                    $at_value->value = $v;
                    $at_value->parent_id = $parent->value_id;
                    $at_value->sort = $sort;
                    $at_value->save();
                }
                $sort++;
            }
        }
    }

    public function updateRootValue($rootAttrId, $id, $value, $sort)
    {
        $av = AttributeValues::model()
            ->find(
                'value_id=:value_id and attribute_id=:attribute_id',
                array(
                    ':attribute_id' => $rootAttrId,
                    ':value_id' => $id,
                )
            );

        if (!is_null($av)) {
            $av->value = $value;
            $av->sort = $sort;
            $av->update(array('sort', 'value'));
        }
    }

    public function updateChildValues($childAttrId, $rootAttributeId, $idValue)
    {
        if (isset($_POST['values']['update']['dep'][$childAttrId][$idValue]) && !empty($_POST['values']['update']['dep'][$childAttrId][$idValue])) {
            $childs = $_POST['values']['update']['dep'][$childAttrId][$idValue];
            $this->deleteDiffValues($childs, $childAttrId, $idValue);
            $sort = 0;

            foreach ($childs as $childIndex => $childValue) {
                if (is_int($childIndex)) {
                    $av = AttributeValues::model()
                        ->find(
                            'value_id=:value_id and attribute_id=:attribute_id',
                            array(
                                ':attribute_id' => $childAttrId,
                                ':value_id' => $childIndex,
                            )
                        );
                    $av->value = $childValue;
                    $av->sort = $sort;
                    $av->update(array('sort', 'value'));
                } else {
                    $at_value = new AttributeValues();
                    $at_value->attribute_id = $childAttrId;
                    $at_value->value = $childValue;
                    $at_value->parent_id = $idValue;
                    $at_value->sort = $sort;
                    $at_value->save();
                }
                $sort++;
            }

        } else {
            $av = AttributeValues::model()->findAll(
                'attribute_id=:attribute_id and parent_id=:parent_id',
                array(
                    ':attribute_id' => $childAttrId,
                    ':parent_id' => $idValue
                )
            );
            if (!empty($av)) {
                foreach ($av as $av_item) {
                    $av_item->delete();
                }
            }
        }

    }




    protected function updateDependet($rootAttributeId, $childAttributeId)
    {
        if (isset($_POST['values']['update']) && !empty($_POST['values']['update'])) {

            /**
             * ROOT
             */
            if (isset($_POST['values']['update']['root'][$rootAttributeId]) && !empty($_POST['values']['update']['root'][$rootAttributeId])) {
                $roots = $_POST['values']['update']['root'][$rootAttributeId];

                //oll remove
                if (count($_POST['values']['update']['root'][$rootAttributeId]) == 1) {
                    $index = key($_POST['values']['update']['root'][$rootAttributeId]);
                    if (empty($_POST['values']['update']['root'][$rootAttributeId][$index])) {
                        $av = AttributeValues::model()->findAll(
                            'attribute_id=:attribute_id',
                            array(':attribute_id' => $rootAttributeId)
                        );
                        foreach ($av as $item) {
                            $item->delete();
                        }
                    }
                }


                //delete roots
                $this->deleteDiffValues($roots, $rootAttributeId, null);
                $sort = 0;
                foreach ($roots as $index => $value) {
                    if (is_int($index)) {
                        //update
                        $this->updateRootValue($rootAttributeId, $index, $value, $sort);
                        $this->updateChildValues($childAttributeId, $rootAttributeId, $index);
                    } else {
                        //create Root
                        $at_value = new AttributeValues();
                        $at_value->attribute_id = $rootAttributeId;
                        $at_value->value = $value;
                        $at_value->parent_id = 0;
                        $at_value->sort = $sort;
                        if ($at_value->save()) {
                            //is child
                            if (isset($_POST['values']['update']['dep'][$childAttributeId][$index]) &&
                                !empty($_POST['values']['update']['dep'][$childAttributeId][$index])
                            ) {
                                $childs = $_POST['values']['update']['dep'][$childAttributeId][$index];
                                $c_sort = 0;
                                foreach ($childs as $c_v) {
                                    $atch_value = new AttributeValues();
                                    $atch_value->attribute_id = $childAttributeId;
                                    $atch_value->value = $c_v;
                                    $atch_value->parent_id = $at_value->value_id;
                                    $atch_value->sort = $c_sort;
                                    $atch_value->save();

                                    $c_sort++;
                                }
                            }
                        }

                    }
                    $sort++;
                }
            }
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel('Attribute', $id, array('with' => 'values'));
        $model->scenario = "update";
        $this->performAjaxValidation($model, 'form-attribute');

        if (isset($_POST['Attribute'])) {

            $model->attributes = $_POST['Attribute'];
            if ($model->save()) {


                if ($model->type == Attribute::TYPE_DEPENDET_SELECT) {
                    $this->updateDependet($model->attribute_id, $model->child_id);

                } elseif ($model->type != Attribute::TYPE_TEXT && $model->type != Attribute::TYPE_TEXT_AREA) {

                    if (isset($_POST['values']) && !empty($_POST['values'])) {
                        //delete
                        $oll_av = Yii::app()->db->createCommand()
                            ->select('value_id')
                            ->from('attribute_values')
                            ->where(
                                'attribute_id=:attribute_id',
                                array(
                                    ':attribute_id' => $model->attribute_id,
                                )
                            )
                            ->queryColumn();

                        $keys = array();
                        foreach ($_POST['values'] as $value) {
                            $keys[] = key($value);
                        }

                        $deletes = array_diff($oll_av, $keys);
                        Yii::app()->db->createCommand()
                            ->delete('attribute_values', array('in', 'value_id', $deletes));


                        //update option for values      
                        foreach ($_POST['values'] as $key => $item) {
                            $ca = AttributeValues::model()
                                ->find(
                                    'value_id=:value_id and attribute_id=:attribute_id',
                                    array(
                                        ':attribute_id' => $model->attribute_id,
                                        ':value_id' => key($item),
                                    )
                                );

                            if (!is_null($ca)) {
                                $ca->value = $item[key($item)];
                                $ca->sort = $key;
                                $ca->update(array('sort', 'value'));
                            } else {
                                $at_value = new AttributeValues();
                                $at_value->attribute_id = $model->attribute_id;
                                $at_value->value = $item[key($item)];
                                $at_value->sort = $key;
                                $at_value->save();
                            }
                        }
                    }
                } else {
                    AttributeValues::model()->deleteAll(
                        'attribute_id=:attribute_id',
                        array(':attribute_id' => $model->attribute_id)
                    );
                }

                Yii::app()->user->setFlash('success', 'Атрибут успешно сохранен');
                $this->redirect(array('/catalog/attribute/index'));
            }
        }

        if ($model->type == Attribute::TYPE_DEPENDET_SELECT) {
            $this->render(
                'updateDependet',
                array(
                    'model' => $model,
                    'values' => $model->values
                )
            );
        } else {
            $this->render(
                'update',
                array(
                    'model' => $model,
                    'values' => $model->values
                )
            );
        }


    }


    public function actionCreate($type = 'common')
    {
        $model = new Attribute('insert');
        $this->performAjaxValidation($model, 'form-attribute');

        if (isset($_POST['Attribute'])) {
            $model->attributes = $_POST['Attribute'];


            if ($model->save()) {

                if ($type == 'common') {

                    //save attribute values
                    if ($model->type != Attribute::TYPE_TEXT && $model->type != Attribute::TYPE_TEXT_AREA) {

                        if (isset($_POST['values']) && !empty($_POST['values'])) {
                            foreach ($_POST['values'] as $key => $value) {
                                if (!empty($value)) {
                                    $at_value = new AttributeValues();
                                    $at_value->attribute_id = $model->attribute_id;
                                    $at_value->value = $value;
                                    $at_value->sort = $key;
                                    $at_value->save();
                                }
                            }
                        }
                    }
                } elseif ($type == 'dependet') {
                    if (isset($_POST['values']) && !empty($_POST['values'])) {


                        foreach ($_POST['values'] as $key => $value) {

                            if (!empty($value[0])) {
                                //save root
                                if (!empty($value[0]['root'][0])) {
                                    $at_value = new AttributeValues();
                                    $at_value->attribute_id = $model->attribute_id;
                                    $at_value->value = $value[0]['root'][0];
                                    $at_value->sort = $key;

                                    if ($at_value->save()) {

                                        if (!empty($value[0]['dep'])) {

                                            $dependents = explode("\n", $value[0]['dep'][0]);

                                            if(count($dependents)>0) {

                                                foreach ($dependents as $i => $v) {
                                                    $dep_value = new AttributeValues();
                                                    $dep_value->attribute_id = $model->child_id;
                                                    $dep_value->parent_id = $at_value->value_id;
                                                    $dep_value->value = $v;
                                                    $dep_value->sort = $i;
                                                    $dep_value->save();
                                                }

                                            }


                                        }

                                    } else {
                                        continue;
                                    }

                                } else {
                                    continue;
                                }
                            }
                        }
                        //end foreach
                    }


                }

                Yii::app()->user->setFlash('success', 'Атрибут успешно создан');
                if ($_POST['submit'] == 'index') {
                    $this->redirect(array('/catalog/attribute/index'));
                } else {
                    $this->refresh();
                }
            }
        }


        if ($type == 'common') {
            $this->render('create', array('model' => $model));
        } elseif ($type == 'dependet') {
            $this->render('createDependet', array('model' => $model));
        }

    }

    public function actionMultipleRemove()
    {
        parent::multipleRemove('Attribute');
    }

    public function actionDelete($id)
    {
        $deleted = $this->loadModel('Attribute', $id)->delete();
        if ($deleted == 0) {
            RAjax::error(array('messages' => 'Ошыбка при удалении'));
        } else {
            RAjax::success(array('messages' => 'Атрибут успешно удален'));
        }
    }

    public function actionGetChildValues($id)
    {
        if(empty($id)) {
            $data = array();
            RAjax::data(
                array(
                    'options' => '<option value="">- выберите значения -</option>'
                )
            );
        }

        $values = AttributeValues::model()->findAll(
            'parent_id=:parent_id',
            array(
                ':parent_id' => $id
            )
        );

        if (count($values) > 0) {
            $htmlOptions = array(
                'empty' => '- выберите значения  -'
            );
            $rawData = CHtml::listData($values, 'value_id', 'value');

            RAjax::data(
                array(
                    'options' => CHtml::listOptions('', $rawData, $htmlOptions)
                )
            );
        } else {
            RAjax::data(
                array(
                    'options' => ''
                )
            );
        }

    }
    
    protected function _loadModel($id)
    {
        if (!$model = Attribute::model()->findByPk($id)) {
            if (Yii::app()->request->isAjaxRequest) {
                RAjax::error(array('messages' => 'Атрибут не существует'));
            } else {
                throw new CHttpException(404, 'Атрибут не существует');
            }
        }
        return $model;
    }
}