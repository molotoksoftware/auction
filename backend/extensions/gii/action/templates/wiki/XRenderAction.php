<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className).'Action'; ?> class file.
 */

class <?php echo ucfirst($this->className).'Action'; ?> extends <?php echo $this->baseClass."\n"; ?>
{
    public function run()
    {
        // get the Model Name
        $model_class = ucfirst('car');

        // create the Model
        $model = new $model_class();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        if (isset($_POST[$model_class])) {
            $model->attributes = $_POST[$model_class];

            if ($model->save())
            $this->redirect(array('view', 'id' => $model->id));
        }
        $this->render('create', array(
            'model' => $model,
        ));

    }

}