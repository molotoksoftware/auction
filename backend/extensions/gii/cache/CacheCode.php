<?php
class CacheCode extends CCodeModel
{
    public $className;
    public $baseClass='CCache';
    public $scriptPath='ext.cache';    

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
            'className'=>'Cache Class Name',
            'scriptPath'=>'Script Path',            
        ));
    }

    public function getBaseClassNames()
    {
        return array(
            'CCache'=>'CCache',
            'CCacheDependency'=>'CCacheDependency',
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
        $cache = $this->getClassSuffix();
		$output=<<<EOD
<p>The $cache Component has been generated successfully.</p>
EOD;
		$code=$this->render($this->templatePath.'/include.php');
		return $output.highlight_string($code,true);
	}

    public function getClassSuffix() {
        switch ($this->baseClass){
            case 'CCache' : return 'Cache';
            case 'CCacheDependency' : return 'CacheDependency';
            default:
                return '';
        }
    }
}
?>