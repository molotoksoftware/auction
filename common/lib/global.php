<?php
/**
 * This is the shortcut to Yii::app()
 */
function app()
{
    return Yii::app();
}

/**
 * This is the shortcut to Yii::app()->clientScript
 *
 * @return CClientScript
 */
function cs()
{
    // You could also call the client script instance via Yii::app()->clientScript
    // But this is faster
    return Yii::app()->getClientScript();
}

/**
 * This is the shortcut to Yii::app()->user.
 */
function user()
{
    return Yii::app()->getUser();
}

/**
 * This is the shortcut to Yii::app()->createUrl()
 */
function url($route,$params=array(),$ampersand='&')
{
    return Yii::app()->createUrl($route,$params,$ampersand);
}

/**
 * This is the shortcut to CHtml::encode
 */
function h($text)
{
    return htmlspecialchars($text,ENT_QUOTES,Yii::app()->charset);
}

/**
 * This is the shortcut to CHtml::link()
 */
function l($text, $url = '#', $htmlOptions = array())
{
    return CHtml::link($text, $url, $htmlOptions);
}

/**
 * This is the shortcut to Yii::t() with default category = 'stay'
 */
function t($message, $category = 'stay', $params = array(), $source = null, $language = null)
{
    return Yii::t($category, $message, $params, $source, $language);
}

/**
 * This is the shortcut to Yii::app()->request->baseUrl
 * If the parameter is given, it will be returned and prefixed with the app baseUrl.
 */
function bu($url=null)
{
    static $baseUrl;
    if ($baseUrl===null)
        $baseUrl=Yii::app()->getRequest()->getBaseUrl();
    return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');
}


/**
 * This is the shortcut to Yii::app()->theme->baseUrl
 * If the parameter is given, it will be returned and prefixed with the app baseUrl.
 */
function tbu($url=null)
{
    static $baseUrl;
    if ($baseUrl===null)
        $baseUrl=Yii::app()->getTheme()->getBaseUrl();
    return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');
}


/**
 * Returns the named application parameter.
 * This is the shortcut to Yii::app()->params[$name].
 */
function param($name)
{
    return Yii::app()->params[$name];
}

function request() {
    return Yii::app()->getRequest();
}

function dump($target)
{
    return CVarDumper::dump($target, 10, true) ;
}

function sess($key = null, $value = null)
{
    if (!empty ($key) && !empty ($value))
    {
        return Yii::app()->session[$key] = $value;
    }
    elseif (!empty ($key))
    {
        return Yii::app()->session[$key];
    }
    else
    {
        return Yii::app()->session;
    }
}

function printSess()
{
    echo '<pre>';
    foreach (getSessArr() as $key => $value)
    {
        echo '  '.$key .' -> '.$value.'<br/>';
    }
    echo '</pre>';
}

function hasCookie($name)
{
    return !empty(Yii::app()->request->cookies[$name]->value);
}

function getCookie($name)
{
    return Yii::app()->request->cookies[$name]->value;
}

function setCookieGlobal($name, $value)
{
    $cookie = new CHttpCookie($name,$value);
    Yii::app()->request->cookies[$name] = $cookie;
}

function removeCookie($name)
{
    unset(Yii::app()->request->cookies[$name]);
}