<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace Solves;


class SolvesMail {

    public static $EMAIL_HOST = '';
    public static $EMAIL_PORT = '';
    public static $EMAIL_REMETENTE = '';
    public static $EMAIL_REMETENTE_PASSWD = '';
    public static $EMAIL_REMETENTE_FROM_LABEL = '';
    
    public static function getNewMailer() {
        $mail = new PHPMailer();
        return SolvesMail::configureMailer($mail);
    }
    public static function configureMailer($mail) {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
        $mail->Host = SolvesMail::$EMAIL_HOST;
        $mail->Port = SolvesMail::$EMAIL_PORT;
        $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
        $mail->Username = SolvesMail::$EMAIL_REMETENTE;
        $mail->Password = SolvesMail::$EMAIL_REMETENTE_PASSWD;
        $mail->setFrom(SolvesMail::$EMAIL_REMETENTE, SolvesMail::$EMAIL_REMETENTE_FROM_LABEL);
        return $mail;
    }

}