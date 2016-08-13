<?php

/**
 * JsTreeBehavior class file.
 *
 * Date: 1/29/13
 * Time: 12:00 PM
 *
 * @author: Spiros Kabasakalis <kabasakalis@gmail.com>
 * @link http://iws.kabasakalis.gr/
 * @link http://www.reverbnation.com/spiroskabasakalis
 * @copyright Copyright &copy; Spiros Kabasakalis 2013
 * @license http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @version 1.0.0
 */
class JsTreeBehavior extends CBehavior
{

    /**
     * @var string the model class name
     */
    public $modelClassName;

    /**
     * @var string the partial form view alias path (example:application.views.controllerId._form) used for creating and updating the model.
     */
    public $form_alias_path;

    /**
     * @var string the partial details view alias path (example:application.views.controllerId._view) used for viewing model details.
     */
    public $view_alias_path;

    /**
     * @var string the model property that will be used as the jstree  node label.
     */
    public $label_property = 'name';

    /**
     * @var string the model property that will be populate the rel attribute of the jstree anchor tag.
     */
    public $rel_property = 'name';

    /**
     *  Renames the node,meaning updates the model property used as label.
     */
    public function actionRename()
    {
        $new_name = $_POST['new_name'];
        $id = $_POST['id'];
        $renamed_cat = $this->loadModel($id);
        $renamed_cat->setAttribute($this->label_property, $new_name);
        if ($renamed_cat->saveNode()) {
            echo json_encode(array('success' => true));
            exit;
        } else {
            echo json_encode(array('success' => false));
            exit;
        }
    }

    /**
     *  Deletes the node.
     */
    public function actionRemove()
    {
        $id = $_POST['id'];
        $deleted_cat = $this->loadModel($id);
        if ($deleted_cat->deleteNode()) {
            echo json_encode(array('success' => true));
            exit;
        } else {
            echo json_encode(array('success' => false));
            exit;
        }
    }

    /**
     *  Creates a new root node
     */
    public function actionCreateRoot()
    {
        if (isset($_POST[$this->modelClassName])) {

            $new_root = new $this->modelClassName;
            $new_root->attributes = $_POST[$this->modelClassName];
            if ($new_root->saveNode(false)) {
                echo json_encode(array('success' => true,
                    'id' => $new_root->primaryKey)
                );
                exit;
            } else {
                echo json_encode(array('success' => false,
                    'message' => 'Error.Root ' . $this->modelClassName . ' was not created.'
                        )
                );
                exit;
            }
        }
    }

    /**
     *  Creates a new node
     */
    public function actionCreateNode()
    {
        if (isset($_POST[$this->modelClassName])) {
            $model = new $this->modelClassName;
            $model->attributes = $_POST[$this->modelClassName];
            $parent = $this->loadModel($_POST['parent_id']);
            if ($model->appendTo($parent)) {
                echo json_encode(array('success' => true,
                    'id' => $model->primaryKey)
                );
                exit;
            } else {
                echo json_encode(array('success' => false,
                    'message' => 'Error.' . $this->modelClassName . ' was not created.'
                        )
                );
                exit;
            }
        }
    }

    /**
     *  Renders details view in fancybox popup,must provide $view_alias_path  variable
     */
    public function actionReturnView()
    {
        $this->excludeScripts();
        $model = $this->loadModel($_POST['id']);
        $this->owner->renderPartial($this->view_alias_path, array(
            'model' => $model,
                ), false, true);
    }

    /**
     *  Renders form in fancybox popup to create or update a node.
     */
    public function actionReturnForm()
    {
        $this->excludeScripts();
        //Figure out if we are updating a Model or creating a new one.
        if (isset($_POST['update_id']))
            $model = $this->loadModel($_POST['update_id']);
        else
            $model = new $this->modelClassName;
        $this->owner->renderPartial($this->form_alias_path, array(
            'model' => $model,
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : '',
            'modelClassName' => $this->modelClassName
                ), false, true);
    }

    /**
     *  Updates a node.
     */
    public function actionUpdateNode()
    {
        if (isset($_POST[$this->modelClassName])) {
            $model = $this->loadModel($_POST['update_id']);
            $model->attributes = $_POST[$this->modelClassName];
            if ($model->saveNode(false)) {
                echo json_encode(array('success' => true));
            } else
                echo json_encode(array('success' => false));
        }
    }

    /**
     * Moves or makes a copy of  a node.
     */
    public function actionMoveCopy()
    {

        $moved_node_id = $_POST['moved_node'];
        $new_parent_id = $_POST['new_parent'];
        $new_parent_root_id = $_POST['new_parent_root'];
        $previous_node_id = $_POST['previous_node'];
        $next_node_id = $_POST['next_node'];
        $copy = $_POST['copy'];

        //the following is additional info about the operation provided by
        // the jstree.It's there if you need it.See  jstree documentation .
        //$old_parent_id=$_POST['old_parent'];
        //$pos=$_POST['pos'];
        // $copied_node_id=$_POST['copied_node'];
        $replaced_node_id = $_POST['replaced_node'];

        //the  moved,copied  node
        $moved_node = $this->loadModel($moved_node_id);

        //if we are not moving as a new root...
        if ($new_parent_root_id != 'root') {
            //the new parent node
            $new_parent = $this->loadModel($new_parent_id);
            //the previous sibling node (after the move).
            if ($previous_node_id != 'false')
                $previous_node = $this->loadModel($previous_node_id);

            //if we move
            if ($copy == 'false') {
                //if the moved node is only child of new parent node
                if ($previous_node_id == 'false' && $next_node_id == 'false') {
                    if ($moved_node->moveAsFirst($new_parent)) {
                        echo json_encode(array('success' => true));
                        exit;
                    }
                } //if we moved it in the first position
                else if ($previous_node_id == 'false' && $next_node_id != 'false') {
                    if ($moved_node->moveAsFirst($new_parent)) {
                        echo json_encode(array('success' => true));
                        exit;
                    }
                } //if we moved it in the last position
                else if ($previous_node_id != 'false' && $next_node_id == 'false') {
                    if ($moved_node->moveAsLast($new_parent)) {
                        echo json_encode(array('success' => true));
                        exit;
                    }
                } //if the moved node is somewhere in the middle
                else if ($previous_node_id != 'false' && $next_node_id != 'false') {
                    if ($moved_node->moveAfter($previous_node)) {
                        echo json_encode(array('success' => true));
                        exit;
                    }
                }
            } //end of it's a move
            //else if it is a copy
            else {
                //create the copied Categorydemo model
                $copied_node = new $this->modelClassName;
                //copy the attributes (only safe attributes will be copied).
                $copied_node->attributes = $moved_node->attributes;
                //remove the primary key
                $copied_node->primaryKey = null;
                if ($copied_node->appendTo($new_parent)) {
                    echo json_encode(array('success' => true,
                        'id' => $copied_node->primaryKey
                            )
                    );
                    exit;
                }
            }
        } //if the new parent is not root end
        //else,move it as a new Root
        else {
            //if moved/copied node is not Root
            if (!$moved_node->isRoot()) {

                if ($moved_node->moveAsRoot()) {
                    echo json_encode(array('success' => true));
                } else {
                    echo json_encode(array('success' => false));
                }
            } //else if moved/copied node is Root
            else {

                echo json_encode(array('success' => false, 'message' => 'Node is already a Root.Roots are ordered by primary key.'));
            }
        }
    }

    /**
     * Returns the unordered  list which jstree acts upon.
     */
    public function actionFetchTree()
    {
        self::printULTree();
    }

    public function loadModel($id)
    {
        $model = CActiveRecord::model($this->modelClassName)->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Prints  the unordered  list which jstree acts upon.
     */
    public function printULTree()
    {
        $categories = CActiveRecord::model($this->modelClassName)->findAll(array('order' => 'lft'));
        $level = 0;
        foreach ($categories as $n => $category) {

            if ($category->level == $level)
                echo CHtml::closeTag('li') . "\n";
            else if ($category->level > $level)
                echo CHtml::openTag('ul') . "\n";
            else {
                echo CHtml::closeTag('li') . "\n";

                for ($i = $level - $category->level; $i; $i--) {
                    echo CHtml::closeTag('ul') . "\n";
                    echo CHtml::closeTag('li') . "\n";
                }
            }

            echo CHtml::openTag('li', array('id' => 'node_' . $category->primaryKey, 'rel' => $category->getAttribute($this->rel_property)));
            echo CHtml::openTag('a', array('href' => '#'));
            echo CHtml::encode($category->getAttribute($this->label_property));
            echo CHtml::closeTag('a');

            $level = $category->level;
        }

        for ($i = $level; $i; $i--) {
            echo CHtml::closeTag('li') . "\n";
            echo CHtml::closeTag('ul') . "\n";
        }
    }

    /**
     * Prints  the unordered list of nodes with no anchors.
     */
    public function printULTree_noAnchors()
    {
        $categories = CActiveRecord::model($this->modelClassName)->findAll(array('order' => 'lft'));
        $level = 0;

        foreach ($categories as $n => $category) {
            if ($category->level == $level)
                echo CHtml::closeTag('li') . "\n";
            else if ($category->level > $level)
                echo CHtml::openTag('ul') . "\n";
            else { //if $category->level<$level
                echo CHtml::closeTag('li') . "\n";

                for ($i = $level - $category->level; $i; $i--) {
                    echo CHtml::closeTag('ul') . "\n";
                    echo CHtml::closeTag('li') . "\n";
                }
            }

            echo CHtml::openTag('li');
            echo CHtml::encode($category->getAttribute($this->label_property));
            $level = $category->level;
        }

        for ($i = $level; $i; $i--) {
            echo CHtml::closeTag('li') . "\n";
            echo CHtml::closeTag('ul') . "\n";
        }
    }

    /**
     *  don't reload these scripts or they will mess up the page
     */
    private function excludeScripts()
    {
        Yii::app()->clientScript->scriptMap['*.js'] = false;
    }

}

