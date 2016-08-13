<?php
class BehaviorCode extends CCodeModel
{
    public $className;
    public $baseClass='CBehavior';
    public $scriptPath='application.components.behaviors';
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('className,scriptPath', 'required'),
            array('className', 'match', 'pattern'=>'/^\w+$/'),
			array('baseClass', 'match', 'pattern'=>'/^\w+$/', 'message'=>'{attribute} should only contain word characters.'),			
            array('scriptPath', 'validateScriptPath'),
            array('scriptPath', 'sticky'),            
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'baseClass'=>'Base Class',
            'className'=>'Behavior Class Name',
            'scriptPath'=>'Script Path',            
        ));
    }

    public function getBaseClassNames()
    {
        return array(
            'CBehavior'=>'CBehavior',
            'CModelBehavior'=>'CModelBehavior',
            'CActiveRecordBehavior'=>'CActiveRecordBehavior',
        );
    }

	public function validateScriptPath($attribute,$params)
	{
		if($this->hasErrors('scriptPath'))
			return;
		if(Yii::getPathOfAlias($this->scriptPath)===false)
			$this->addError('scriptPath','Script Path must be a valid path alias.');
	}

    public function prepare()
    {
        $path=Yii::getPathOfAlias($this->scriptPath).'/' . ucfirst($this->className) . $this->getClassSuffix() . '.php';
        $code=$this->render($this->templatepath.'/include.php');
        $this->files[]=new CCodeFile($path, $code);
    }

	public function successMessage()
	{
        $behavior = $this->getClassSuffix();
		$output=<<<EOD
<p>The $behavior Component has been generated successfully.</p>
EOD;
		$code=$this->render($this->templatePath.'/include.php');
		return $output.highlight_string($code,true);
	}

    public function getClassSuffix() {
        switch ($this->baseClass){
            case 'CBehavior' : return 'Behavior';
            case 'CModelBehavior' : return 'ModelBehavior';
            case 'CActiveRecordBehavior' : return 'ActiveRecordBehavior';
            default:
                return '';
        }
    }
    
}
?>