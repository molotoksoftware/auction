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


class CitySelectorWidget extends CWidget
{
    const SCOPE_DEFAULT = 'default';
    const SCOPE_CATEGORY = 'category';

    public $model = null;
    public $className = 'selectbox';
    public $showIco = true;
    public $useUserRegion = true;
    public $scope = self::SCOPE_DEFAULT;
    public $baseUrl = '';

    private $defaultLocation;

    public function init()
    {
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/country_dropdown.js');
        $this->defaultLocation = Setting::model()->find('name=:name AND type=:type',
            array(
                ':name' => 'defaultLocation',
                ':type' => Setting::TYPE_LOCALIZATION,
            )
        );
    }

    public function run()
    {
        $this->render('index', [
            'model'         => $this->model,
            'className'     => $this->className,
            'showIco'       => $this->showIco,
            'useUserRegion' => $this->useUserRegion,
            'scope'         => $this->scope,
            'baseUrl'         => $this->baseUrl,
            'defaultLocation'         => $this->defaultLocation,
        ]);
    }
}