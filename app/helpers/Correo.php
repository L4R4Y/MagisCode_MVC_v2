<?php

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

    /**
     * MODO PRUEBA: mientras esta constante sea true, los correos no se
     * envían de verdad; se guardan en un archivo de texto para poder
     * revisarlos. Ponla en false cuando ya tengas un SMTP configurado.
     */
    private const MODO_PRUEBA = true;

    private static function enviar(string $destinatario, string $asunto, string $cuerpo): bool
    {
        $remitenteNombre = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'MagisCode';
        $remitenteCorreo = defined('MAIL_FROM') ? MAIL_FROM : 'no-responder@magiscode.com';

        if (self::MODO_PRUEBA) {
            $registro = "===== " . date('Y-m-d H:i:s') . " =====\n"
                . "Para: {$destinatario}\n"
                . "De: {$remitenteNombre} <{$remitenteCorreo}>\n"
                . "Asunto: {$asunto}\n\n"
                . "{$cuerpo}\n\n";

            return (bool) file_put_contents(
                __DIR__ . '/../../correo_debug.log',
                $registro,
                LOCK_EX
            );
        }

        $asuntoCodificado = '=?UTF-8?B?' . base64_encode($asunto) . '?=';

        $cabeceras = "From: {$remitenteNombre} <{$remitenteCorreo}>\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";

        return @mail($destinatario, $asuntoCodificado, $cuerpo, $cabeceras);
    }
}
