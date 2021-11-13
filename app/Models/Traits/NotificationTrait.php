<?php

namespace App\Models\Traits;

trait NotificationTrait
{
    protected $mailFrom = '';
    protected $mailTo = '';
    protected $message = '';
    protected $subject = '';

    protected function sendEmail()
    {

    }
}