<?php
/**
 * ExtMinScriptController class file.
 *
 * @author Tobias Giacometti
 * @link http://bitbucket.org/limi7less/minscript/
 * @copyright Copyright &copy; 2011-2013 Tobias Giacometti
 * @license http://bitbucket.org/limi7less/minscript/wiki/License
 * @package ext.minScript.controllers
 * @since 1.0
 */

/**
 * ExtMinScriptController serves files from minScript groups in a minified, combined and compressed state.
 * A far future "Expires" header ensures that the client browser caches the files optimally.
 *
 * @author Tobias Giacometti
 * @package ext.minScript.controllers
 * @since 1.0
 */
class ExtMinScriptController extends CExtController {

	/**
	 * @var string ID of the minScript application component. Defaults to "clientScript".
	 * @since 2.1
	 */
	public $minScriptComponentId = 'clientScript';

	/**
	 * @var ExtMinScript The minScript application component instance.
	 * @since 2.1
	 */
	protected $_minScriptComponent;

	/**
	 * Serve files.
	 */
	public function actionServe() {
		Yii::$enableIncludePath = false;
		require (dirname(dirname(__FILE__)) . '/vendors/minify/min/index.php');
	}

	/**
	 * Ensure that everything is prepared before we execute the serve action.
	 * @param CFilterChain $filterChain Instance of CFilterChain.
	 * @throws CException if the minScript application component is not defined in CWebApplication::$components.
	 * @since 2.1
	 */
	public function filterPrepareServe($filterChain) {
		// Check for existence of the minScript application component inside CWebApplication::$components
		if (!(($this -> _minScriptComponent = Yii::app() -> getComponent($this -> minScriptComponentId)) instanceof ExtMinScript)) {
			throw new CException('The minScript application component with ID "' . $this -> minScriptComponentId . '" needs to be defined in CWebApplication::$components.');
		}
		// Clean output buffer and headers
		@ob_end_clean();
		header('X-Powered-By:');
		header('Pragma:');
		header('Expires:');
		header('Cache-Control:');
		header('Last-Modified:');
		header('Etag:');
		// Process query string
		$get = array();
		if (isset($_GET['g'])) {
			$get['g'] = $_GET['g'];
		}
		if (isset($_GET['debug'])) {
			$get['debug'] = '';
		} elseif (isset($_GET['lm']) && ctype_digit((string)$_GET['lm'])) {
			$get[$_GET['lm']] = '';
		}
		$_GET = $get;
		$_SERVER['QUERY_STRING'] = http_build_query($get, '', '&');
		// Disable CWebLogRoute
		if (isset(Yii::app() -> log)) {
			foreach (Yii::app()->log->routes as $route) {
				if ($route instanceof CWebLogRoute) {
					$route -> enabled = false;
				}
			}
		}
		// Serve
		$filterChain -> run();
	}

	/**
	 * Execute filters.
	 * @return array Filters to execute.
	 */
	public function filters() {
		return array('prepareServe + serve');
	}

	/**
	 * Log error messages. Used as logger for minify.
	 * @param string $message Error message.
	 * @since 2.1
	 */
	public function log($message) {
		Yii::log($message, CLogger::LEVEL_ERROR, 'ext.minScript.controllers.ExtMinScriptController');
		Yii::getLogger() -> flush(true);
	}

}

/**
 * Include Minify_Source class file.
 */
require_once (dirname(dirname(__FILE__)) . '/vendors/minify/min/lib/Minify/Source.php');

/**
 * ExtMinScriptSource extends Minify_Source to offer custom source handling for minScript.
 *
 * @author Tobias Giacometti
 * @package ext.minScript.controllers
 * @since 2.1
 */
class ExtMinScriptSource extends Minify_Source {

	/**
	 * Initialize ExtMinScriptSource.
	 * @param array $options Initialization options.
	 */
	public function __construct($options) {
		if (isset($options['filepath'])) {
			$this -> contentType = CFileHelper::getMimeTypeByExtension($options['filepath']);
			$this -> filepath = $options['filepath'];
			$this -> _id = $options['filepath'];
			$this -> lastModified = isset($options['lastModified']) ? $options['lastModified'] : time();
			$this -> minifier = isset($options['minifier']) ? $options['minifier'] : null;
		}
	}

}

/**
 * ExtYiiMinCache gives Minify the ability to access Yii's cache application components for caching.
 *
 * @author Tobias Giacometti
 * @package ext.minScript.controllers
 * @since 2.1
 */
class ExtYiiMinCache {

	/**
	 * @var CCache A Yii Framework cache application component instance.
	 */
	protected $_yiiCache;

	/**
	 * @var integer Seconds until cache expiration. 0 means never expire.
	 */
	protected $_exp;

	/**
	 * @var integer Most recently fetched last modified timestamp.
	 */
	protected $_lm = null;

	/**
	 * @var string Most recently fetched data.
	 */
	protected $_data = null;

	/**
	 * @var string Most recently fetched cache ID.
	 */
	protected $_id = null;

	/**
	 * Initialize ExtYiiMinCache.
	 * @param CCache $yiiCache A Yii Framework cache application component instance.
	 * @param integer $expire Seconds until cache expiration. 0 means never expire.
	 * @throws CException if the Yii Framework cache application component is not valid.
	 */
	public function __construct($yiiCache, $expire = 0) {
		if (!($yiiCache instanceof ICache)) {
			throw new CException('The Yii Framework cache application component is not valid.');
		}
		$this -> _yiiCache = $yiiCache;
		$this -> _exp = $expire;
	}

	/**
	 * Store data in cache.
	 * @param string $id The cache ID.
	 * @param string $data Data which should get stored in cache.
	 * @return boolean Whether the data was stored successfully.
	 */
	public function store($id, $data) {
		return $this -> _yiiCache -> set('ExtYiiMinCache' . $id, "{$_SERVER['REQUEST_TIME']}|{$data}", $this -> _exp);
	}

	/**
	 * Get the size of a cache entry.
	 * @param string $id The cache ID.
	 * @return integer The size in bytes or false on failure.
	 */
	public function getSize($id) {
		if (!$this -> _fetch($id)) {
			return false;
		}
		return (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) ? mb_strlen($this -> _data, '8bit') : strlen($this -> _data);
	}

	/**
	 * Check if a valid cache entry exists.
	 * @param string $id The cache ID.
	 * @param integer $lm Last modified timestamp of the original source files.
	 * @return boolean Whether a valid cache entry exists.
	 */
	public function isValid($id, $lm) {
		return ($this -> _fetch($id) && ($this -> _lm >= $lm));
	}

	/**
	 * Display the cached content.
	 * @param string $id The cache ID.
	 */
	public function display($id) {
		echo $this -> _fetch($id) ? $this -> _data : '';
	}

	/**
	 * Fetch the cached content.
	 * @param string $id The cache ID.
	 * @return string The cached content.
	 */
	public function fetch($id) {
		return $this -> _fetch($id) ? $this -> _data : '';
	}

	/**
	 * Fetch data and timestamp from cache.
	 * @param string $id The cache ID.
	 * @return boolean Whether the data was fetched successfully.
	 */
	protected function _fetch($id) {
		if ($this -> _id === $id) {
			return true;
		}
		$ret = $this -> _yiiCache -> get('ExtYiiMinCache' . $id);
		if (false === $ret) {
			$this -> _id = null;
			return false;
		}
		list($this -> _lm, $this -> _data) = explode('|', $ret, 2);
		$this -> _id = $id;
		Yii::log('Processed files for ID "' . $id . '" are served directly from cache.', CLogger::LEVEL_INFO, 'ext.minScript.controllers.ExtMinScriptController');
		return true;
	}

}
