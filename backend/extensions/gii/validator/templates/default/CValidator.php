<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Validator"; ?> class file. 
 */
class <?php echo ucfirst($this->className).'Validator'; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param CModel the data object being validated
	 * @param string the name of the attribute to be validated.
	 */
	protected function validateAttribute($object,$attribute){
		// your code here ...
	}
}