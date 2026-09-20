<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/mail.local.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envío de notificaciones por correo electrónico.
 *
 * Usa la función nativa mail() de PHP. En un entorno XAMPP local, para que
 * el correo salga realmente es necesario configurar un servidor SMTP en
 * php.ini (por ejemplo Mercury Mail incluido en XAMPP, o un relay externo
 * tipo Gmail/SendGrid). En producción se recomienda reemplazar el envío
 * interno de este helper por una librería como PHPMailer, manteniendo
 * igual la firma de los métodos públicos para no afectar a quien los llama.
 */
class Correo
{
    /**
     * Notifica al Aprendiz que ya cuenta con un usuario en MagisCode,
     * enviándole sus credenciales de acceso al correo registrado.
     */
    public static function enviarCuentaCreada(
        string $destinatario,
        string $nombreCompleto,
        string $username,
        string $passwordTemporal
    ): bool {
        if ($destinatario === '' || !filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $asunto = 'Ya tienes un usuario en MagisCode';

        $cuerpo = "Hola {$nombreCompleto},\n\n"
            . "Ya tienes un usuario creado en la plataforma MagisCode. Estos son tus datos de acceso:\n\n"
            . "Usuario: {$username}\n"
            . "Contraseña temporal: {$passwordTemporal}\n\n"
            . "Por seguridad, ingresa y cambia esta contraseña desde el menú Configuración "
            . "la primera vez que inicies sesión.\n\n"
            . "Si no reconoces esta solicitud, comunícate con el administrador de la plataforma.\n\n"
            . "Equipo MagisCode";

        return self::enviar($destinatario, $asunto, $cuerpo);
    }

    /**
     * Registra las credenciales de un usuario creado en el archivo de debug,
     * independientemente del rol. Útil en entornos educativos donde no todos
     * los usuarios reciben notificación por correo formal, pero se necesita
     * dejar constancia de las credenciales generadas.
     */
    public static function registrarCredencialesDebug(
        string $username,
        string $passwordTemporal
    ): bool {
        $registro = "Usuario: {$username}\n"
            . "Contraseña temporal: {$passwordTemporal}\n";

        return (bool) file_put_contents(
            __DIR__ . '/../../correo_debug.log',
            $registro,
            LOCK_EX
        );
    }

    
    private static function enviar(
    string $destinatario,
    string $asunto,
    string $cuerpo
): bool {
    $mail = new PHPMailer(true);

    try {
        // Configurar SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;

        // Seguridad y puerto
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        // Codificación
        $mail->CharSet = 'UTF-8';

        // Remitente
        $mail->setFrom(
            MAIL_FROM,
            MAIL_FROM_NAME
        );

        // Destinatario
        $mail->addAddress($destinatario);

        // Contenido
        $mail->isHTML(false);
        $mail->Subject = $asunto;
        $mail->Body = $cuerpo;

        // Enviar
        $mail->send();

        return true;

    } catch (Exception $e) {
        return false;
    }
}
}
