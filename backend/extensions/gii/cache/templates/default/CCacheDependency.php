<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> class file.
 */

class <?php echo ucfirst($this->className).$this->getClassSuffix(); ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Generates the data needed to determine if dependency has been changed.
	 * Derived classes should override this method to generate actual dependent data.
	 * @return mixed the data needed to determine if dependency has been changed.
	 */
	protected function generateDependentData()
	{
		return null;
	}

}
