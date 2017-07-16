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


class SettingsController extends FrontController
{

    public function filters()
    {
        return [
            'accessControl',
            'postOnly + registration',
            'ajaxOnly + registration',
        ];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => [
                    'common', 'uploadAvatar', 'update_info', 'certified',
                    'aboutMe', 'notifications', 'access', 'bulkUpdates',
                ],
                'users'   => ['@'],
            ],
            ['deny'],
        ];
    }


    public function actionCommon()
    {
        $this->pageTitle = Yii::t('basic', 'Settings');

        $this->layout = '//layouts/settings';
        /** @var User $user */
        $user = User::model()->findByPk(Yii::app()->user->id);
        $userAttributes = $user->getAttributes();


        $form = new EditUserForm();
        $form->setAttributes($userAttributes);
        $form->user = $user;
        $oldNick = $user->nick;

        $this->performAjaxValidation($form, 'form-edit-user');

        if (isset($_POST['EditUserForm'])) {
            $form->attributes = $_POST['EditUserForm'];
            if ($form->validate()) {
                $user->setAttributes($form->getAttributes());

                if ($user->save()) {
                    if ($user->nick && $oldNick != $user->nick) {
                        $this->onAfterNickUpdate($user);
                    }
                    Yii::app()->user->setFlash('success-edit-profile', Yii::t('basic', 'Successfully updated'));
                    $this->refresh();
                } else {
                    if ($user->getError('email')) {
                        $form->addError('email', Yii::t('basic', 'E-mail is not available'));
                    }
                }
            }
        }
        $this->render('common', [
            'model' => $form,
            'user'  => $user,
        ]);
    }

    public function onAfterNickUpdate(User $user)
    {
        $event = new AfterNickUpdateEvent();
        $event->setUser($user);
        $this->raiseEvent('onAfterNickUpdate', $event);
    }

    public function actionAboutMe()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();
        $this->pageTitle = Yii::t('basic', 'About me');
        $this->layout = '//layouts/settings';

        /** @var User $user */
        $user = User::model()->findByPk(Yii::app()->user->id);

        if ($request->getIsPostRequest() && $request->getPost('User')) {
            $userData = $request->getPost('User');
            $user->setAttribute('about', $userData['about']);
            if ($user->save(true, ['about'])) {
                Yii::app()->user->setFlash('success-edit-profile', Yii::t('basic', 'Successfully updated'));
                $this->refresh();
            }
        }

        $this->render('aboutMe', [
            'user' => $user,
        ]);
    }

    public function actionNotifications()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();
        $this->pageTitle = Yii::t('basic', 'Notifications e-mail');
        $this->layout = '//layouts/settings';

        /** @var User $user */
        $user = User::model()->findByPk(Yii::app()->user->id);

        if ($request->getIsPostRequest() && $request->getPost('User')) {
            $userData = $request->getPost('User');
            $user->setAttribute('consent_receive_notification', $userData['consent_recive_notification']);
            if ($user->save(true, ['consent_receive_notification'])) {
                Yii::app()->user->setFlash('success-edit-profile', Yii::t('basic', 'Successfully updated'));
                $this->refresh();
            }
        }

        $this->render('common_parts/notifications', [
            'user' => $user,
        ]);
    }

    public function actionAccess()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();
        $this->pageTitle = Yii::t('basic', 'Access control');
        $this->layout = '//layouts/settings';

        /** @var User $user */
        $user = User::model()->findByPk(Yii::app()->user->id);

        $form = new EditUserPasswordForm();
        $form->setAttributes($user->getAttributes());
        $form->user = $user;

        if ($request->getIsPostRequest() && $request->getPost('EditUserPasswordForm')) {
            $formData = $request->getPost('EditUserPasswordForm');
            $form->setAttributes($formData);

            if ($form->validate()) {
                if (!empty($form->passwordNew)) {
                    $user->password = $user->hashPassword($form->passwordNew);
                    if ($user->update(['password'])) {
                        $this->onAfterPasswordUpdate($user, $form->passwordNew);
                        Yii::app()->user->setFlash('success-edit-profile', Yii::t('basic', 'Successfully updated'));
                    }
                    $this->refresh();
                }
            }
        }

        $this->render('common_parts/access', [
            'model' => $form,
            'user'  => $user,
        ]);
    }

    public function onAfterPasswordUpdate(User $user, $password)
    {
        $event = new AfterPasswordUpdateEvent();
        $event->setUser($user);
        $event->setPassword($password);
        $this->raiseEvent('onAfterPasswordUpdate', $event);
    }

    public function actionBulkUpdates()
    {
        /** @var CHttpRequest $request */
        $request = Yii::app()->getRequest();
        $user = Getter::userModel();
        $this->pageTitle = Yii::t('basic', 'Bulk updates');
        $this->layout = '//layouts/settings';

        $minPricePercents = -99;
        $maxPricePercents = 500;

        $errors = [];
        if ($request->getIsPostRequest()) {
            $done = false;

            $republish = $request->getPost('switch_auto_republish');
            if ($republish && in_array($republish, ['y', 'n'])) {
                Yii::app()
                    ->getDb()
                    ->createCommand()
                    ->update(
                        Auction::model()->tableName(),
                        ['is_auto_republish' => $republish == 'y' ? 1 : 0],
                        'owner = :owner',
                        [':owner' => $user->user_id]
                    );
                $done = true;
            }

            if ($request->getPost('price_update')) {
                $percent = (int)$request->getPost('price_update');
                if ($percent >= $minPricePercents && $percent <= $maxPricePercents) {
                    $operator = $percent < 0 ? '-' : '+';
                    $percent = abs($percent) / 100;
                    $query = "
                    UPDATE auction
                    SET
                      price          = CASE
                                       WHEN price != '' AND price {$operator} (price * {$percent}) < 1
                                         THEN 1
                                       WHEN price != '' AND price {$operator} (price * {$percent}) > 99000000
                                         THEN 99000000
                                       WHEN price != ''
                                         THEN price {$operator} (price * {$percent})
                                       ELSE price
                                       END,
                      starting_price = CASE
                                       WHEN type_transaction = 0 AND starting_price {$operator} (starting_price * {$percent}) < 1
                                        THEN 1
                                       WHEN type_transaction = 0 AND starting_price {$operator} (starting_price * {$percent}) > 99000000
                                        THEN 99000000
                                       WHEN type_transaction = 0 AND starting_price {$operator} (starting_price * {$percent}) > 0.99 AND starting_price {$operator} (starting_price * {$percent}) < 99000000.01
                                        THEN starting_price {$operator} (starting_price * {$percent})
                                       ELSE starting_price
                                       END
                    WHERE
                      owner = :owner AND status != 10 AND bid_count = 0 AND sales_id = 0 AND type_transaction IN (0, 1)
                    ";
                    Yii::app()
                        ->getDb()
                        ->createCommand($query)
                        ->query([
                            ':owner' => $user->user_id,
                        ]);

                    $done = true;
                }
            }

            if ($done) {
                Getter::webUser()->setFlash('success_bulk_update', Yii::t('basic', 'Successfully updated'));
            }
            $this->refresh();
        }

        $this->render('common_parts/bulk_updates', [
            'user'             => $user,
            'errors'           => $errors,
            'minPricePercents' => $minPricePercents,
            'maxPricePercents' => $maxPricePercents,
        ]);
    }

    public function actionUploadAvatar()
    {
        $file = CUploadedFile::getInstanceByName('file');
        $uploadPath = Yii::getPathOfAlias('webroot.images.users');
        $file_name = md5(microtime()) . '.' . $file->getExtensionName();

        $user = User::model()->findByPk(Yii::app()->user->id);
        if ($user) {
            if (!empty($user->avatar)) {
                $user->uploadedFile->deleteFile();
            }
        }

        if ($file->saveAs($uploadPath . '/' . $file_name)) {
            if (!is_dir($uploadPath . '/thumbs')) {
                if ((@mkdir($uploadPath . '/thumbs')) == false)
                    throw new CException('Thumbs not found');
            }

            $org_image = Yii::app()->image->load($uploadPath . '/' . $file_name);

            foreach (User::$thumbs as $name => $params) {
                $method = key($params);
                $args = array_values($params);

                call_user_func_array([$org_image, $method], is_array($args[0]) ? $args[0] : [$args[0]]);
                $org_image->save($uploadPath . '/thumbs' . DIRECTORY_SEPARATOR . $name . '_' . $file_name);
            }

            Yii::app()->db->createCommand()->update('users', [
                'avatar' => $file_name,
            ], 'user_id=:user_id', [':user_id' => Yii::app()->user->id]);

            RAjax::success([
                'avatar'      => Yii::app()->baseUrl . '/images/users/thumbs/avatar_' . $file_name,
                'avatar_mini' => Yii::app()->baseUrl . '/images/users/thumbs/avatar_mini_' . $file_name,
            ]);
        }
    }

    public function actionUpdate_info()
    {
        if (Yii::app()->request->isAjaxRequest && !Yii::app()->user->isGuest) {
            $model = Auction::model()->updateAll(['conditions_transfer' => $_GET['info']], 'owner=:owner', [':owner' => Yii::app()->user->id]);
        }
    }

    /**
     * @throws CDbException
     */
    public function actionCertified()
    {
        $request = Yii::app()->getRequest();
        $this->pageTitle = Yii::t('basic', 'Verification');
        $this->layout = '//layouts/settings';
        $user = Getter::userModel();

        $text = Yii::app()->db->createCommand()->select('text_certified')->from('pages_pro')->queryRow();



        $this->render('certified', [
            'user'             => $user,
            'text'             => $text,
        ]);
    }

}