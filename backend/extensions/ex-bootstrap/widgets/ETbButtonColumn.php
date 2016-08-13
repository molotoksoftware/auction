<?php
/**
 * переопределение класса для критерия отображения стандартной кнопки delete
 */

Yii::import('bootstrap.widgets.TbButtonColumn');

/**
 * В клас добавлен функцыонал
 */

class ETbButtonColumn extends TbButtonColumn
{
	
	public $deleteButtonVisible;

	/**
	 * Initializes the default buttons (view, update and delete).
	 */
        public function init()
	{
            parent::init();
            
            if(isset($this->buttons['delete']) && isset($this->deleteButtonVisible))
			$this->buttons['delete']['visible'] = $this->deleteButtonVisible;
 
   
 
           
        }
        
	
	protected function renderButton($id,$button,$row,$data)
	{

		if(!isset($button['visible']) || $button['visible']===null)
		{
			parent::renderButton($id,$button,$row,$data);
		}
		else
		{
			if(is_string($button['visible']))
				$button['visible']=$this->evaluateExpression("'" . $button['visible']."'", array('data'=>$data,'row'=>$row));

			if($button['visible'])
				parent::renderButton($id,$button,$row,$data);
		}
	}
}