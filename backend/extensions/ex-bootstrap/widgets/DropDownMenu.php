<?php

class DropDownMenu extends CMenu {

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
//
//                    if (isset($item['items']))
//                        $classes[] = $this->getDropdownCssClass();

//                    if (isset($item['disabled']))
//                        $classes[] = 'disabled';

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
                        $this->controller->widget('bootstrap.widgets.TbDropdown', array(
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
     * Initializes the widget.
     */
    public function init() {
		$route=$this->getController()->getRoute();
		$this->items=$this->normalizeItems($this->items,$route,$hasActiveChild);
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

            $item['label'] = '<i class="' . $item['icon'] . '"></i> ' . $item['label'];
        }

        if (!isset($item['linkOptions']))
            $item['linkOptions'] = array();

        if (isset($item['items']) && !empty($item['items']))
            $item['url'] = '#';

        $item['linkOptions']['tabindex'] = -1;

        if (isset($item['url']))
            return CHtml::link($item['label'], $item['url'], $item['linkOptions']);
        else
            return $item['label'];
    }

//    public function getDropdownCssClass() {
//        return 'dropdown-submenu';
//    }


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
