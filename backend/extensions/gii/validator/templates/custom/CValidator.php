<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Validator"; ?> class file.
 *
 * @author Jon Doe <jonny@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * <?php echo ucfirst($this->className)."Validator"; ?> is ...
 *
 *
 * @author Jon Doe <jonny@gmail.com>
 * @version
 * @package
 * @since 1.0
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