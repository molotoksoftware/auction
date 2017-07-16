    <div class="row">
        <?php echo $form->labelEx($model,'className'); ?>
        <?php echo $form->textField($model,'className',array('size'=>65,'style'=>'width:100%;')); ?>
        <div class="tooltip">
            View renderer class name must only contain word characters.
        </div>
        <?php echo $form->error($model,'className'); ?>
    </div>

	<div class="row sticky">
		<?php echo $form->labelEx($model,'baseClass'); ?>
		<?php echo $form->textField($model,'baseClass',array('size'=>65)); ?>
		<div class="tooltip">
			This is the class that the new portlet class will extend from.
			Please make sure the class exists and can be autoloaded.
		</div>
		<?php echo $form->error($model,'baseClass'); ?>
	</div>
	<div class="row sticky">
		<?php echo $form->labelEx($model,'scriptPath'); ?>
		<?php echo $form->textField($model,'scriptPath', array('size'=>65)); ?>
		<div class="tooltip">
			This refers to the directory that the new view script file should be generated under.
			It should be specified in the form of a path alias, for example, <code>application.web</code>,
			<code>mymodule.views</code>.
            <br /><br />When running the generator on Mac OS, Linux or Unix, you may need to change the
permission of the script path so that it is full writeable. <br />Otherwise you will get a generation error.
		</div>
		<?php echo $form->error($model,'scriptPath'); ?>
	</div>