<?php
/**
 * Clase de utilidad para envío de correos electrónicos con PHPMailer
 */

// Incluir autoloader de Composer
require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

// Requires para PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    /**
     * Envía un correo electrónico usando PHPMailer
     * 
     * @param string $to Dirección de correo electrónico del destinatario
     * @param string $subject Asunto del correo
     * @param string $body Cuerpo del correo (HTML)
     * @param array $attachments Array de rutas de archivos para adjuntar
     * @return bool Éxito o fracaso del envío
     */
    public static function sendMail($to, $subject, $body, $attachments = []) {
        // Cargar variables de entorno si no están cargadas
        if (!isset($_ENV['MAIL_HOST'])) {
            require_once dirname(__DIR__) . '/config/env.php';
        }
        
        // Crear instancia de PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME');
            $mail->Password = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');
            $mail->Port = getenv('MAIL_PORT');
            $mail->CharSet = 'UTF-8';
            
            // Remitente
            $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
            
            // Destinatario
            $mail->addAddress($to);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Texto plano alternativo
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $body));
            
            // Adjuntos
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $mail->addAttachment($attachment);
                    }
                }
            }
            
            // Enviar correo
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Registrar error
            error_log("Error al enviar correo: {$mail->ErrorInfo}");
            return false;
        }
    }
}