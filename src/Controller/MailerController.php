<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends AbstractController
{
    #[Route('/email')]
    public function sendEmail(MailerInterface $mailer, $usagerEmail)
    {
        $destinataire = $usagerEmail;
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('patricemartin38@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            //->text('Sending emails is fun again!')
            //->html('<p>See Twig integration for better HTML integration!</p>');
            // path of the Twig template to render
            ->htmlTemplate('emailTemplate.html.twig')
            ->context([
                'username' => $destinataire,
            ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
            }

        
    }
}