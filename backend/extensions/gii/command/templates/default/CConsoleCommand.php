<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Command"; ?> class file. 
 */
class <?php echo ucfirst($this->className)."Command"; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Executes the command.
	 * @param array command line parameters for this command.
	 */
    public function run($args)
    {
        // $args gives an array of the command-line arguments for this command
    }
	/**
	 * Provides the command description.
	 * This method may be overridden to return the actual command description.
	 * @return string the command description. Defaults to 'Usage: php entry-script.php command-name'.
	 */
    public function getHelp()
    {
        return 'Usage: how to use this command';
    }

}