<?php

class Breadcrumbs extends CWidget {

    /**
     * @var string the tag name for the breadcrumbs container tag. Defaults to 'div'.
     */
    public $tagName = 'div';
    public $labelTemplate = null;
    public $areaTemplate = null;

    /**
     * @var array the HTML attributes for the breadcrumbs container tag.
     */
    public $htmlOptions = array('class' => 'breadcrumbs');

    /**
     * @var boolean whether to HTML encode the link labels. Defaults to true.
     */
    public $encodeLabel = true;

    /**
     * @var string the first hyperlink in the breadcrumbs (called home link).
     * If this property is not set, it defaults to a link pointing to {@link CWebApplication::homeUrl} with label 'Home'.
     * If this property is false, the home link will not be rendered.
     */
    public $homeLink;
    public $home = array(
        'icon' => 'icon-home',
        'label' => 'Главная',
        'url' => array('/admin/main/index'),
    );

    /**
     * @var array list of hyperlinks to appear in the breadcrumbs. If this property is empty,
     * the widget will not render anything. Each key-value pair in the array
     * will be used to generate a hyperlink by calling CHtml::link(key, value). For this reason, the key
     * refers to the label of the link while the value can be a string or an array (used to
     * create a URL). For more details, please refer to {@link CHtml::link}.
     * If an element's key is an integer, it means the element will be rendered as a label only (meaning the current page).
     *
     * The following example will generate breadcrumbs as "Home > Sample post > Edit", where "Home" points to the homepage,
     * "Sample post" points to the "index.php?r=post/view&id=12" page, and "Edit" is a label. Note that the "Home" link
     * is specified via {@link homeLink} separately.
     *
     * <pre>
     * array(
     *     'Sample post'=>array('post/view', 'id'=>12),
     *     'Edit',
     * )
     * </pre>
     */
    public $links = array();

    /**
     * @var string the separator between links in the breadcrumbs. Defaults to ' &raquo; '.
     */
    public $separator = '';
        
    public function run() {
        if (empty($this->links))
            return;
        
        array_unshift($this->links, $this->home);

        
        $active = '';
        foreach ($this->links as $item) {
            //  echo "1";
            // if (is_string($item['label']) || is_array($item['url']))
            // $active = ($item['active'] == true) ? 'blue' : '';

            if (is_string($item['label']) && is_array($item['url'])) {
                $active = 'blue';
                $link = CHtml::link($this->encodeLabel ? CHtml::encode($item['label']) : $item['label'], $item['url']);
            } else {
                $active = '';
                $link = $this->encodeLabel ? CHtml::encode($item['label']) : $item['label'];
            }

            echo " <div class=\"breadcrumb-button  $active\">
                        <span class=\"breadcrumb-label\">
                        <i class='" . $item['icon'] . "'></i> " . $link . "</span>
                        <span class=\"breadcrumb-arrow\"><span></span></span></div>";
        }
    }

}