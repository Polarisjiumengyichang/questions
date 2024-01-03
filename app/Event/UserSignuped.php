<?php

declare(strict_types=1);

namespace App\Event;

class UserSignuped
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        var_dump($user);
    }
}