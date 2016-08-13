<?php
/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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

class BackController extends BaseController
{

    //необходимо для bootstrap админки
    public $header_info = array();
    private $_behaviorIDs = array();

    public function multipleRemove($class)
    {
        if (!isset($_POST['data']))
            RAjax::error(array('messages' => 'error'));
        $removes = array();
        $removes = CJSON::decode($_POST['data']);

        if (is_array($removes) && count($removes) > 0) {
            try {
                foreach ($removes as $item) {
                    $this->loadModel($class, $item)->delete();
                }
            } catch (Exception $e) {
                RAjax::error(array('messages' => 'Error'));
            }
        }
        RAjax::success(array('messages' => "Выбрание элементы успешно удалены"));
    }

    public function createAction($actionID)
    {
        $action = parent::createAction($actionID);
        if ($action !== null)
            return $action;
        foreach ($this->_behaviorIDs as $behaviorID) {
            $object = $this->asa($behaviorID);
            if ($object->getEnabled() && method_exists($object, 'action' . $actionID))
                return new CInlineAction($object, $actionID);
        }
    }

    public function attachBehavior($name, $behavior)
    {
        $this->_behaviorIDs[] = $name;
        parent::attachBehavior($name, $behavior);
    }

}
