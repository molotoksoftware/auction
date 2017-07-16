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
 * Class EmailMessage
 */
class EmailMessage implements MessageInterface, EmailMessageInterface
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body = '';

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var array
     */
    private $to = [];
    /**
     * @var array [email => Sender name]
     */
    private $from;
    /**
     * @var string
     */
    private $view;

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string      $body
     * @param string|null $contentType
     * @param string|null $charset
     */
    public function setBody($body = '', $contentType = null, $charset = null)
    {
        $this->body = $body;
        $this->contentType = $contentType;
        $this->charset = $charset;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function addTo($to)
    {
        $this->to[] = $to;
    }

    /**
     * @return array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param array $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}