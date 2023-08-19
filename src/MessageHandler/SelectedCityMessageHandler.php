<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\MessageHandler;

use App\Message\SelectedCityMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;

final class SelectedCityMessageHandler implements MessageHandlerInterface
{

    private MailerInterface $mailer;
    
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function __invoke(SelectedCityMessage $message)
    {
        $email = (new TemplatedEmail())
                ->to(new Address('leonardo@paginaemconstrucao.com.br'))
                ->subject('Selected City')
                ->htmlTemplate('email/city/selectedCityMessage.html.twig')
                ->textTemplate('email/city/selectedCityMessage.text.twig')
                ->context([
                    'city' => $message->getCity(),
                ])
        ;

        $this->mailer->send($email);
    }
}
