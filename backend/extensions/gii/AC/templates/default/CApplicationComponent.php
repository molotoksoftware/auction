<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className); ?> class file. 
 */

class <?php echo ucfirst($this->className); ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Initializes the application component.
	 * This method is required by {@link IApplicationComponent} and is invoked by application.
	 * If you override this method, make sure to call the parent implementation
	 * so that the application component can be marked as initialized.
	 */
	public function init()
	{
		parent::init();
        // your code...
	}

}
