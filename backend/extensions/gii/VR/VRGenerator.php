<?php
/**
 * View Renderer Generator.
 *
 * @author Stefan Volkmar <volkmar_yii@email.de>
 * @link http://www.yiiframework.com/extension/yii-generator-collection/
 * @license BSD
 */
class VRGenerator extends CCodeGenerator
{
    public $codeModel='ext.gtc.VR.VRCode';

    public function actionPreview()
    {
        $parser=new CMarkdownParser;
        echo $parser->safeTransform($_POST['data']);
    }
}
?>