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


Yii::import('zii.widgets.CBreadcrumbs');

class Breadcrumbs extends CBreadcrumbs
{

    public function run()
    {
        if (empty($this->links))
            return;

        echo CHtml::openTag($this->tagName, $this->htmlOptions) . "\n";
        $links = array();
        if ($this->homeLink === null)
            $links[] = CHtml::link(Yii::t('zii', 'Home'), Yii::app()->homeUrl);
        elseif ($this->homeLink !== false)
            $links[] = $this->homeLink;
        foreach ($this->links as $label => $url) {
            if (is_string($label) || is_array($url))
                $links[] = strtr($this->activeLinkTemplate, array(
                    '{url}' => urldecode(CHtml::normalizeUrl($url)),
                    '{label}' => $this->encodeLabel ? CHtml::encode($label) : $label,
                        ));
            else
                $links[] = str_replace('{label}', $this->encodeLabel ? CHtml::encode($url) : $url, $this->inactiveLinkTemplate);
        }
        echo implode($this->separator, $links);
        echo CHtml::closeTag($this->tagName);
    }

}
