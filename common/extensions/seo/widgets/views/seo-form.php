<div class="control-group ">
    <label for="<?php echo CHtml::activeId($model, $title); ?>" class="control-label">Meta Title</label>
    <div class="controls">
        <?php
        echo CHtml::activeTextField($model, $title, array(
            'class' => 'span8'
        ));
        ?>
        <?php echo CHtml::error($model, $title, array('class' => 'help-inline error')); ?>
    </div>
</div>

<div class="control-group ">
    <label for="<?php echo CHtml::activeId($model, $description); ?>" class="control-label">Meta Description</label>
    <div class="controls">
        <?php echo CHtml::activeTextArea($model, $description, array('cols' => 60, 'rows' => '3', 'class' => 'span8')); ?>
        <?php echo CHtml::error($model, $description, array('class' => 'help-inline error')); ?>
    </div>
</div>

<div class="control-group ">
    <label for="<?php echo CHtml::activeId($model, $keywords); ?>" class="control-label">Meta Keywords</label>
    <div class="controls">
        <?php
        echo CHtml::activeTextArea($model, $keywords, array('cols' => 60, 'rows' => '3',
            'class' => 'span8'
        ));
        ?>
        <?php echo CHtml::error($model, $keywords, array('class' => 'help-inline error')); ?>
    </div>
</div>