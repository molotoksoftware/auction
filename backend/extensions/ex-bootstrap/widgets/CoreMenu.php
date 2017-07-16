<?php

Yii::import('zii.widgets.CMenu');
Yii::import('ex-bootstrap.widgets.DropDownMenu');

class CoreMenu extends CMenu {

    public function getDropdownCssClass() {
        return 'dark-nav';
    }

    protected function renderMenu($items) {
        $n = count($items);

        if ($n > 0) {
            echo CHtml::openTag('ul', $this->htmlOptions);

            $count = 0;
            foreach ($items as $item) {
                $count++;

                $options = isset($item['itemOptions']) ? $item['itemOptions'] : array();
                $classes = array();

                if ($item['active'] && $this->activeCssClass != '')
                    $classes[] = $this->activeCssClass;


                if ($this->itemCssClass !== null)
                    $classes[] = $this->itemCssClass;

                if (isset($item['items']))
                    $classes[] = $this->getDropdownCssClass();


                if (!empty($classes)) {
                    $classes = implode(' ', $classes);
                    if (!empty($options['class']))
                        $options['class'] .= ' ' . $classes;
                    else
                        $options['class'] = $classes;
                }


                echo CHtml::openTag('li', $options);

                
                $menu = $this->renderMenuItem($item);

                if (isset($this->itemTemplate) || isset($item['template'])) {
                    $template = isset($item['template']) ? $item['template'] : $this->itemTemplate;
                    echo strtr($template, array('{menu}' => $menu));
                }
                else
                    echo $menu;

                if (isset($item['items']) && !empty($item['items'])) {
                   
                    if ($item['active'])
                        $item['submenuOptions']['class'].= ' in';


                    $this->controller->widget('DropDownMenu', array(
                        'encodeLabel' => $this->encodeLabel,
                        'htmlOptions' => isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions,
                        'items' => $item['items'],
                    ));
                }

                echo '</li>';
            }

            echo '</ul>';
        }
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please see {@link items} on what data might be in the item.
     * @return string the rendered item
     */
    protected function renderMenuItem($item) {
        if (isset($item['icon'])) {
            if (strpos($item['icon'], 'icon') === false) {
                $pieces = explode(' ', $item['icon']);
                $item['icon'] = 'icon-' . implode(' icon-', $pieces);
            }

            $item['label'] = '<i class="' . $item['icon'] . '"></i> ' . '<span>' . $item['label'];
        }

        if (!isset($item['linkOptions']))
            $item['linkOptions'] = array();


        if (isset($item['items']) && !empty($item['items'])) {
            if (empty($item['url'])) {
                $item['url'] = '#';
            }

            $item['linkOptions']['data-toggle'] = 'collapse';

            $item['linkOptions']['class'] = 'accordion-toggle';
            if ($item['active'] == false) {
                $item['linkOptions']['class'].=' collapsed';
            }
            $item['label'] .= ' <i class="icon-caret-down"></i></span>';
        }

        if (isset($item['url']))
            return CHtml::link($item['label'], $item['url'], $item['linkOptions']);
        else
            return $item['label'];
    }

    /**
     * Normalizes the {@link items} property so that the 'active' state is properly identified for every menu item.
     * @param array $items the items to be normalized.
     * @param string $route the route of the current request.
     * @param boolean $active whether there is an active child menu item.
     * @return array the normalized menu items
     */
    protected function normalizeItems($items, $route, &$active) {
        foreach ($items as $i => $item) {
            if (!is_array($item))
                $item = array('divider' => true);
            else {
                if (!isset($item['itemOptions']))
                    $item['itemOptions'] = array();

                $classes = array();

                if (!isset($item['url']) && !isset($item['items']) && $this->isVertical()) {
                    $item['header'] = true;
                    $classes[] = 'nav-header';
                }

                if (!empty($classes)) {
                    $classes = implode($classes, ' ');
                    if (isset($item['itemOptions']['class']))
                        $item['itemOptions']['class'] .= ' ' . $classes;
                    else
                        $item['itemOptions']['class'] = $classes;
                }
            }

            $items[$i] = $item;
        }

        return parent::normalizeItems($items, $route, $active);
    }

}