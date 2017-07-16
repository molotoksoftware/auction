<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."LogRoute"; ?> class file.
 *
 * @author Jon Doe <jonny@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * <?php echo ucfirst($this->className)."LogRoute"; ?> is ...
 *
 *
 * @author Jon Doe <jonny@gmail.com>
 * @version
 * @package
 * @since 1.0
 */

class <?php echo ucfirst($this->className).'LogRoute'; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Initializes the route.
	 * This method is invoked after the route is created by the route manager.
	 */
	public function init()
	{
        //your code here...
	}

	/**
	 * Processes log messages and sends them to specific destination.
	 * Derived child classes must implement this method.
	 * @param array list of messages.  Each array elements represents one message
	 * with the following structure:
	 * array(
	 *   [0] => message (string)
	 *   [1] => level (string)
	 *   [2] => category (string)
	 *   [3] => timestamp (float, obtained by microtime(true));
	 */
	protected function processLogs($logs)
    {
        //your code here...
	}
}