<?php
require dirname(__DIR__,2) . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.hostinger.com';  
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'contacto@serviciosempresarialestamapa.com';
            $this->mail->Password = '#C0ntact0';  
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;
            $this->mail->setFrom('contacto@serviciosempresarialestamapa.com', 'Servicio Empresarial Estamapa');
        } catch (Exception $e) {
            echo "Error al configurar PHPMailer: " . $this->mail->ErrorInfo;
        }
    }

    public function enviarEmail($correo, $token) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($correo, 'Destinatario'); 
            $this->mail->isHTML(true);  
            $this->mail->Subject = 'Consulta desde formulario de contacto';
            $this->mail->Body = "
                <p>Hola,</p>
                <p>Hemos recibido tu solicitud de contacto. Para continuar, por favor utiliza el siguiente token:</p>
                <p style='font-size: 18px; font-weight: bold;'>$token</p>
                <p>Este token es válido por un tiempo limitado. Copia y pega este código en el formulario correspondiente para verificar tu identidad.</p>
                <p>Si no solicitaste este token, por favor ignora este correo.</p>
                <p>Gracias,</p>
                <p><strong>Servicio Empresarial Estamapa</strong></p>
            ";  

            $this->mail->AltBody = "Hola,\n\nHemos recibido tu solicitud de contacto. Para continuar, por favor utiliza el siguiente token:\n\n$token\n\nEste token es válido por un tiempo limitado. Copia y pega este código en el formulario correspondiente para verificar tu identidad.\n\nSi no solicitaste este token, por favor ignora este correo.\n\nGracias,\nServicio Empresarial Estamapa";

            if ($this->mail->send()) {
                return ["status" => 'Correo enviado correctamente'];
            } else {
                return ["error" => 'Error al enviar el correo: ' . $this->mail->ErrorInfo];
            }
        } catch (Exception $e) {
            return ["error" => 'No se pudo enviar el correo. Error de PHPMailer: ' . $this->mail->ErrorInfo];
        }
    }
}
?>