<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendOrderDeliveredEmail(string $email, string $name, int $courId): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('farahboukesra4@gmail.com', 'Nom de votre application'))
            ->to($email)
            ->subject('Confirmation d\'inscription au cours')
            ->htmlTemplate('emails/order_delivered.html.twig') // Le template HTML
            ->context([
                'name' => $name,
                'courId' => $courId,
            ]);

        $this->mailer->send($email);
    }
}
