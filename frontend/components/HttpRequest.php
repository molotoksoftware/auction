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

class HttpRequest extends CHttpRequest 
{
    public $noCsrfValidationRoutes = array();

    protected function normalizeRequest()
    {
        parent::normalizeRequest();

        $route = Yii::app()->getUrlManager()->parseUrl($this);
        if($this->enableCsrfValidation){
            foreach($this->noCsrfValidationRoutes as $cr){
                if(preg_match('#'.$cr.'#', $route)){
                    Yii::app()->detachEventHandler('onBeginRequest',
                        array($this,'validateCsrfToken'));
                    Yii::trace('Route "'.$route.' passed without CSRF validation');
                    break; // found first route and break
                }
            }
        }
    }
}
