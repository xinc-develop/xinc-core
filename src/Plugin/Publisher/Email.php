<?php
/**
 * Xinc - Continuous Integration.
 * This interface represents a publishing mechanism to publish build results
 *
 *
 * @author    Arno Schneider <username@example.org>
 * @copyright 2007 Arno Schneider, Barcelona
 * @license   http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *            This file is part of Xinc.
 *            Xinc is free software; you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation; either version 2.1 of
 *            the License, or (at your option) any later version.
 *
 *            Xinc is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public
 *            License along with Xinc, write to the Free Software Foundation,
 *            Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Plugin\Publisher;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Base;
use Xinc\Core\Task\Slot;

class Email extends Base
{
    private $defaultFrom = 'xinc@localhost';
	
	private $to;
    private $from;
    private $subject;
    private $message;
	
    public final function process(BuildInterface $build)
    {
        if ( ($status = $this->publish($build)) === true ) {
            $build->setStatus(Xinc_Build_Interface::PASSED);
        } else if ( $status == -1 ) {
            $build->setStatus(Xinc_Build_Interface::STOPPED);
        } else {
            $build->setStatus(Xinc_Build_Interface::FAILED);
        }
    }

    /**
     * Returns the slot of this task inside a build.
     *
     * @return integer The slot number.
     */
    public function getPluginSlot()
    {
        return Slot::POST_PROCESS;
    }

    private function _sendPearMail($from, $to, $subject, $message)
    {
        require_once 'Xinc/Ini.php';

        try {
            $smtpSettings = Xinc_Ini::getInstance()->get('email_smtp');
        } catch (Exception $e) {
            $smtpSettings = null;
        }
        if ($smtpSettings != null) {
            $mailer = Mail::factory('smtp', $smtpSettings);
        } else {
            $mailer = Mail::factory('mail');
        }
        $recipients = split(',', $to);
        $headers = array();

        if (isset($smtpSettings['localhost'])) {
            $from = str_replace('@localhost', '@' . $smtpSettings['localhost'], $from);
        }

        $headers['From'] = $from;
        $headers['Subject'] = $subject;
        $res = $mailer->send($recipients, $headers, $message);
        if ($res === true) {
            return $res;
        } else {
            return false;
        }
    }

    public function email(
        Xinc_Project $project, $to, $subject, $message, $from = 'Xinc'
    ) {
        if (empty($from)) {
            $from = $this->_defaultFrom;
        }
        $project->info('Executing email publisher with content ' 
                      ."\nTo: " . $to
                      ."\nSubject: " . $subject
                      ."\nMessage: " . $message
                      ."\nFrom: " . $from);

        /** send the email */
        @include_once 'Mail.php';

        if (class_exists('Mail')) {
            return $this->_sendPearMail($from, $to, $subject, $message);
        } else {
            $res = mail($to, $subject, $message, "From: $from\r\n");
            if ($res) {
                $project->info('Email sent successfully');
                return true;
            } else {
                $project->error('Email could not be sent');
                return false;
                //$project->setStatus(Xinc_Build_Interface::FAILED);
            }
        }
    }
	
    public function getName()
    {
        return 'email';
    }

    /**
     * Set the email address to send to
     *
     * @param string $subject
     */
    public function setTo($to)
    {
        $this->_to = (string)$to;
    }
    /**
     * Set the email address to send to
     *
     * @param string $subject
     */
    public function setFrom($from)
    {
        $this->_from = (string)$from;
    }
    /**
     * Set the subject of the email
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->_subject = (string)$subject;
    }

    /**
     * Set the message of the email
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->_message = (string)$message;
    }

    public function validate(&$msg = null)
    {
        if (!isset($this->_to)) {
              throw new Xinc_Exception_MalformedConfig(
                'Element publisher/email - required attribute "to" is not set'
            );
        }
        if (!isset($this->_subject)) {
            throw new Xinc_Exception_MalformedConfig(
                'Element publisher/email - required attribute "subject" is not set'
            );
        }
        if (!isset($this->_message)) {
            throw new Xinc_Exception_MalformedConfig(
                'Element publisher/email - required attribute "message" is not set'
            );
        }
        return true;
    }

    public function publish(Xinc_Build_Interface $build)
    {
        $statusBefore = $build->getStatus();
        $res = $this->plugin->email($build->getProject(), $this->_to, $this->_subject, $this->_message, $this->_from);
    }
}
