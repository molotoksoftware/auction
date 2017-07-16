<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className).'MessageSource'; ?> class file.
 */

class <?php echo ucfirst($this->className).'MessageSource'; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Loads the message translation for the specified language and category.
	 * @param string $category the message category
	 * @param string $language the target language
	 * @return array the loaded messages
	 */
	protected function loadMessages($category,$language)
    {
        // your code here...
    }
}
