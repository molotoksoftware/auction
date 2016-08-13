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


class CustomUrlRule extends CUrlRule
{

    public function createUrl($manager, $route, $params, $ampersand)
    {
        if ($this->parsingOnly)
            return false;

        if ($manager->caseSensitive && $this->caseSensitive === null || $this->caseSensitive)
            $case = '';
        else
            $case = 'i';

        $tr = array();
        if ($route !== $this->route) {
            if ($this->routePattern !== null && preg_match($this->routePattern . $case, $route, $matches)) {
                foreach ($this->references as $key => $name)
                    $tr[$name] = $matches[$key];
            }
            else
                return false;
        }

        foreach ($this->defaultParams as $key => $value) {
            if (isset($params[$key])) {
                if ($params[$key] == $value)
                    unset($params[$key]);
                else
                    return false;
            }
        }

        foreach ($this->params as $key => $value)
            if (!isset($params[$key]))
                return false;

        if ($manager->matchValue && $this->matchValue === null || $this->matchValue) {
            foreach ($this->params as $key => $value) {
                if (!preg_match('/\A' . $value . '\z/u' . $case, $params[$key]))
                    return false;
            }
        }

        foreach ($this->params as $key => $value) {
            $tr["<$key>"] = $params[$key];
            unset($params[$key]);
        }

        $suffix = $this->urlSuffix === null ? $manager->urlSuffix : $this->urlSuffix;

        $url = strtr($this->template, $tr);

        if ($this->hasHostInfo) {
            $hostInfo = Yii::app()->getRequest()->getHostInfo();
            if (stripos($url, $hostInfo) === 0)
                $url = substr($url, strlen($hostInfo));
        }

        if (empty($params))
            return $url !== '' ? $url . $suffix : $url;

        if ($this->append)
            $url.='/' . $manager->createPathInfo($params, '/', '/') . $suffix;
        else {
            if ($url !== '')
                $url.=$suffix;
            $url.='?' . $manager->createPathInfo($params, '=', $ampersand);
        }

        return $url;
    }

}