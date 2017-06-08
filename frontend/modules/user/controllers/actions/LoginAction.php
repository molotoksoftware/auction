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


class LoginAction extends CAction
{
    public function run()
    {
        if (!Yii::app()->user->isGuest) {
            Yii::app()->controller->redirect(Yii::app()->homeUrl);
        }

        $serviceName = Yii::app()->request->getQuery('service');
        if (isset($_POST['returnUrl'])) Yii::app()->user->setReturnUrl($_POST['returnUrl']);

        if (isset($serviceName)) {
            $eauth = Yii::app()->eauth->getIdentity($serviceName);
            $eauth->redirectUrl = Yii::app()->user->returnUrl;
            $eauth->cancelUrl = Yii::app()->controller->createAbsoluteUrl('userlogin');

            if ($eauth->authenticate()) {
                $identity = new ServiceUserIdentity($eauth);

                if ($identity->authenticate()) {
                    $exist = UsersService::model()->exists('service=:service AND service_id=:service_id', [':service' => $identity->service->serviceName, ':service_id' => $identity->service->id]);

                    if ($exist)
                    {
                        Yii::app()->user->login($identity, 3600 * 24 * 30);
                        $this->controller->redirect('/');
                    } else
                    {
                        $login = self::get_in_translate_to_en($identity->service->name);

                        $exist_login = User::model()->exists('login=:login', [':login' => $login]);

                        if ($exist_login) {
                            $login .= rand(1, 9999);
                        }

                        if (isset($_COOKIE['soc_email']) && !empty($_COOKIE['soc_email'])) {
                            $soc_email = $_COOKIE['soc_email'];
                        } else {
                            $soc_email = '';
                        }
                        if (isset($_COOKIE['soc_telephone']) && !empty($_COOKIE['soc_telephone'])) {
                            $soc_telephone = $_COOKIE['soc_telephone'];
                        } else {
                            $soc_telephone = '';
                        }

                        Yii::app()->db->createCommand()->insert('users', [
                            'login'      => $login,
                            'createtime' => date('Y-m-d H:i:s'),
                            'lastvisit'  => date('Y-m-d H:i:s'),
                            'email'      => $soc_email,
                            'telephone'  => $soc_telephone,
                        ]);

                        $user_id = Yii::app()->db->getLastInsertId();

                        $model2 = new UsersService();
                        $model2->id = $user_id;
                        $model2->service = $identity->service->serviceName;
                        $model2->service_id = $identity->service->id;
                        $model2->save();

                        if ($identity->authenticate()) {
                            Yii::app()->user->login($identity, 3600 * 24 * 30);
                            $this->controller->redirect('/');
                        }
                    }
                } else {
                    $this->controller->redirect('/');
                }
            } else {
                $this->controller->redirect('/');
            }
        } else {

            $model = new LoginForm();

            // if it is ajax validation request
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'form-login') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }

            $redirect = Yii::app()->user->getReturnUrl();

            if (empty($redirect)) {
                if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                    Yii::app()->user->setReturnUrl($_SERVER['HTTP_REFERER']);
                }
            }

            // collect user input data
            if (isset($_POST['LoginForm'])) {
                $model->attributes = $_POST['LoginForm'];
                // validate user input and redirect to the previous page if valid
                if ($model->validate() && $model->login()) {
                    $this->onAfterLogin(Getter::userModel(), $model);

                    if (Yii::app()->getUser()->returnUrl && Yii::app()->getUser()->returnUrl != Yii::app()->homeUrl) {
                        Yii::app()->request->redirect(Yii::app()->getUser()->returnUrl);
                    } else {
                        Yii::app()->request->redirect(Yii::app()->homeUrl);
                    }
                }
            }
        }

        if (!isset($serviceName)) {
            $this->controller->render('login', ['model' => $model]);
        }
    }

    /**
     * @param User      $user
     * @param LoginForm $model
     */
    private function onAfterLogin(User $user, LoginForm $model)
    {
        $event = new AfterLoginEvent($this);
        $event->setUser($user);
        $event->setLogin($model->login);
        $event->setPassword($model->password);
        $this->getController()->onAfterLogin($event);
    }

    public static function get_in_translate_to_en($string, $gost = false)
    {
        if ($gost) {
            $replace = [" " => "_", "А" => "A", "а" => "a", "Б" => "B", "б" => "b", "В" => "V", "в" => "v", "Г" => "G", "г" => "g", "Д" => "D", "д" => "d",
                        "Е" => "E", "е" => "e", "Ё" => "E", "ё" => "e", "Ж" => "Zh", "ж" => "zh", "З" => "Z", "з" => "z", "И" => "I", "и" => "i",
                        "Й" => "I", "й" => "i", "К" => "K", "к" => "k", "Л" => "L", "л" => "l", "М" => "M", "м" => "m", "Н" => "N", "н" => "n", "О" => "O", "о" => "o",
                        "П" => "P", "п" => "p", "Р" => "R", "р" => "r", "С" => "S", "с" => "s", "Т" => "T", "т" => "t", "У" => "U", "у" => "u", "Ф" => "F", "ф" => "f",
                        "Х" => "Kh", "х" => "kh", "Ц" => "Tc", "ц" => "tc", "Ч" => "Ch", "ч" => "ch", "Ш" => "Sh", "ш" => "sh", "Щ" => "Shch", "щ" => "shch",
                        "Ы" => "Y", "ы" => "y", "Э" => "E", "э" => "e", "Ю" => "Iu", "ю" => "iu", "Я" => "Ia", "я" => "ia", "ъ" => "", "ь" => ""];
        } else {
            $arStrES = ["ае", "уе", "ое", "ые", "ие", "эе", "яе", "юе", "ёе", "ее", "ье", "ъе", "ый", "ий"];
            $arStrOS = ["аё", "уё", "оё", "ыё", "иё", "эё", "яё", "юё", "ёё", "её", "ьё", "ъё", "ый", "ий"];
            $arStrRS = ["а$", "у$", "о$", "ы$", "и$", "э$", "я$", "ю$", "ё$", "е$", "ь$", "ъ$", "@", "@"];

            $replace = [" " => "_", "А" => "A", "а" => "a", "Б" => "B", "б" => "b", "В" => "V", "в" => "v", "Г" => "G", "г" => "g", "Д" => "D", "д" => "d",
                        "Е" => "Ye", "е" => "e", "Ё" => "Ye", "ё" => "e", "Ж" => "Zh", "ж" => "zh", "З" => "Z", "з" => "z", "И" => "I", "и" => "i",
                        "Й" => "Y", "й" => "y", "К" => "K", "к" => "k", "Л" => "L", "л" => "l", "М" => "M", "м" => "m", "Н" => "N", "н" => "n",
                        "О" => "O", "о" => "o", "П" => "P", "п" => "p", "Р" => "R", "р" => "r", "С" => "S", "с" => "s", "Т" => "T", "т" => "t",
                        "У" => "U", "у" => "u", "Ф" => "F", "ф" => "f", "Х" => "Kh", "х" => "kh", "Ц" => "Ts", "ц" => "ts", "Ч" => "Ch", "ч" => "ch",
                        "Ш" => "Sh", "ш" => "sh", "Щ" => "Shch", "щ" => "shch", "Ъ" => "", "ъ" => "", "Ы" => "Y", "ы" => "y", "Ь" => "", "ь" => "",
                        "Э" => "E", "э" => "e", "Ю" => "Yu", "ю" => "yu", "Я" => "Ya", "я" => "ya", "@" => "y", "$" => "ye"];

            $string = str_replace($arStrES, $arStrRS, $string);
            $string = str_replace($arStrOS, $arStrRS, $string);
        }

        return iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));
    }
}