<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
 * @copyright 2016 MolotokSoftware
 * @license GNU General Public License, version 3
 */

/**
 * 
 * This file is part of MolotokSoftware.
 *
 * MolotokSoftware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MolotokSoftware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with MolotokSoftware.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 *  Page class file

 *
 *
 * @name Page
 * @package
 * @version 0.1
 * @author timur
 *
 */
class PageWidget extends CWidget {

    public $alias;
    private $_model;

    public function init() {
        if (is_null($this->alias)) {
            throw new CException('Specify "alias" page');
        }

        $dependency = new CDbCacheDependency('SELECT MAX(`update`) FROM pages where alias="'.$this->alias.'"');
        $this->_model = Page::model()->cache(1000, $dependency)->find('alias=:alias',array(':alias' => $this->alias));

        if (is_null($this->_model)) {
            throw new CHttpException(404, 'Page net found');
        }
    }

    public function run() {
    }

    /**
     * @param $width (int)
     * @return string
     */
    public function getTitle($width = 0) {

        return ($width > 0) ? wordwrap($this->_model->title, $width, "<br />\n") : $this->_model->title;
    }

    public function getContent() {
        return $this->_model->body;
    }

    public function getSeoTitle() {
        if (trim($this->_model->meta_title !== '')) {
            $this->getController()->setPageTitle($this->_model->meta_title);
        } else {
            $this->getController()->setPageTitle($this->_model->title);
        }
    }

    public function getSeoDescription() {
        if (trim($this->_model->meta_description !== ''))
            $this->getController()->pageDescription = $this->_model->meta_description;
    }

    public function getSeoKeywords() {
        if (trim($this->_model->meta_keywords !== ''))
            $this->getController()->pageKeywords = $this->_model->meta_keywords;
    }

}