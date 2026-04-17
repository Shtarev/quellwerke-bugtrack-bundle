<?php

namespace Quellwerke\QuellwerkeBugtrackBundle\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Pimcore\Config;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function sendMail($emails, $jsonFile, $fileName, $message): bool
    {
        $config = Config::getSystemConfiguration();
        $senderData = $config['email']['sender'] ?? null;
        if($senderData) {
            $customer = $senderData['name'] ?? null;
            $from = $senderData['email'] ?? null;
        }

        if($customer && $from) {
            $email = (new Email())
                ->from($from)
                ->to(...$emails)
                ->subject('Message from ' . $customer .' to technical support')
                ->text($message)
                ->attach(
                    $jsonFile,
                    $fileName,
                    'application/octet-stream'
                );
            $this->mailer->send($email);
            return true;
        }
        else {
            return false;
        }
    }
}
