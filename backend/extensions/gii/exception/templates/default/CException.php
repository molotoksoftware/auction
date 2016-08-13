<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Exception"; ?> class file.
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