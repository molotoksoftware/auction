<?php
/**
* SetReturnUrl Filter
*
* Позволяет сохранять текущий url в сессии для всех или выборочных действий
* контроллера, чтобы затем к нему вернуться.
*
* @version 1.0
* @author creocoder <creocoder@gmail.com>
*/

class ESetReturnUrlFilter extends CFilter
{
	protected function preFilter($filterChain)
	{
		Getter::webUser()->setReturnUrl(Yii::app()->getRequest()->getUrl());
		return true;
	}
}