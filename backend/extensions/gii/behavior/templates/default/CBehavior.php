<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> class file.
 */

class <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Declares events and the corresponding event handler methods.
	 * The events are defined by the {@link owner} component, while the handler
	 * methods by the behavior class. The handlers will be attached to the corresponding
	 * events when the behavior is attached to the {@link owner} component; and they
	 * will be detached from the events when the behavior is detached from the component.
	 * @return array events (array keys) and the corresponding event handler methods (array values).
	 */
	public function events()
	{
		return array();
	}

}
