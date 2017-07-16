<?php

/**
 * Provides SEO functionality for a model.
 *
 * @version 1.0
 * @package SeoBehavior
 */
class SeoBehavior extends CActiveRecordBehavior
{

    public $titleAttribute = 'meta_title';
    public $descriptionAttribute = 'meta_description';
    public $keywordsAttribute = 'meta_keywords';
    public $scenarios = array('insert', 'update');

    public function attach($owner)
    {
        parent::attach($owner);
        $modelName = get_class($owner);

        if (in_array($owner->getScenario(), $this->scenarios)) {

            $titleValidator = CValidator::createValidator(
                            'length', $owner, $this->titleAttribute, array(
                        'max' => 80,
                            )
            );
            $commonValidator = CValidator::createValidator(
                            'length', $owner, implode(', ', array(
                                $this->descriptionAttribute,
                                $this->keywordsAttribute,
                            )), array('max' => 200)
            );

            $owner->validatorList->add($titleValidator);
            $owner->validatorList->add($commonValidator);
        }

        if (isset($_POST[$modelName][$this->titleAttribute])) {
            $owner{$this->titleAttribute} = $_POST[$modelName][$this->titleAttribute];
        }
    }

}

?>
