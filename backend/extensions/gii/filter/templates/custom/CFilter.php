<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Filter"; ?> class file.
 *
 * @author Jon Doe <jonny@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * <?php echo ucfirst($this->className)."Filter"; ?> represents a filter ...
 *
 *
 * @author Jon Doe <jonny@gmail.com>
 * @version
 * @package
 * @since 1.0
 */

class <?php echo ucfirst($this->className)."Filter"; ?> extends <?php echo $this->baseClass."\n"; ?>
{

    /**
     * Initializes the filter.
     * This method is invoked after the filter properties are initialized
     * and before {@link preFilter} is called.
     * You may override this method to include some initialization logic.
     */
    public function init()
    {
        // your code here...
    }

	/**
	 * Performs the pre-action filtering.
	 * @param CFilterChain the filter chain that the filter is on.
	 * @return boolean whether the filtering process should continue and the action
	 * should be executed.
	 */
	protected function preFilter($filterChain)
	{
	    // your code here...
		return true;
	}

	/**
	 * Performs the post-action filtering.
	 * @param CFilterChain the filter chain that the filter is on.
	 */
	protected function postFilter($filterChain)
	{
	    // your code here...
	}
}