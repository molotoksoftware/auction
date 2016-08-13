<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> class file.
 *
 * @author Jon Doe <jonny@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> is ...
 *
 *
 * @author Jon Doe <jonny@gmail.com>
 * @version
 * @package
 * @since 1.0
 */

class <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Declares events and the corresponding event handler methods.
	 * @return array events (array keys) and the corresponding event handler methods (array values).
	 * @see CBehavior::events
	 */
	public function events()
	{
		return array_merge(parent::events(), array(
			//'onBeforeJump'=>'beforeJump',
		));
	}

	/**
	 * Responds to {@link CModel::onAfterConstruct} event.
	 * Overrides this method if you want to handle the corresponding event of the {@link CBehavior::owner owner}.
	 * @param CEvent $event event parameter
	 */
	public function afterConstruct($event)
	{
	}

	/**
	 * Responds to {@link CModel::onBeforeValidate} event.
	 * Overrides this method if you want to handle the corresponding event of the {@link owner}.
	 * You may set {@link CModelEvent::isValid} to be false to quit the validation process.
	 * @param CModelEvent $event event parameter
	 */
	public function beforeValidate($event)
	{
	}

	/**
	 * Responds to {@link CModel::onAfterValidate} event.
	 * Overrides this method if you want to handle the corresponding event of the {@link owner}.
	 * @param CEvent $event event parameter
	 */
	public function afterValidate($event)
	{
	}

    /**
     * Logs a message.
     *
     * @param string $message Message to be logged
     * @param string $level Level of the message (e.g. 'trace', 'warning',
     * 'error', 'info', see CLogger constants definitions)
     */
    public static function log($message, $level='error')
    {
        Yii::log($message, $level, __CLASS__);
    }

    /**
     * Dumps a variable or the object itself in terms of a string.
     *
     * @param mixed variable to be dumped
     */
    protected function dump($var='dump-the-object',$highlight=true)
    {
        if ($var === 'dump-the-object') {
            return CVarDumper::dumpAsString($this,$depth=15,$highlight);
        } else {
            return CVarDumper::dumpAsString($var,$depth=15,$highlight);
        }
    }
}
