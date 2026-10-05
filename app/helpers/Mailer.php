<?php
// app/helpers/Mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private array $config;
    private PHPMailer $mail;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? require __DIR__ . '/../config/mail.php';

        $this->mail = new PHPMailer(true);
        $this->configurarSMTP();
    }

    private function configurarSMTP(): void
    {
        $this->mail->isSMTP();
        $this->mail->Host = $this->config['host'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->config['username'];
        $this->mail->Password = $this->config['password'];
        $this->mail->SMTPSecure = $this->config['encryption'] === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $this->config['port'];
        $this->mail->CharSet = $this->config['charset'];

        $this->mail->setFrom($this->config['from'], $this->config['from_name']);
    }

    public function enviar(array $opciones): array
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();

            // Destinatarios
            $para = $opciones['para'] ?? null;
            if (empty($para)) {
                return ['ok' => false, 'error' => 'Falta el destinatario.'];
            }
            foreach ((array) $para as $email) {
                $this->mail->addAddress(trim($email));
            }

            // CC y BCC
            foreach ((array) ($opciones['copia'] ?? []) as $email) {
                if ($email)
                    $this->mail->addCC(trim($email));
            }
            foreach ((array) ($opciones['copia_oculta'] ?? []) as $email) {
                if ($email)
                    $this->mail->addBCC(trim($email));
            }

            // Adjuntos
            $adjuntos = (array) ($opciones['adjuntos'] ?? []);
            foreach ($adjuntos as $ruta) {
                if (is_file($ruta)) {
                    $this->mail->addAttachment($ruta);
                } else {
                    error_log("Adjunto no encontrado: $ruta");
                }
            }

            // Validación mínima: debe haber contenido o adjuntos
            if (empty($opciones['contenido']) && empty($adjuntos)) {
                return ['ok' => false, 'error' => 'El correo no tiene contenido ni adjuntos.'];
            }

            // Contenido
            $this->mail->isHTML(true);
            $this->mail->Subject = $opciones['asunto'] ?? 'Sin asunto';
            $this->mail->Body = $opciones['contenido'] ?? '';
            $this->mail->AltBody = strip_tags($opciones['contenido'] ?? '');

            $this->mail->send();

            return ['ok' => true, 'mensaje' => 'Correo enviado correctamente.'];

        } catch (Exception $e) {
            return ['ok' => false, 'error' => $this->mail->ErrorInfo];
        }
    }
}