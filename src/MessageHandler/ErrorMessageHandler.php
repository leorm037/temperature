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

use App\Message\ErrorMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ErrorMessageHandler implements MessageHandlerInterface
{

    public function __invoke(ErrorMessage $message)
    {
        $email = new TemplatedEmail();

        $email->from()
                ->to()
                ->subject()
                ->htmlTemplate('email/error/errorMessage.html.twig')
                ->textTemplate('email/error/errorMessage.text.twig')
                ->context([
                    'code' => $message->getCode(),
                    'message' => $message->getMessage(),
                    'file' => $message->getFile(),
                    'line' => $message->getLine(),
                ])
        ;
        
        $this->mailer->send($email);
    }
}
