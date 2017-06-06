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

class Notification
{

    const TYPE_NEW_ASK = 1; //новый вопрос
    const TYPE_RATE_SLAUGHTERED = 3; //[Активные ставки] ставка перебита
    const TYPE_WINNER_AUCTION = 4; //[История покупок]победителем в торгах по лоту
    const TYPE_ACTIVE_LOTS = 5; //[Активные лоты] По вашему лоту появилась новая ставка 
    const TYPE_COMPLETED_WINNER_LOT = 6; //[Проданные лоты.] Торги по Вашему лоту
    const TYPE_NOT_WON_BIDDING_LOT = 7; //[Покупки] Не выигранные. Торги по лоту
    const TYPE_COMPLETED_LOT = 13; //[Завершенные лоты.] Торги по Вашему лоту завершены. !!! ВЫКЛЮЧЕНО В emailNotify()
    const TYPE_REM_EXPIRY_PRO = 14; //[Напоминания] Действия ПРО-аккаунта завершится ...
    const TYPE_ACTIVE_LOTS_BIDS = 21;
    const TYPE_REVIEW = 24; // Уведомления об оставленном отзыве на один лот.
    const TYPE_REVIEW_MULTIPLE = 25; // Уведомления об оставленном отзыве на неколько лотов.

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
            if (!empty($user->email) && ($user->consent_receive_notification == 1) && ($user->ban != 1)) {
                $this->emailNotify(
                    $user->email,
                    ['text' => $this->text]
                );
            }
        } else {
            Yii::log('Error sending notification');
        }
    }

    public function systemNotify()
    {
        if($this->type == self::TYPE_COMPLETED_LOT)
            return true;

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
        if($this->type == self::TYPE_COMPLETED_LOT)
            return;

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
        $fileTemplate = Yii::getPathOfAlias('frontend.components.notification.templates') . '/' . $template . '.php';
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
            case self::TYPE_NEW_ASK:
                return $this->getRendererFile($this->getFile('newAsk'), $this->params);
                break;
            case self::TYPE_RATE_SLAUGHTERED:
                $data = [
                    'linkItem'       => $this->params['linkItem'],
                    'linkActiveRate' => Yii::app()->createAbsoluteUrl('/user/shopping/activeBets'),
                    'bet'            => $this->params['bet'],
                ];
                return $this->getRendererFile($this->getFile('rateSlaughtered'), $data);
                break;
            //[История покупок]победителем в торгах по лоту
            case self::TYPE_WINNER_AUCTION:
                return $this->getRendererFile($this->getFile('winnerAuction'), $this->params);
                break;
            //[Активные лоты] По вашему лоту появилась новая ставка 
            case self::TYPE_COMPLETED_WINNER_LOT:
                return $this->getRendererFile($this->getFile('completedWinnerLot'), $this->params);
                break;
            //[Покупки] Не выигранные. Торги по лоту
            case self::TYPE_NOT_WON_BIDDING_LOT:
                return $this->getRendererFile($this->getFile('notWonBiddingLot'), $this->params);
                break;
            //[Активные лоты] По вашему лоту появилась новая ставка 
            case self::TYPE_ACTIVE_LOTS:
                return $this->getRendererFile($this->getFile('activeLots'), $this->params);
                break;
            //[Активные лоты] По вашему лоту появились новые ставки
            case self::TYPE_ACTIVE_LOTS_BIDS:
                return $this->getRendererFile($this->getFile('activeLotsBids'), $this->params);
                break;
            case self::TYPE_COMPLETED_LOT:
                return $this->getRendererFile($this->getFile('completedLot'), $this->params);
                break;
            case self::TYPE_REM_EXPIRY_PRO:
                return $this->getRendererFile($this->getFile('remExpiryPro'), $this->params);
                break;
            case self::TYPE_REVIEW:
                return $this->getRendererFile($this->getFile('review'), $this->params);
                break;
            case self::TYPE_REVIEW_MULTIPLE:
                return $this->getRendererFile($this->getFile('review_multiple'), $this->params);
                break;


            default:
                return '';
                break;
        }
    }


    public function getSubject()
    {
        $lotName = '';
        if (isset($this->params['linkItem'])) {
            $lotName = strip_tags($this->params['linkItem']);
        }
        $lotId = '';
        if (isset($this->params['lotModel'])) {
            /** @var Auction $lotModel */
            $lotModel = $this->params['lotModel'];
            $lotId = $lotModel->getPrimaryKey();
            if (empty($lotName)) {
                $lotName = $lotModel->name;
            }
        }

        switch ($this->type) {
            case self::TYPE_NEW_ASK:
                return Yii::t('mail', 'In your item {item} {item_title}, a new question.',[
                    '{item}' => $lotId,
                    '{item_title}' => iconv_substr($lotName, 0, 50, 'UTF-8'),
                    ]);
                break;
            case self::TYPE_WINNER_AUCTION:
                return 'Вы стали победителем в торгах по лоту '.$lotId.' "'.iconv_substr($lotName, 0, 50, 'UTF-8').'..."';
                break;
            case self::TYPE_COMPLETED_WINNER_LOT:
                return 'Ваш лот '.$lotId.' "'.iconv_substr($lotName, 0, 50, 'UTF-8').'..." куплен';
                break;
            case self::TYPE_ACTIVE_LOTS:
                return 'По вашему лоту '.$lotId.' "'.iconv_substr($lotName, 0, 50, 'UTF-8').'..." появилась новая ставка';
                break;
            case self::TYPE_RATE_SLAUGHTERED:
                return 'Ваша ставка по лоту '.$lotId.' "'.iconv_substr($lotName, 0, 50, 'UTF-8').'..." перебита';
                break;
            case self::TYPE_COMPLETED_LOT:
                return 'Торги по Вашему лоту "'.iconv_substr($lotName, 0, 50, 'UTF-8').'..." завершились';
                break;
            case self::TYPE_REVIEW:
                return 'Ваш контрагент высказал о Вас мнение по лоту "' . $lotName . '"';
                break;
            case self::TYPE_REVIEW_MULTIPLE:
                return 'Ваш контрагент высказал о Вас мнение по нескольким лотам';
                break;


            default:
                return Yii::app()->params['siteName'];
                break;
        }
    }
}
