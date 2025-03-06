<?php
namespace App\Service;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class MailerService
{
    private $mailer;

    public function __construct()
    {
        // Configuration du transport SMTP
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('yassinewertani09@gmail.com')
            ->setPassword('mmlpxeorlihaqgkr');  // ou mot de passe d'application pour 2FA

        $this->mailer = new Swift_Mailer($transport);
    }

    public function sendOrderDeliveredEmail(string $toEmail, string $nom, int $orderId)
    {
        // Création du message
        $message = (new Swift_Message('Votre commande a été livrée !'))
            ->setFrom('yassinewertani09@gmail.com')  // L'adresse de l'expéditeur
            ->setTo($toEmail)                   // L'adresse du destinataire
            ->setBody(
                "<h1>Bonjour $nom,</h1><p>Votre commande <strong>#$orderId</strong> a bien été livrée.</p><p>Merci pour votre confiance !</p>",
                'text/html'
            );

        try {
            // Envoi de l'email
            $this->mailer->send($message);
            echo 'Email envoyé avec succès !';
        } catch (\Exception $e) {
            // Gestion des erreurs
            echo 'Erreur lors de l\'envoi de l\'email : ', $e->getMessage();
        }
    }
}