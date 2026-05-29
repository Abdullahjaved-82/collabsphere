<?php

namespace App\Exceptions;

use Exception;

class TeamFullException extends Exception
{
    public function __construct(string $message = "Team has reached maximum capacity (8 members)")
    {
        parent::__construct($message);
    }
}
