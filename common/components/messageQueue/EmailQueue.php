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


Yii::import('common.extensions.mail.SmtpApi');

/**
 * Class EmailQueue
 */
class EmailQueue extends CMessageQueue
{
    public $isFirstVersion = true;

    public $isSecondVersion = false;

    private $_controller;

    /**
     * Добавление одного сообщения.
     *
     * @param EmailMessageInterface $message
     *
     * @return bool
     */
    public function add(EmailMessageInterface $message)
    {
        return $this->addItemToQueue($message);
    }

    /**
     * Добавление пачки сообщений.
     */
    public static function addBulk()
    {
        // TODO
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    protected function addItemToQueue($message)
    {
        /** @var EmailMessage $message */

        $model = new MessageQueue();
        $model->is_email = true;
        $model->message_json = [
            'view'        => $message->getView(),
            'subject'     => $message->getSubject(),
            'body'        => $message->getBody(),
            'contentType' => $message->getContentType(),
            'to'          => $message->getTo(),
            'from'        => $message->getFrom(),
        ];
        $model->scope = MessageQueue::SCOPE_EMAIL_MAILING;
        return $model->save();
    }

    public function process($count)
    {
        $this->line();
        if (!is_numeric($count) || $count < 1) {
            $this->warning(sprintf('Items count must be greater than 0!', $count));
            return false;
        }
        $this->trace(sprintf('Begin processing email queue. Will be processed %s items.', $count));

        $appProcess = AppProcess::model()->findByAttributes(['code' => AppProcess::CODE_EMAIL_QUEUE]);
        if (!$appProcess) {
            $appProcess = new AppProcess();
            $appProcess->code = AppProcess::CODE_EMAIL_QUEUE;
            $appProcess->title = 'Email Queue';
            $appProcess->data = [
                'is_running' => false,
                'start'      => '',
                'finish'     => '',
            ];
            $appProcess->save();
            $appProcess->refresh();
        }
        $processData = $appProcess->data;
        $isRunning = $processData['is_running'];

        if (!$isRunning) {
            $processData['start'] = date('Y-m-d H:i:s');
            $processData['is_running'] = true;
            $appProcess->data = $processData;
            $appProcess->update(['data']);

            $offset = 0;
            $processedCount = 0;
            $processedSuccessCount = 0;
            $processedErrorCount = 0;
            $this->batchSize = $count > $this->batchSize ? $this->batchSize : $count;

            while ($processedCount < $count) {
                $items = $this->getPendingItems($this->batchSize, $processedCount);
                if (!$items) {
                    break;
                }

                foreach ($items as $eachMessage) {
                    $messageJson = $eachMessage->message_json;
                    if (empty($messageJson)) {
                        $eachMessage->sendFailed();
                        $this->error('$messageJson is not array or is empty, $eachMessage data: ' . CVarDumper::dumpAsString($eachMessage->getAttributes()));
                        $processedErrorCount++;
                    } else {
                        $this->trace(sprintf('Sending message ID %s, title: %s', $eachMessage->getPrimaryKey(), $messageJson['subject']));
                        try {
                            $result = $this->sendMessage($messageJson);
                        } catch (Exception $e) {
                            $error = 'Exception ' . get_class($e) . ' with message: "' . $e->getMessage() . '"' . "\n";
                            $error .= 'File ' . $e->getFile() . ':' . $e->getLine() . "\n";
                            $error .= 'Trace ' . $e->getTraceAsString() . "\n";
                            $this->error(sprintf('Send message ID %s failed. Error: ', $eachMessage->getPrimaryKey(), $error));
                            $result = false;
                        }
                        if ($result) {
                            $eachMessage->sendSuccess();
                            $processedSuccessCount++;
                        } else {
                            $eachMessage->sendFailed();
                            $processedErrorCount++;
                        }
                    }
                }
                $processedCount += count($items);
                $offset += $this->batchSize;

                $this->trace(sprintf('%s from %s (memory: %s mb)', $processedCount, $count, round(memory_get_peak_usage() / 1024 / 1024)));
            }

            $this->trace(sprintf(
                'Email queue processed successfully. Total: %s, success: %s, failed: %d.',
                $processedCount, $processedSuccessCount, $processedErrorCount
            ));
            $this->line();

            $processData['finish'] = date('Y-m-d H:i:s');
            $processData['is_running'] = false;
            $appProcess->data = $processData;
            $appProcess->update(['data']);

            return [
                'processedCount'        => $processedCount,
                'processedSuccessCount' => $processedSuccessCount,
                'processedErrorCount'   => $processedErrorCount,
            ];
        } else {
            $this->warning('Email queue processing already started!');
            return false;
        }
    }

    public function testProcess($count)
    {
        $this->line();
        if (!is_numeric($count) || $count < 1) {
            $this->warning(sprintf('[TEST] Items count must be greater than 0!', $count));
            return false;
        }
        $this->trace(sprintf('[TEST] Begin processing email queue. Will be processed %s items.', $count));

        $offset = 0;
        $processedCount = 0;
        $processedSuccessCount = 0;
        $processedErrorCount = 0;
        $this->batchSize = $count > $this->batchSize ? $this->batchSize : $count;

        while ($processedCount < $count) {

            $criteria = new CDbCriteria();
            $criteria->condition = 'is_email = 1 AND scope = :scope';
            $criteria->limit = $this->batchSize;
            $criteria->params = [':scope' => MessageQueue::SCOPE_EMAIL_MAILING];
            /** @var MessageQueue[] $items */
            $items = MessageQueue::model()->findAll($criteria);
            if (!$items) {
                break;
            }

            foreach ($items as $eachMessage) {
                $messageObject = $eachMessage->message_object;
                if ($messageObject && is_object($messageObject) && $messageObject instanceof EmailMessage) {
                    if ($this->sendMessage($messageObject, true)) {
                        $processedSuccessCount++;
                    } else {
                        $processedErrorCount++;
                    }
                } else {
                    $this->error('[TEST] $messageObject is not object, $eachMessage data: ' . CVarDumper::dumpAsString($eachMessage->getAttributes()));
                }
            }
            $processedCount += count($items);
            $offset += $this->batchSize;

            $this->trace(sprintf('[TEST] %s from %s (memory: %s mb)', $processedCount, $count, round(memory_get_peak_usage() / 1024 / 1024)));
        }

        $this->trace(sprintf(
            '[TEST] Email queue processed successfully. Total: %s, success: %s, failed: %d.',
            $processedCount, $processedSuccessCount, $processedErrorCount
        ));
        $this->line();

        return [
            'processedCount'        => $processedCount,
            'processedSuccessCount' => $processedSuccessCount,
            'processedErrorCount'   => $processedErrorCount,
        ];
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return MessageQueue[]
     */
    protected function getPendingItems($limit, $offset)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'is_email = 1 AND scope = :scope AND status = :status';
        $criteria->limit = $limit;
        $criteria->offset = $offset;
        $criteria->params = [
            ':scope'  => MessageQueue::SCOPE_EMAIL_MAILING,
            ':status' => MessageQueue::STATUS_NEW,
        ];
        return MessageQueue::model()->findAll($criteria);
    }

    /**
     * @param EmailMessage|array $message
     * @param bool               $isTest
     *
     * @return bool
     */
    protected function sendMessage($message, $isTest = false)
    {
        $canSendEmail = Getter::getIsEnabledEmailNtf() || $isTest;

        if (false) {
        } elseif (false) {
        } else {
            $mailComponent = Getter::mail2();

            $messageObj = new YiiMailMessage();
            $messageObj->view = $message['view'];
            $messageObj->setSubject($message['subject']);
            $messageObj->setBody($message['body'], $message['contentType']);
            $messageObj->setFrom($message['from']);
            foreach ($message['to'] as $eachEmail) {
                $messageObj->addTo($eachEmail);
            }

            if ($canSendEmail) {
                $sentItems = $mailComponent->send($messageObj);
                $this->trace('Send result: ' . CVarDumper::dumpAsString($sentItems));
            } else {
                $this->trace('Email data: ' . CVarDumper::dumpAsString($messageObj));
                $sentItems = 0;
            }
            if ($sentItems == 0) {
                Yii::log('Error while sending email. sentItems: ' . $sentItems, CLogger::LEVEL_ERROR);
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * @return string
     */
    private function getPidFilename()
    {
        return Yii::app()->getRuntimePath() . '/email_queue_process.pid';
    }
}