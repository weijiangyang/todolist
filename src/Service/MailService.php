<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * This function allow to send a email from the site
     *
     * @param string $from
     * @param string $subject
     * @param string $htmlTemplate
     * @param array $context
     * @param string $to
     * @return void
     */
    public function sendEmail(
        string $from = "weijiangyanglaval@gmail.com",
        string $subject,
        string $htmlTemplate,
        array $context,
        string $to
    ) {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)

            ->htmlTemplate($htmlTemplate)

            ->context($context);


        // ...
        $this->mailer->send($email);
    }
}
