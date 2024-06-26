<?php
/**
 * Adaptador para envío de correos.
 *
 * @author $Author$
 * @copyright Copyright (c) 2008, {@link http://www.aquainteractive.com AquaInteractive}
 * @package Aif
 * @since 2013-04-15
 * @subpackage Lib
 * @version $Id$
 */

/**
 * Si se utiliza cuentas de Gmail para enviar correos activar la opcion
 * de permitir conexion con aplicaciones no seguras
 * https://myaccount.google.com/u/1/security
 * https://myaccount.google.com/security?utm_source=OGB&pli=1#connectedapps
 * https://accounts.google.com/b/0/DisplayUnlockCaptcha
 * https://accounts.google.com/DisplayUnlockCaptcha Una resolución más permanente sería cambiar su contraseña a un nivel más fuerte: mayúscula + minúscula + símbolos especiales + números
 */

/**
 * Adaptador para envío de correos, extendiendo de {@link
 * http://phpmailer.worxware.com/ PHPMailer}.
 *
 * @package Aif
 * @subpackage Lib
 */
class AifMailer
{
    /**
     * Idioma pordefault
     * @var string
     */
    public static $lang = 'es';
    /**
     * [send description]
     * @param  [type] $mails   [description]
     * @param  string $subject [description]
     * @param  string $body    [description]
     * @param  [type] $files   [description]
     * @return [type]          [description]
     */
    public static function send($mails, $subject = '', $body = '', $files = NULL, $altBody = '')
    {
        $cfg = Aif::getElementConfig('mail');
        if ($cfg) {
            is_array($cfg) || ($cfg = Aif::ecsc('aif/lib', 'urlArray', $cfg, null));
            if ($cfg) {
                switch ($cfg['type']) {
                    case 'sendgrid':
                        return Aif::ecsc('sendgrid', 'send', array($cfg, $mails, $subject, $body, $files, $altBody), FALSE);
                    case 'gmail':
                    case 'mail':
                        return self::mailer($cfg, $mails, $subject, $body, $files, $altBody);
                }
            }
        } else {
            Aif::ecsc('aif/debugger', 'set', 'not exists Aif::$cfg->mail', '');
        }
        return false;
    }
    /**
     * Envio debugger
     * @param  [type] $cfg     [description]
     * @param  [type] $mails   [description]
     * @param  [type] $subject [description]
     * @param  [type] $body    [description]
     * @param  [type] $files   [description]
     * @return [type]          [description]
     */

    public static function mailer($cfg, $mails, $subject = '', $body = '', $files = NULL, $altBody = '')
    {

        if (class_exists('PHPMailer')) {


            
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'quoted-printable';
            //Tell PHPMailer to use SMTP
            $mail->isSMTP();
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $mail->SMTPDebug = 0;
            //Set the hostname of the mail server
            $mail->Host = $cfg['server'];
            // use
            // $mail->Host = gethostbyname('smtp.gmail.com');
            // if your network does not support SMTP over IPv6
            //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
            $mail->Port = $cfg['port'];
            //Set the encryption system to use - ssl (deprecated) or tls
            $mail->SMTPSecure = 'tls';
            //Whether to use SMTP authentication
            $mail->SMTPAuth = true;
            //Username to use for SMTP authentication - use full email address for gmail
            $cfg['user'] = str_replace('*', '@', $cfg['user']);
            if (strpos($cfg['user'], '@') === FALSE) {
                $cfg['user'] .= '@gmail.com';
            }
            $mail->Username = $cfg['user'];
            //Password to use for SMTP authentication
            $mail->Password = $cfg['password'];
            //Set who the message is to be sent from
            $fromEmail = isset($cfg['email']) && $cfg['email'] ? $cfg['email'] : $cfg['user'];
            $fronName = isset($cfg['name']) && $cfg['name'] ? $cfg['name'] : 'system';
            $mail->setFrom($fromEmail, $fronName);

            //Set an alternative reply-to address
            //$mail->addReplyTo('martin@anemona.com', 'First Last');
            //Set who the message is to be sent to

            if (is_array($mails)) {
                foreach ($mails as $em => $dd) {
                    $name = NULL;
                    if (is_array($dd)) {
                        if (count($dd) > 1) $name = $dd[1];
                        $em = is_numeric($em) ? $dd[0] : $em;
                    } else {
                        if (is_numeric($em)) $em = $dd;
                        else $name = $dd;
                    }
                    $name ? $mail->addAddress($em, $name) : $mail->addAddress($em);
                }
            } else {
                $mail->addAddress($mails);
            }

            //Set the subject line
            $mail->Subject = $subject;
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->msgHTML($body.'');
            //Replace the plain text body with one created manually

            if ($altBody) {
                $mail->AltBody = $altBody;
            }
            //Attach an image file

            if ($files) {
                //$mail->addAttachment('images/phpmailer_mini.png');
            }

            //send the message, check for errors
            if (!$mail->send()) {
                die($mail->ErrorInfo);
                Aif::ecsc('aif/debugger', 'set', array('Mail::send', $mail->ErrorInfo));
                return FALSE;
            } else {
                return TRUE;
            }
            //self::save_mail();
        }
        Aif::ecsc('aif/debugger', 'set', array('Mail::send', 'Requiere PHPMailer class...'));
        return FALSE;
    }

    /**
     * Undocumented function
     *
     * @param [type] $mail
     * @return void
     * Section 2: IMAP
     * IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
     * Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
     * You can use imap_getmailboxes($imapStream, '/imap/ssl', '*' ) to get a list of available folders or labels, this can
     * be useful if you are trying to get this working on a non-Gmail IMAP server.
     */

    public static function save_mail($mail)
    {
        if (function_exists('imap_open')) {
            //You can change 'Sent Mail' to any other folder or tag
            $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";
            //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
            $imapStream = imap_open($path, $mail->Username, $mail->Password);
            $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
            imap_close($imapStream);
            return $result;
        } else {
            Aif::ecsc('aif/debugger', 'set', array('Mail::imap', 'requiere module imap in php...'));
        }
        return FALSE;
    }
}
