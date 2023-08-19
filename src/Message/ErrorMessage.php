<?php

/*
 * This file is part of Temperature.
 *
 * (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Message;

final class ErrorMessage
{

    private $code;
    private $message;
    private $file;
    private $line;

    public function __construct($code, $message, $file, $line)
    {
        $this->code = $code;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }
    
    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getLine()
    {
        return $this->line;
    }

//     private $name;
//     public function __construct(string $name)
//     {
//         $this->name = $name;
//     }
//    public function getName(): string
//    {
//        return $this->name;
//    }
}
