<?php echo "<?php\n"; ?>
<?php echo $this->renderClassLevelComment(); ?>
class <?php echo ucfirst($this->className).'Cache'; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Initializes this application component.
	 * This method is required by the {@link IApplicationComponent} interface.
	 * @throws CException ...
	 */
	public function init()
	{
		parent::init();
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	protected function getValue($key)
	{
		return false;
	}

	/**
	 * Retrieves multiple values from cache with the specified keys.
	 * @param array a list of keys identifying the cached values
	 * @return array a list of cached values indexed by the keys
	 */
	protected function getValues($keys)
	{
		return parent::getValues($keys);
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function setValue($key,$value,$expire)
	{
		return false;
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	protected function addValue($key,$value,$expire)
	{
		return false;
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key)
	{
		return false;
	}

	/**
	 * Deletes all values from cache.
	 * Child classes may implement this method to realize the flush operation.
	 * @return boolean whether the flush operation was successful.
	 */
	protected function flushValues()
	{
		return false;
	}


}