<?php

/**
 * Widget for adding SEO options to model editing form
 *
 * @version 1.0
 */
class SeoWidget extends CWidget
{

    //model for editing
    public $model;
    public $form;
    public $titleAttribute = 'meta_title';
    public $descriptionAttribute = 'meta_description';
    public $keywordsAttribute = 'meta_keywords';

    public function run()
    {
        $this->render('seo-form', array(
            'model' => $this->model,
            'title' => $this->titleAttribute,
            'description' => $this->descriptionAttribute,
            'keywords' => $this->keywordsAttribute
        ));
    }

}

?>
