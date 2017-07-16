<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."ViewRenderer"; ?> class file.
 */

class <?php echo ucfirst($this->className)."ViewRenderer"; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Parses the source view file and saves the results as another file.
	 * @param string the source view file path
	 * @param string the resulting view file path
	 */
	protected function generateViewFile($sourceFile,$viewFile)
    {
        // your code....
    }
}