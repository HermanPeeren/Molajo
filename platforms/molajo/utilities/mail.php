<?php
/**
 * @package     Molajo
 * @subpackage  MolajoController
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Forked to fix PHP Strict Errors
 * Pull request issued
 */

/**
 * Email Class.  Provides a common interface to send email from the Molajo Framework
 *
 * @package    Molajo
 * @subpackage  Mail
 * @since       1.0
 */
class MolajoMail extends PHPMailer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // PHPMailer has an issue using the relative path for it's language files
        $this->SetLanguage('joomla', JOOMLA_LIBRARY . '/phpmailer/language/');
    }

    /**
     * Returns the global email object, only creating it
     * if it doesn't already exist.
     *
     * NOTE: If you need an instance to use that does not have the global configuration
     * values, use an id string that is not 'Joomla'.
     *
     * @param   string  $id  The id string for the MolajoMail instance [optional]
     *
     * @return  object  The global MolajoMail object
     * @since   1.0
     */
    public static function getInstance($id = 'Molajo')
    {
        static $instances;

        if (!isset ($instances)) {
            $instances = array();
        }

        if (empty($instances[$id])) {
            $instances[$id] = new MolajoMail();
        }

        return $instances[$id];
    }

    /**
     * Send the mail
     *
     * @return  mixed  True if successful, a MolajoError object otherwise
     * @since   1.0
     */
    public function Send()
    {
        if (($this->Mailer == 'mail') && !function_exists('mail')) {
            return MolajoError::raiseNotice(500, TextService::_('MOLAJO_MAIL_FUNCTION_DISABLED'));
        }

        @$result = parent::Send();

        if ($result == false) {
            // TODO: Set an appropriate error number
            $result = MolajoError::raiseNotice(500, TextService::_($this->ErrorInfo));
        }

        return $result;
    }

    /**
     * Set the email sender
     *
     * @param   array  email address and Name of sender
     *        <pre>
     *            array([0] => email Address [1] => Name)
     *        </pre>
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function setSender($from)
    {
        if (is_array($from)) {
            // If $from is an array we assume it has an address and a name
            $this->SetFrom(
                MolajoMailServices::cleanLine($from[0]),
                MolajoMailServices::cleanLine($from[1])
            );
        }
        elseif (is_string($from)) {
            // If it is a string we assume it is just the address
            $this->SetFrom(
                MolajoMailServices::cleanLine($from)
            );
        }
        else {
            // If it is neither, we throw a warning
            MolajoError::raiseWarning(0, TextService::sprintf('MOLAJO_MAIL_INVALID_EMAIL_SENDER', $from));
        }

        return $this;
    }

    /**
     * Set the email subject
     *
     * @param   string   $subject    Subject of the email
     *
     * @return  object   MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function setSubject($subject)
    {
        $this->Subject = MolajoMailServices::cleanLine($subject);
        return $this;
    }

    /**
     * Set the email body
     *
     * @param   string  $content    Body of the email
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function setBody($content)
    {
        /*
           * Filter the Body
           * TODO: Check for XSS
           */
        $this->Body = MolajoMailServices::cleanText($content);
        return $this;
    }

    /**
     * Add recipients to the email
     *
     * @param   mixed  $recipient    Either a string or array of strings [email address(es)]
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function addRecipient($recipient, $name = '')
    {
        // If the recipient is an array, add each recipient... otherwise just add the one
        if (is_array($recipient)) {
            foreach ($recipient as $to)
            {
                $to = MolajoMailServices::cleanLine($to);
                $this->AddAddress($to);
            }
        } else {
            $recipient = MolajoMailServices::cleanLine($recipient);
            $this->AddAddress($recipient);
        }

        return $this;
    }

    /**
     * Add carbon copy recipients to the email
     *
     * @param   mixed  $cc  Either a string or array of strings [email address(es)]
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function addCC($cc, $name = '')
    {
        // If the carbon copy recipient is an array, add each recipient... otherwise just add the one
        if (isset ($cc)) {
            if (is_array($cc)) {
                foreach ($cc as $to) {
                    $to = MolajoMailServices::cleanLine($to);
                    parent::AddCC($to);
                }
            } else {
                $cc = MolajoMailServices::cleanLine($cc);
                parent::AddCC($cc);
            }
        }

        return $this;
    }

    /**
     * Add blind carbon copy recipients to the email
     *
     * @param   mixed  $bcc    Either a string or array of strings [email address(es)]
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function addBCC($bcc, $name = '')
    {
        // If the blind carbon copy recipient is an array, add each recipient... otherwise just add the one
        if (isset($bcc)) {
            if (is_array($bcc)) {
                foreach ($bcc as $to) {
                    $to = MolajoMailServices::cleanLine($to);
                    parent::AddBCC($to);
                }
            } else {
                $bcc = MolajoMailServices::cleanLine($bcc);
                parent::AddBCC($bcc);
            }
        }

        return $this;
    }

    /**
     * Add file attachments to the email
     *
     * @param   mixed  $attachment    Either a string or array of strings [filenames]
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
    {
        // If the file attachments is an array, add each file... otherwise just add the one
        if (isset($attachment)) {
            if (is_array($attachment)) {
                foreach ($attachment as $file) {
                    parent::AddAttachment($file);
                }
            } else {
                parent::AddAttachment($attachment);
            }
        }

        return $this;
    }

    /**
     * Add Reply to email address(es) to the email
     *
     * @param   array  $replyto    Either an array or multi-array of form
     *        <pre>
     *            array([0] => email Address [1] => Name)
     *        </pre>
     *
     * @return  object  MolajoMail    Returns this object for chaining.
     * @since   1.0
     */
    public function addReplyTo($replyto, $name = '')
    {
        // Take care of reply email addresses
        if (is_array($replyto[0])) {
            foreach ($replyto as $to)
            {
                $to0 = MolajoMailServices::cleanLine($to[0]);
                $to1 = MolajoMailServices::cleanLine($to[1]);
                parent::AddReplyTo($to0, $to1);
            }
        }
        else {
            $replyto0 = MolajoMailServices::cleanLine($replyto[0]);
            $replyto1 = MolajoMailServices::cleanLine($replyto[1]);
            parent::AddReplyTo($replyto0, $replyto1);
        }

        return $this;
    }

    /**
     * Use send_mail for sending the email
     *
     * @param   string   $send_mail    Path to send_mail [optional]
     * @return  boolean  True on success
     * @since   1.0
     */
    public function useSendmail($send_mail = null)
    {
        $this->Sendmail = $send_mail;

        if (empty ($this->Sendmail)) {
            $this->IsMail();
            return false;
        } else {
            $this->IsSendmail();
            return true;
        }
    }

    /**
     * Use SMTP for sending the email
     *
     * @param   string   $auth    SMTP Authentication [optional]
     * @param   string   $host    SMTP Host [optional]
     * @param   string   $user    SMTP Username [optional]
     * @param   string   $pass    SMTP Password [optional]
     * @param   string   $secure
     * @param   integer  $port
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function useSMTP($auth = null, $host = null, $user = null, $pass = null, $secure = null, $port = 25)
    {
        $this->SMTPAuth = $auth;
        $this->Host = $host;
        $this->Username = $user;
        $this->Password = $pass;
        $this->Port = $port;

        if ($secure == 'ssl' || $secure == 'tls') {
            $this->SMTPSecure = $secure;
        }

        if (($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null)
            || ($this->SMTPAuth === null && $this->Host !== null)
        ) {
            $this->IsSMTP();

            return true;
        }
        else {
            $this->IsMail();

            return false;
        }
    }

    /**
     * Function to send an email
     *
     * @param   string   $from            From email address
     * @param   string   $fromName        From name
     * @param   mixed    $recipient        Recipient email address(es)
     * @param   string   $subject        email subject
     * @param   string   $body            Message body
     * @param   boolean  $mode            false = plain text, true = HTML
     * @param   mixed    $cc                CC email address(es)
     * @param   mixed    $bcc            BCC email address(es)
     * @param   mixed    $attachment        Attachment file name(s)
     * @param   mixed    $replyTo        Reply to email address(es)
     * @param   mixed    $replyToName    Reply to name(s)
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function sendMail($from, $fromName, $recipient, $subject, $body, $mode = 0,
                             $cc = null, $bcc = null, $attachment = null, $replyTo = null, $replyToName = null)
    {
        $this->setSender(array($from, $fromName));
        $this->setSubject($subject);
        $this->setBody($body);

        // Are we sending the email as HTML?
        if ($mode) {
            $this->IsHTML(true);
        }

        $this->addRecipient($recipient);
        $this->addCC($cc);
        $this->addBCC($bcc);
        $this->addAttachment($attachment);

        // Take care of reply email addresses
        if (is_array($replyTo)) {
            $numReplyTo = count($replyTo);

            for ($i = 0; $i < $numReplyTo; $i++)
            {
                $this->addReplyTo(array($replyTo[$i], $replyToName[$i]));
            }
        }
        else if (isset($replyTo)) {
            $this->addReplyTo(array($replyTo, $replyToName));
        }

        return $this->Send();
    }

    /**
     * Sends mail to administrator for approval of a user submission
     *
     * @param   string  $adminName    Name of administrator
     * @param   string  $adminEmail    Email address of administrator
     * @param   string  $email        [NOT USED TODO: Deprecate?]
     * @param   string  $type        Type of item to approve
     * @param   string  $title        Title of item to approve
     * @param   string  $author        Author of item to approve
     * @param   string  $url
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function sendAdminMail($adminName, $adminEmail, $email, $type, $title, $author, $url = null)
    {
        $subject = TextService::sprintf('MOLAJO_MAIL_USER_SUBMITTED', $type);

        $message = sprintf(TextService::_('MOLAJO_MAIL_MSG_ADMIN'), $adminName, $type, $title, $author, $url, $url, 'administrator', $type);
        $message .= TextService::_('MOLAJO_MAIL_MSG') . "\n";

        $this->addRecipient($adminEmail);
        $this->setSubject($subject);
        $this->setBody($message);

        return $this->Send();
    }
}
