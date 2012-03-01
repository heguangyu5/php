<?php
class Mailer
{
    public function send($recipient, $subject, $content)
    {
        return mail($recipient, $subject, $content);
    }
}
