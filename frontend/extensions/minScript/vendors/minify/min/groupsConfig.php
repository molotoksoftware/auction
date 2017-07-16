<?php
/**
 * minScript groups config file for Minify.
 *
 * @author Tobias Giacometti
 * @link http://bitbucket.org/limi7less/minscript/
 * @copyright Copyright &copy; 2011-2013 Tobias Giacometti
 * @license http://bitbucket.org/limi7less/minscript/wiki/License
 * @package ext.minScript.vendors.minify.min
 * @since 1.0
 */

/**
 * Create group definition for Minify.
 */
$groupId = (isset($_GET['g'])) ? preg_replace('/[^a-z0-9]/i', '', $_GET['g']) : null;
if (isset($groupId) && ($files = $this -> _minScriptComponent -> minScriptGetGroup($groupId)) !== false) {
	// Get the last modified timestamp for the set of files
	if (($lm = $this -> _minScriptComponent -> minScriptGetLm($files)) === false) {
		Yii::log('The minScript group "' . $groupId . '" could not be served because some files are inaccessible.', CLogger::LEVEL_ERROR, 'ext.minScript.controllers.ExtMinScriptController');
		throw new CHttpException(500, 'Internal Server Error');
	}
	// Loop through files and create ExtMinScriptSource instances
	foreach ($files as $key => $file) {
		$minifier = null;
		foreach ($this -> _minScriptComponent -> minScriptDisableMin as $disableMinPattern) {
			if (preg_match($disableMinPattern, $file)) {
				$minifier = '';
				break;
			}
		}
		$files[$key] = new ExtMinScriptSource( array('filepath' => $file, 'lastModified' => $lm, 'minifier' => $minifier));
	}
	// Return group definition
	return array($groupId => $files);
} else {
	Yii::log('The minScript group "' . $groupId . '" could not be served because it was not found.', CLogger::LEVEL_ERROR, 'ext.minScript.controllers.ExtMinScriptController');
	throw new CHttpException(400, 'Bad Request');
}
