<?php
/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://www.molotoksoftware.com/
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

class Notification
{

    // модуль Support
            // константы


    protected $userId;
    public $text;
    public $type;
    public $params;

    /**
     * @param int   $userId
     * @param array $params
     * @param int   $type
     */
    
    public function __construct($userId, $params, $type)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->params = $params;
        $this->text = $this->getText();
    }

    public function getUser()
    {
        return User::model()->findByPk($this->userId);
    }

    public function send()
    {
        if ($user = $this->getUser()) {
            $this->systemNotify();
            if (!empty($user->email) && ($user->consent_receive_notification == 1)) {
                $this->emailNotify(
                    $user->email,
                    ['text' => $this->text]
                );
            }
        } else {
            Yii::log('ошибка при отправлении уведомления');
        }
    }

    public function systemNotify()
    {

        $model = new SystemNotification();
        $model->text = $this->text;
        $model->user_id = $this->getUser()->user_id;
        $model->type = $this->type;
        if ($model->save()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $toEmail
     * @param array  $params
     */
    public function emailNotify($toEmail, $params)
    {
        $user = $this->getUser();
        if ($user !== false) {
            if (!empty($user->firstname) && !empty($user->lastname)) {
                $fullUserName = $user->firstname . ' ' . $user->lastname;
                $params = array_merge($params, array('userName' => $fullUserName));
            } else {
                $params = array_merge($params, array('userName' => $user->login));
            }
        }
        
        $message = new EmailMessage();
        $message->setView('common');
        $message->setSubject($this->getSubject());
        $message->setBody($params, 'text/html');
        $message->addTo($toEmail);
        $message->setFrom([Yii::app()->params['adminEmail'] => CHtml::encode(Yii::app()->params['adminName'])]);
        Getter::emailQueue()->add($message);
    }

    public function getFile($template)
    {
        $fileTemplate = Yii::getPathOfAlias('backend.components.notification.templates') . '/' . $template . '.php';
        if (file_exists($fileTemplate)) {
            return $fileTemplate;
        }
        throw new Exception('not found file');
    }

    public function renderFile($_viewFile_, $_data_ = null, $_return_ = false)
    {
        if (is_array($_data_)) {
            extract($_data_, EXTR_PREFIX_SAME, 'data');
        } else {
            $data = $_data_;
        }
        if ($_return_) {
            ob_start();
            ob_implicit_flush(false);
            require($_viewFile_);
            return ob_get_clean();
        } else {
            require($_viewFile_);
        }
    }

    public function getRendererFile($file, $data)
    {
        return $this->renderFile($file, $data, true);
    }

    public function getText()
    {
        switch ($this->type) {

        //    case self::TYPE_SUPPORT_NEW_ANSWER:
        //        return $this->getRendererFile($this->getFile('имя_шаблона'), $this->params);
        //        break;


            default:
                return '';
                break;
        }
    }

    // Формируем тему сообщения
    public function getSubject()
    {

        switch ($this->type) {
       //     case self::TYPE_SUPPORT_NEW_ANSWER:
       //         return 'Заголовок письма;
       //         break;
        }
    }
}