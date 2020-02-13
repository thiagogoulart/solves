<?php
/**
 * @author Thiago G.S. Goulart
 * @version 1.0
 * @created 18/07/2019
 */ 
namespace SolvesMail;

use PHPMailer\PHPMailer\PHPMailer;

class SolvesMail {

    private static $EMAIL_HOST = '';
    private static $EMAIL_PORT = '';
    private static $EMAIL_REMETENTE = '';
    private static $EMAIL_REMETENTE_PASSWD = '';
    private static $EMAIL_REMETENTE_FROM_LABEL = '';

    public static function config($emailHost, $emailPort, $emailRemetente, $emailRemetentePasswd, $emailRemetenteFromLabel){
        SolvesMail::setEmailHost($emailHost);
        SolvesMail::setEmailPort($emailPort);
        SolvesMail::setEmailRemetente($emailRemetente);
        SolvesMail::setEmailRemetentePasswd($emailRemetentePasswd);
        SolvesMail::setEmailRemetenteFromLabel($emailRemetenteFromLabel);
    }

    public static function setEmailHost($p){SolvesMail::$EMAIL_HOST = $p;}
    public static function setEmailPort($p){SolvesMail::$EMAIL_PORT = $p;}
    public static function setEmailRemetente($p){SolvesMail::$EMAIL_REMETENTE = $p;}
    public static function setEmailRemetentePasswd($p){SolvesMail::$EMAIL_REMETENTE_PASSWD = $p;}
    public static function setEmailRemetenteFromLabel($p){SolvesMail::$EMAIL_REMETENTE_FROM_LABEL = $p;}
    
    public static function getNewMailer() {
        if(\Solves\Solves::isTestMode()){
            return new PHPMailerMock();
        }else{
            $mail = new PHPMailer();
            return SolvesMail::configureMailer($mail);
        }
    }
    public static function configureMailer($mail, ?bool $useTls=true) {
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
        if($useTls){
            $mail->SMTPSecure = 'tls';
            $mail->smtpConnect(
                array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                        "allow_self_signed" => true
                    )
                )
            );
        }
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
class PHPMailerMock{
    public $Subject;
    public $Body;
    public $AltBody;


    public $enviado = false;
    public $adresses = [];
/**
     * Add a "To" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addAddress($address, $name = ''){ 
        $this->adresses[$address]= $name;
    }

    /**
     * Create a message and send it.
     * Uses the sending method specified by $Mailer.
     *
     * @throws Exception
     *
     * @return bool false on error - See the ErrorInfo property for details of the error
     */
    public function send(): bool{
        $this->enviado = true;
        return $this->enviado;
    }
}