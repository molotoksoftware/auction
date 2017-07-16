<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Exception"; ?> class file.
 *
 * @author Jon Doe <jonny@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * <?php echo ucfirst($this->className)."Exception"; ?> represents an exception caused by ...
 *
 *
 * @author Jon Doe <jonny@gmail.com>
 * @version
 * @package
 * @since 1.0
 */

class <?php echo ucfirst($this->className).'Exception'; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Constructor.
	 * @param string $message error message
	 * @param integer $code error code
	 */
	public function __construct($message=null,$code=0)
	{
        // your code here...
		parent::__construct($message,$code);
	}

}