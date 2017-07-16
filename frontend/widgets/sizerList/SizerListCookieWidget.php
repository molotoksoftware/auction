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


class SizerListCookieWidget extends CWidget
{
    public $sizerAttribute = 'size';

    /**
     * @var array items per page sizes variants
     */
    public $sizerVariants = array(10, 20, 30);

    /**
     * @var string CSS class of sorter element
     */
    public $sizerCssClass = 'sizer';

    /**
     * @var string the text shown before sizer links. Defaults to empty.
     */
    public $sizerHeader = 'Show by: ';

    /**
     * @var string the text shown after sizer links. Defaults to empty.
     */
    public $sizerFooter = '';
    public $dataProvider;

    public function renderSizer()
    {
        $pageVar = $this->dataProvider->getPagination()->pageVar;
        $pageSize = $this->dataProvider->getPagination()->pageSize;


        echo CHtml::openTag('ul', array('class' => $this->sizerCssClass));

        foreach ($this->sizerVariants as $i => $count) {
            $params = array_replace($_GET, array($this->sizerAttribute => $count));
            if (isset($params[$pageVar]))
                unset($params[$pageVar]);

            $class = '';
            if ($i == 0)
                $class.=' first';

            if ($i == count($this->sizerVariants) - 1)
                $class.=' last';

            if ((isset(Yii::app()->request->cookies['item_on_page']->value) && Yii::app()->request->cookies['item_on_page']->value == $count) || (!isset(Yii::app()->request->cookies['item_on_page']->value) && $count == 25))
                $class.=' active';


            $htmlOptions['class'] = $class;
            $link = urldecode(CHtml::link($count, Yii::app()->controller->createUrl('', $params)));

            echo CHtml::tag('li', $htmlOptions, $link);
        }
        echo CHtml::closeTag('ul');
    }

    public function run()
    {
        $this->renderSizer();
    }
}