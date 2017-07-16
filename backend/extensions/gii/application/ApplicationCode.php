<?php
class ApplicationCode extends CCodeModel
{
    public $className;
    public $baseClass='CApplication';
    public $scriptPath='application.components';

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('className,baseClass,scriptPath', 'required'),
            array('className', 'match', 'pattern'=>'/^\w+$/'),
			array('baseClass', 'match', 'pattern'=>'/^\w+$/', 'message'=>'{attribute} should only contain word characters.'),
			array('baseClass', 'sticky'),
            array('scriptPath', 'validateScriptPath'),
            array('scriptPath', 'sticky'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'baseClass'=>'Base Class',
            'className'=>'Class Name of the Application',
            'scriptPath'=>'Script Path',
        ));
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
        $path=Yii::getPathOfAlias($this->scriptPath).'/' . ucfirst($this->className) . 'Application.php';
        $code=$this->render($this->templatepath.DIRECTORY_SEPARATOR.'include.php');
        $this->files[]=new CCodeFile($path, $code);
    }

	public function successMessage()
	{
		$output=<<<EOD
<p>The application class has been generated successfully.</p>
EOD;
		$code=$this->render($this->templatePath.'/include.php');
		return $output.highlight_string($code,true);
	}

}
?>