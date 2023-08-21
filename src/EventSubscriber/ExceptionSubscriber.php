<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventSubscriber;

use App\Message\ErrorMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private MessageBusInterface $messageBus;
    
    public function __construct(
            LoggerInterface $logger, 
            MessageBusInterface $messageBus
    ) {
        $this->logger = $logger;
        $this->messageBus = $messageBus;
    }
    
    public function onKernelException(ExceptionEvent $event): void
    {
        $code = $event->getThrowable()->getCode();
        $file = $event->getThrowable()->getFile();
        $line = $event->getThrowable()->getLine();
        $message = $event->getThrowable()->getMessage();
        
        $this->logger->error($message, [
            'code' => $code,
            'file' => $file,
            'line' => $line
        ]);
              
        $this->messageBus->dispatch(new ErrorMessage($code, $message, $file, $line));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
