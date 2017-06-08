<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
 * @copyright 2016 MolotokSoftware
 * @license GNU General Public License, version 3
 */

/**
 * 
 * This file is part of MolotokSoftware.
 *
 * MolotokSoftware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MolotokSoftware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with MolotokSoftware.  If not, see <http://www.gnu.org/licenses/>.
 */


class SiteController extends FrontController
{

    public function actionIndex()
    {
        $this->layout = 'common';
        $this->pageTitle = '';
        $this->render('index');
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->renderPartial('/system/error', $error);
            }
        }
    }


    /**
     * system
     * @param $sqlFile
     * @return string
     * @throws
     * @throws Exception
     */
    public function execSqlFile($sqlFile)
    {
        $message = "ok";

        if (file_exists($sqlFile)) {
            $sqlArray = file_get_contents($sqlFile);
            $cmd = Yii::app()->db->createCommand($sqlArray);
            try {
                $cmd->execute();
            } catch (CDbException $e) {
                throw new $e;
                $message = $e->getMessage();
            }
        } else {
            throw new Exception('fail');
        }
        return $message;
    }

    public function attribute()
    {
        $attribute = Yii::app()->db->createCommand()
            ->select('name, attribute_id')
            ->from('attribute')
            ->queryAll();

        foreach ($attribute as $attr) {
            Yii::app()->db->createCommand()->update(
                'attribute',
                array(
                    'sys_name' => $attr['name']
                ),
                'attribute_id=:attribute_id',
                array(
                    ':attribute_id' => $attr['attribute_id']
                )
            );
        }

    }

    public function actionImage()
    {
        $dir = Yii::getPathOfAlias('frontend.www.i') . '/';
        $save_path = Yii::getPathOfAlias('frontend.www.ii') . '/';

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (filetype($dir . $file) == 'file') {

                        $org_image = Yii::app()->image->load($dir . $file);

                        $type = 'Auction';
                        foreach ($type::$versions as $v_name => $v_params) {
                            $method = key($v_params);
                            $args = array_values($v_params);


                            call_user_func_array(
                                array($org_image, $method),
                                is_array($args[0]) ? $args[0] : array($args[0])
                            );

                            if ($v_name == 'large' || $v_name == 'big') {

                                $org_image->watermark(
                                    Yii::getPathOfAlias('frontend.www.img') . '/watermark.png',
                                    10,
                                    10
                                );
                            }

                            $org_image->save($save_path . 'thumbs' . DIRECTORY_SEPARATOR . $v_name . '_' . $file);


                        }

                        print "File: $file : type: " . filetype($dir . $file) . "<br>";
                    }
                }
                closedir($dh);
            }
        }

        die();

        $org_image = Yii::app()->image->load($img['file']);
        $type = get_class($model);
        foreach ($type::$versions as $v_name => $v_params) {
            $method = key($v_params);
            $args = array_values($v_params);
            call_user_func_array(
                array($org_image, $method),
                is_array($args[0]) ? $args[0] : array($args[0])
            );
            $org_image->save($save_path . 'thumbs' . DIRECTORY_SEPARATOR . $v_name . '_' . $file);
        }
    }
}