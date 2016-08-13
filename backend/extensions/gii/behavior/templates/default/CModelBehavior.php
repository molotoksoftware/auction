<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> class file.
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

}
