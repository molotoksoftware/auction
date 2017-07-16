'image'=>array(
            'class'=>'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver'=>'GD',
            // ImageMagick setup path
            'params'=>array('directory'=>'D:/Program Files/ImageMagick-6.4.8-Q16'),
        ),

$image = Yii::app()->image->load('images/test.jpg');
$image->resize(400, 100)->rotate(-45)->quality(75)->sharpen(20);
$image->save(); // or $image->save('images/small.jpg');


Yii::import('application.extensions.image.Image');
$image = new Image('images/test.jpg');
$image->resize(400, 100)->rotate(-45)->quality(75)->sharpen(20);
$image->render();

//Отразить изображение горизонтально или вертикально.
flip($direction)

//Изменение качества изображения.
quality(0..100)

//Резкость изображения.
sharpen($amount = 20)

//
centeredpreview($width, $height)

//соответствовать
fit($width, $height)

Изменить размер изображения к определенной ширины и высоты. По умолчанию будет Кохана
      * Сохранить пропорции используя ширину в качестве главного измерение. Если вы
      * Хотите использовать высоту, овладения Дим, установить $ Image-> = master_dim изображения :: Рост
      * Этот метод может использоваться в цепочке.

cresize($width, $height, $master = NULL)

//watermark($path, $x, $y)