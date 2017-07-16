<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <title><?= Yii::t('basic', 'Oops! Something went wrong')?></title>
    <link rel="icon" href="/favicon.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon"/>
</head>
<body>
<div class="container_404">
    <?php if (isset($message)): ?>
        <?php echo CHtml::encode($message); ?>
    <?php else: ?>
        <p><?= Yii::t('basic', 'Oops! Something went wrong')?></p>
    <?php endif; ?>
    <p><a href="<?= Yii::app()->params['siteUrl']; ?>"><?= Yii::t('basic', 'Please, return to main page')?></a></p>
</div>
</body>
</html>