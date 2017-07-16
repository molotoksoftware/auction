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

/**
 * This is the model class for table "users".
 *
 * @property string   $user_id
 * @property string   $login
 * @property string   $firstname
 * @property string   $lastname
 * @property string   $birthday
 * @property string   $avatar
 * @property string   $about
 * @property string   $email
 * @property string   $telephone
 * @property string   $show_telephone
 * @property string   $password
 * @property string   $add_contact_info
 * @property integer  $status
 * @property string   $createtime
 * @property string   $lastvisit
 * @property integer  $rating
 * @property integer  $consent_receive_notification
 * @property integer  $balance
 * @property string   $pro
 * @property string   $certified
 * @property int|null $id_city
 * @property int|null $id_region
 * @property int|null $id_country
 * @property string   $nick
 * @property string   $update
 * @property string   $last_ip_addr IP с которого юзер заходил последний раз на сайт.

 *

 */
class User extends CActiveRecord {

    public $is_change_balance;
    public $balance_comment;
    public $changeBalance;

    const SAVE_PATH = 'frontend.www.images.users';
    const PRO_ACCOUNT = 1;
    const STANDARD_ACCOUNT = 0;

    private static $unreadNotificationsCount;
    public static $thumbs = [
        'avatar_mini' => [
            'centeredpreview' => [
                'width' => 38,
                'height' => 38,
            ],
        ],
        'preview' => [
            'centeredpreview' => [
                'width' => 120,
                'height' => 120,
            ],
        ],
        'medium' => [
            'centeredpreview' => [
                'width' => 400,
                'height' => 400,
            ],
        ],
        'avatar' => [
            'centeredpreview' => [
                'width' => 208,
                'height' => 208,
            ],
        ]
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            ['login, email', 'required'],
            ['password', 'required', 'on' => 'insert'],
            ['status, rating, id_country, id_region, id_city', 'numerical', 'integerOnly' => true],
            ['login, firstname, lastname, avatar, email, password', 'length', 'max' => 255],
            ['login, email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['add_contact_info', 'length', 'max' => 512],
            ['terms_delivery', 'length', 'max' => 2048],
            ['show_telephone, consent_receive_notification', 'boolean'],
            ['login, email', 'unique'],
            ['certified, ban', 'numerical', 'integerOnly' => true],
            [
                'login',
                'match',
                'pattern' => '/^[A-Za-z0-9_\-]{2,50}$/',
                'message' => 'Допустимы только буквы латинского алфавита и цифры. НИК на кириллице можно будет задать в настройках.'
            ],
            ['about', 'length', 'max' => 10000, 'tooLong' => 'Слишком длинный текст (максимум: 10000 симв.).'],
            ['telephone', 'length', 'max' => 20],
            ['createtime, lastvisit, last_ip_addr', 'safe'],
            [
                'balance_comment',
                'required',
                'message' => 'При изменении баланса необходимо написать комментарий',
                'on' => 'changeBalance'
            ],
            ['changeBalance', 'numerical'],
            ['is_change_balance', 'boolean'],
            ['balance_comment', 'length', 'max' => 255],
            ['nick', 'length', 'min' => 3, 'max' => 25],
            [
                'user_id, certified, ban, is_change_balance, login, firstname, lastname, birthday, avatar, about, email, telephone, password, status, createtime, lastvisit, rating, update',
                'safe',
                'on' => 'search'
            ],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return [
            'count_lots' => [self::STAT, 'BaseAuction', 'owner'],
            'userCommon' => [self::HAS_ONE, 'UserCommon', 'user_id'],
        ];
    }

    public function behaviors() {
        return [
            'uploadedFile' => [
                'class' => 'backend.extensions.simpleImageUpload.SimpleImageUploadBehavior',
                'attributeName' => 'avatar',
                'savePathAlias' => self::SAVE_PATH,
                'versions' => self::$thumbs
            ]
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return [
            'login' => 'Логин',
            'nick' => 'Ник',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'birthday' => 'Дата рождения',
            'avatar' => 'Аватар',
            'about' => 'Краткая информация о себе',
            'email' => 'E-mail',
            'telephone' => 'Telephone',
            'password' => 'Password',
            'status' => 'Статус',
            'createtime' => 'Дата регистрации',
            'rating' => 'Рейтинг',
            'changeBalance' => 'Значения',
            'balance_comment' => 'Комментарий',
            'certified' => 'Проверенный продавец',
            'ban' => 'Бан',
        ];
    }

    /**
     * @return CActiveDataProvider the data provider that can return the models
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('login', $this->login, true);
        $criteria->compare('firstname', $this->firstname, true);
        $criteria->compare('lastname', $this->lastname, true);
        $criteria->compare('birthday', $this->birthday, true);
        $criteria->compare('avatar', $this->avatar, true);
        $criteria->compare('about', $this->about, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('telephone', $this->telephone, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('createtime', $this->createtime, true);
        $criteria->compare('lastvisit', $this->lastvisit, true);
        $criteria->compare('rating', $this->rating);
        $criteria->compare('update', $this->update, true);
        $criteria->compare('certified', $this->certified, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
    }

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function validatePassword($password) {
        return CPasswordHelper::verifyPassword($password, $this->password);
    }

    public function hashPassword($password) {
        return CPasswordHelper::hashPassword($password);
    }

    public function beforeValidate() {
        //при изменении баланса
        if ($this->is_change_balance == 1) {
            $this->setScenario('changeBalance');
        }

        return parent::beforeValidate();
    }

    public function beforeSave() {
        if (!$this->isNewRecord) {
            if ($this->is_change_balance == 1) {
                if ($this->changeBalance != 0) {
                    if ($this->changeBalance > 0) {
                        $this->moneyAdd($this->changeBalance, $this->balance_comment, BalanceHistory::STATUS_ADD);
                    } else {
                        $this->moneySub(($this->changeBalance * -1), $this->balance_comment);
                    }
                }
            }
        }
        if ($this->nick == false) {
            $this->nick = null;
        }

        return parent::beforeSave();
    }

    /**
     * *************************************************************************
     * API
     * *************************************************************************
     */

    /**
     * @return string
     */
    public function getFullName() {
        $data = [];
        $separator = ' ';
        $data[] = $this->firstname;
        $data[] = $this->lastname;

        return implode($separator, $data);
    }

    public function getNameForEmail() {
        if (empty($this->firstname) || empty($this->lastname)) {
            return $this->login;
        } else {
            $this->getFullName();
        }
    }

    /**
     * @param array $htmlOptions
     *
     * @return string
     */
    public function getLink($htmlOptions = []) {
        return CHtml::link(
                        $this->nick ? $this->nick : $this->login, Yii::app()->createAbsoluteUrl('/' . $this->login), $htmlOptions
        );
    }

    public function getNickOrLogin() {
        return $this->nick ? $this->nick : $this->login;
    }

    public function getUrl() {
        return Yii::app()->createAbsoluteUrl('/' . $this->login);
    }

    public static function getByEmail($email) {
        return User::model()->find('email=:email', [':email' => $email]);
    }

    public function outName() {
        return self::outUName($this->nick, $this->login);
    }

    public static function outUName($nick, $login) {
        if ($nick)
            return CHtml::encode($nick);
        return CHtml::encode($login);
    }

    /**
     * @param $login
     *
     * @return User
     * @throws CHttpException
     */
    public static function getByLogin($login) {
        $user = self::model()->find(
                'login=:login', [
            ':login' => $login
                ]
        );

        if ($user == false) {
            throw new CHttpException(404);
        }
        return $user;
    }

    /**
     * @param $minutes
     *
     * @return array
     * возвращает всех неактивных(по истечению определенного времени) пользователей
     */
    public static function getNoActives($minutes) {
        $dateB = new DateTime('now');
        $interval = new DateInterval('PT' . (int) $minutes . 'M');
        $dateA = $dateB->sub($interval);

        return Yii::app()->db->createCommand()
                        ->select('user_id')
                        ->from('users')
                        ->where(
                                'DATE_FORMAT(lastvisit, "%Y-%m-%d %H:%i:%s")<=:check_date and online=1', [':check_date' => $dateA->format('Y-m-d H:i:s')]
                        )
                        ->queryAll();
    }

    /**
     * @return boolean
     */
    public function isOnline() {
        return ($this->online) ? true : false;
    }

    public function getBalance($decimals = 0) {

        $webUser = Getter::webUser();

        return $this->balance;

    }

    /**
     * @param float|int $amount
     * @param string    $description
     * @param string    $type
     */
    public function moneyAdd($amount, $description, $type) {
        $this->balance += (float) $amount;
        /**
         * @var $balance_history BalanceHistory
         */
        $balance_history = new BalanceHistory();
        $balance_history->user_id = $this->user_id;
        $balance_history->description = $description;
        $balance_history->type = (int) $type;
        $balance_history->summa = (float) $amount;
        $balance_history->save(false);

        //  Yii::log('add balance on ' . $amount . ' user id' . $this->user_id);
    }

    /**
     * @param float|int $amount
     * @param string    $message
     * @param int       $type
     */
    public function moneySub($amount, $message, $type = BalanceHistory::STATUS_SUB) {
        $this->balance -= $amount;
        /**
         * @var $balance_history BalanceHistory
         */
        $balance_history = new BalanceHistory();
        $balance_history->user_id = $this->user_id;
        $balance_history->description = $message;

        //'Пополнения баланса';

        $balance_history->type = $type;
        $balance_history->summa = floatval($amount);
        $balance_history->save(false);

        Yii::log('sub balance on ' . $amount . ' user id' . $this->user_id);
    }

    /**
     * @return bool
     */
    public function getIsPro() {
        return $this->pro == self::PRO_ACCOUNT;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param bool|true $createIfNotExist
     *
     * @return UserCommon|null
     */
    public function getCommonData($createIfNotExist = true) {
        $userCommon = $this->userCommon;
        if (!$userCommon && $createIfNotExist && !$this->getIsNewRecord()) {
            $userCommon = new UserCommon();
            $userCommon->user_id = $this->getPrimaryKey();
            $userCommon->save(false);
            $userCommon->refresh();
            return $userCommon;
        }
        return $userCommon;
    }

    /**
     * Получить название папки в которую сохраняются фото лотов юзера.
     * Важно: Использовать для юзера у которого определен ID.
     *
     * @return string
     * @throws CException
     */
    public function getImagesPath() {
        $pk = $this->getPrimaryKey();
        if (empty($pk)) {
            throw new CException('Primary key must be filled! Use $this->getPrimaryKey().');
        }

        return Yii::getPathOfAlias('frontend.www.i2') . '/' . $this->getPrimaryKey();
    }

    /**
     * @return string
     * @throws CException
     */
    public function getImageThumbsPath() {
        return $this->getImagesPath() . '/thumbs';
    }

    /**
     * @param array       $ids
     * @param string      $columns
     * @param null|string $indexBy
     * @param bool|true   $asArray
     *
     * @return array
     * @throws CDbException
     */
    public static function getByIds($ids, $columns = '*', $indexBy = null, $asArray = true) {
        return ActiveRecord::findAllByIds(
                        self::model()->tableName(), 'user_id', $ids, $columns, $indexBy, $asArray
        );
    }

    /**
     * @param bool|false $refresh
     *
     * @return string
     */
    public function getUnreadNotificationsCount($refresh = false) {
        if (self::$unreadNotificationsCount === null || $refresh) {
            self::$unreadNotificationsCount = SystemNotification::model()
                    ->byUserId($this->getPrimaryKey())
                    ->getByStatus(0)
                    ->count();
        }
        return self::$unreadNotificationsCount;
    }


    public function getTimeLastVisit() {
        $visit = strtotime($this->lastvisit);
        $min5 = time() - 300;
        $today = strtotime(date('d-m-Y'));
        $yesterday = $today - 3600 * 24;
        $time_text = date('d.m.Y H:i', $visit);

        if ($visit >= $min5) {
            $time_text = '<span class="label label-success">'.Yii::t('basic', 'Online').'</span>';
        }
        if ($visit >= $today && $visit < $min5) {
            $time_text = Yii::t('basic', 'Today at').' ' . date('H:i', $visit);
        }
        if ($visit >= $yesterday && $visit < $today && $visit < $min5) {
            $time_text = Yii::t('basic', 'Yesterday at').' ' . date('H:i', $visit);
        }

        return $time_text;
    }

}
