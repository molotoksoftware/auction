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


Yii::import('zii.widgets.CMenu');

class CategoriesWidget extends CMenu
{
    public $prefix;
    public $countRelationName;
    public $hasActiveItem;

    /**
     * Показывать указанные категории.
     *
     * @var Category[]
     */
    public $categories;

    public $activeCategory;

    public $widgetCacheKey;

    public $linkBaseUrl;

    public $cacheMenuItems = true;

    /**
     * Добавить в меню пункт Все категории. Работает только в ajax.
     *
     * @var bool|array
     */
    public $prependAllCategoriesItem = false;

    public function init()
    {
        if ($this->countRelationName == null) {
            throw new Exception ('атрибут "countRelationName" не может быть пустым');
        }
        
        $this->items = Category::getMenuArray(
            $this->prefix,
            $this->countRelationName,
            $this->categories,
            $this->activeCategory,
            $this->widgetCacheKey,
            [
                'linkBaseUrl'    => $this->linkBaseUrl,
                'cacheMenuItems' => $this->cacheMenuItems,
            ]
        );

        if (is_array($this->prependAllCategoriesItem)) {
            array_unshift(
                $this->items,
                $this->prependAllCategoriesItem
            );
        }
        if (isset($this->categories)) {
            foreach ($this->items as $i => $eachItem) {
                if (isset($eachItem['count'])) {
                    unset($this->items[$i]['count']);
                }
            }
        }

        $this->activateParents = true;
        parent::init();


        $cs = Yii::app()->clientScript;
        $cs->registerScript(
            'cats', '
                
                function trim(str) {	
                    return str.replace(/[^0-9]/g, "")
                }

                $(".main_nav:not(.profile_cat_tree) > li").each(function(i){
                    var num = 0;
                    $("ul li span.tatr", this).each(function(){
                        var varia = trim($(this).html());
                        num += parseInt(varia);
                    });

                    $(this).append("<span class=\"count_m\" style=\"right: 26px;\">("+num+")</span>");
                });

                $(".main_nav.profile_cat_tree > li").each(function(i){
                    var num = 0;
                    $("ul li span.tatr", this).each(function(){
                        var varia = trim($(this).html());
                        num += parseInt(varia);
                    });
                    $(this).find("span.str").eq(0).after("<span class=\"count_m\">("+num+")</span>");
                });
                
                $(".count_spec").each(function(i){
                    var id = $(this).data("id");
                    $(this).text($(".menu_item_" + id).html());
                });
            ',
	       CClientScript::POS_READY
        ); 

        foreach($this->items as $item) {
            if($item['active']) $this->hasActiveItem = true;
        }
    }

    protected function renderMenuRecursive($items)
    {
        $count = 0;
        $n = count($items);
        foreach ($items as $item) {
            $count++;
            $options = isset($item['itemOptions']) ? $item['itemOptions'] : array();
            $class = array();
            if($this->hasActiveItem && $item['level'] == 2 && !$item['active'] && !$this->prependAllCategoriesItem) continue;
            //if(!$this->hasActiveItem && $item['level'] != 2) continue;

            if ($item['active'] && $this->activeCssClass != '') {
                $class[] = $this->activeCssClass;
            }
            if ($count === 1 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($count === $n && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if ($this->itemCssClass !== null) {
                $class[] = $this->itemCssClass;
            }
            if ($class !== array()) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            echo CHtml::openTag('li', $options);

            $menu = $this->renderMenuItem($item);
            if (isset($this->itemTemplate) || isset($item['template'])) {
                $template = isset($item['template']) ? $item['template'] : $this->itemTemplate;
                echo strtr($template, array('{menu}' => $menu));
            } else {
                echo $menu;
            }

            if (isset($item['items']) && count($item['items'])) {
                echo "\n" . '<span class="str"></span>';

                echo "\n" . CHtml::openTag(
                    'ul',
                    isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions
                ) . "\n";
                $this->renderMenuRecursive($item['items']);
                echo CHtml::closeTag('ul') . "\n";
            }

            echo CHtml::closeTag('li') . "\n";
        }
    }

    protected function renderMenuItem($item)
    {
        if (isset($item['url'])) {
            $request = Yii::app()->getRequest();
            if (!empty($item['url']['path']) && $request->getQuery('filter')) {
                $item['url']['path'] = Url::appendParam($item['url']['path'], 'filter', $request->getQuery('filter'));
            }

            $label = $this->linkLabelWrapper === null ? $item['label'] : CHtml::tag(
                $this->linkLabelWrapper,
                $this->linkLabelWrapperHtmlOptions,
                $item['label']
            );
            $item['linkOptions'] = isset($item['linkOptions']) ? $item['linkOptions'] : array();
            $item['linkOptions'] = \Yiinitializr\Helpers\ArrayX::merge(
                $item['linkOptions'],
                array('data-category-id' => isset($item['spec']) ? $item['spec'] : '')
            );
            $link = CHtml::link($label, $item['url'], isset($item['linkOptions']) ? $item['linkOptions'] : array());

            if (isset($item['count'])) {
                $link = CHtml::link(
                    '<span>' . $label . '</span>',
                    $item['url'],
                    isset($item['linkOptions']) ? $item['linkOptions'] : array()
                );
                
                if (isset($item['num']) && $item['num'] == 1)
                {
                    $link .= "\n<span class='count tatr menu_item_".$item['spec']."'>(" . $item['count'] . ")</span>";
                }
                else
                {
                    if (isset($item['type_spec']))
                    {
                        $link .= "\n<span class='count count_spec' data-id='".$item['spec']."'>(" . $item['count'] . ")</span>";
                    }
                    else
                    {
                        $link .= "\n<span class='count menu_item_".$item['spec']."'>(" . $item['count'] . ")</span>";
                    }
                }
            }
            return $link;
        } else {
            return CHtml::tag(
                'span',
                isset($item['linkOptions']) ? $item['linkOptions'] : array(),
                $item['label']
            );
        }
    }

}