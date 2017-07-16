<?php
class ActionCode extends CCodeModel
{
    public $className;
    public $baseClass='CAction';
    public $scriptPath='application.controllers';

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('className,scriptPath', 'required'),
            array('className', 'match', 'pattern'=>'/^\w+$/'),
			array('baseClass', 'match', 'pattern'=>'/^\w+$/', 'message'=>'{attribute} should only contain word characters. (e.g. delete)'),			
            array('scriptPath', 'validateScriptPath'),
            array('scriptPath', 'sticky'),            
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'baseClass'=>'Base Class',
            'className'=>'Action ID',
            'scriptPath'=>'Script Path',            
        ));
    }

    public function getBaseClassNames()
    {
        return array(
            'CAction'=>'CAction',
            'XRenderAction'=>'XRenderAction',
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
        $path=Yii::getPathOfAlias($this->scriptPath).'/' . ucfirst($this->className) . 'Action.php';
        $code=$this->render($this->templatepath.'/include.php');
        $this->files[]=new CCodeFile($path, $code);
    }

	public function successMessage()
	{
		$output=<<<EOD
<p>The Action has been generated successfully.</p>
EOD;
		$code=$this->render($this->templatePath.'/include.php');
		return $output.highlight_string($code,true);
	}
}
?>