<?php

foreach ($categories as $category) {
    echo CHtml::image($category->getImage(), $category->name);
    
    echo $category->name . "<br>";
    echo $category->count_articles . "<br>";
    echo Yii::app()->createUrl('/articles/articles/view', array('alias' => $category->alias));
}