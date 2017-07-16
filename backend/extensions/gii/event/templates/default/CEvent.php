<?php echo "<?php\n"; ?>
/**
 * <?php echo ucfirst($this->className)."Event"; ?> class file.
 *
 * @author Jon Doe <jonny@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * <?php echo ucfirst($this->className)."Event"; ?> represents the parameter for the ... event.
 *
 *
 * @author Jon Doe <jonny@gmail.com>
 * @version
 * @package
 * @since 1.0
 */

class <?php echo ucfirst($this->className)."Event"; ?> extends <?php echo $this->baseClass."\n"; ?>
{
    /**
     * Constructor.
     * @param mixed sender of the event
     */
    public function __construct($sender=null,$params=null)
    {
        parent::___construct($sender,$params);
    }
}