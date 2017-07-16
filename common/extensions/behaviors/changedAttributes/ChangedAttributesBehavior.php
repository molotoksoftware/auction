<?php

/**
 * TranslitBehavior class file.
 *
 * @author
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
class ChangedAttributesBehavior extends CActiveRecordBehavior
{
    public $_oldAttributes = array();

    public function afterFind($event) {
        $this->_oldAttributes = $this->getOwner()->attributes;
        parent::afterFind($event);
    }

    public function hasChanged($name) {
        return $this->_oldAttributes[$name] != $this->getOwner()->getAttribute($name);
    }

    public function getChanged() {
        $result = array();

        foreach($this->_oldAttributes as $name => $value) {
            if($value != $this->getOwner()->getAttribute($name))
                $result[] = $name;
        }

        return $result;
    }
}
