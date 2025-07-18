<?php

namespace vendor\easyFrameWork\Core\Master;
interface iObserver {
    public function update($subject);
}
interface ISubject {
    public function attach($obs);
    public function detach($obs);
    public function notifyObs();
}


/**
 * mail permet de transmettre un mail 
 * avec les paramètres
 * @author Sébastien DAMART
 */
class mailEventListener implements ISubject {
    private $_observers = array();
    /**
     * adresse mail de l'éméteur
     * @var string
     */
    private $emeteur;
    /**
     * sujet du mail
     * @var string
     */
    private $subject;
    /**
     * Message à transmettre
     * @var string
     */
    private $message;
    /**
     * liste des paramètres d'entêtes
     * @var array
     */
    private $headers;
    /**
     * adresse du destinataire
     * @var string 
     */
    private $to;

    public function __construct($From, $subject = "", $message = "") {
        $this->emeteur = $From;
        $this->subject = $subject;
        $this->message = $message;
        $this->headers = array(
            "From" => $this->emeteur,
            "Reply-To" => "",
            "MIME-Version:" =>"1.0",
            "X-Mailer" => "PHP/" . phpversion(),
            "Content-type" => "text/html; charset=UTF-8"
        );
    }

    public function __get($name) {
        switch ($name) {
            case "From":
                return $this->emeteur;
            case "Subject":
                return $this->subject;
            case "Message":
                return $this->message;
            case "To":
                return $this->to;
            default:
                break;
        }
    }

    public function __set($name, $value) {
        switch ($name) {
            case "From":
                $this->emeteur = $value;
                break;
            case "Subject":
                $this->subject = $value;
                break;
            case "Message":
                $this->message = $value;
                break;
            case "To":
                $this->to = $value;
                break;
            default:
                break;
        }
    }

    public function setHeader($param, $value) {
        $this->headers[$param] = $value;
    }

    public function attach($obs) {
        $this->_observers[] = $obs;
    }

    public function addEventListener($eventHandler) {
        $this->attach($eventHandler);
    }

    public function detach($obs) {
        if (is_int($key = array_search($obs, $this->_observers, true))) {
            unset($this->_observers[$key]);
        }
    }

    public function notifyObs() {
        foreach ($this->_observers as $observer) {
            try {
                $observer->update($this); // délégation
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }
    }

    public function SendMail($to) {
        $subject = $this->subject;
        $message = $this->message;
        $headers = "";
        foreach ($this->headers as $key => $value) {
            $headers .= "$key: $value\r\n";
        }
        mail($to, $subject, $message, $headers);
          
        // Debugging output
        echo "To: $to<br>";
        echo "Subject: $subject<br>";
        echo "Message: $message<br>";
        echo "Headers:<br>" . nl2br($headers) . "<br>";

        if (mail($to, $subject, $message, $headers)) {
            echo "Mail sent successfully.";
        } else {
            echo "Failed to send mail.";
        }

        $this->notifyObs();
    }
}


abstract class MailEventListner implements IObserver{
    public function update($object) {
        $this->execute($object);
    }
    public function execute($mail){}
}

class MailLog extends MailEventListner{
    private $file;
    public function __construct($f) {
        $this->file=$f;
    }
    public function execute($mail) {
        $this->write($mail);
    }
    private function write($mail){
        if(file_exists($this->file)){
        $handle=fopen($this->file,"r");
        $content=  file_get_contents($this->file);
        fclose($handle);
        }else
            $content="";
        $line=$mail->From." send mail [".$mail->Subject."] - ".date("Y-m-d")." at ".date("H:m:s");
        $h=fopen($this->file,"w+");
        file_put_contents($this->file, $content."\n".$line);
        fclose($h);
    }
}