<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."LogRoute"; ?> class file. 
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