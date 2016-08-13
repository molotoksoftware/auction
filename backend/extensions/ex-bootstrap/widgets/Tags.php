<?php

Yii::import('ext.bootstrap.widgets.TbTags');

class Tags extends TbTags {
    public function renderContent($id, $name) {
        if ($this->hasModel()) {
            if ($this->form) {
                echo $this->form->hiddenField($this->model, $this->attribute);
            } else {
                echo CHtml::activeHiddenField($this->model, $this->attribute);
            }
        } else {
            echo CHtml::hiddenField($name, $this->value);
        }

        echo CHtml::openTag('div', array('class' => 'control-group'));
        echo CHtml::label('Теги', 'tags_' . $id, array('class' => 'control-label'));
        echo CHtml::openTag('div', array('class' => 'controls'));
        echo "<div id='tags_{$id}' class='tag-list'><div class='tags'></div></div>";
        echo CHtml::closeTag('div');
        echo CHtml::closeTag('div');
    }

}