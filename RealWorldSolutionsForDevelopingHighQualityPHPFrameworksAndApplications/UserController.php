<?php

require_once 'View.php';
require_once 'ErrorView.php';

class UserController
{
    protected $gateway;
    protected $mailer;
    protected $cryptHelper;

    public function __construct(TableDataGateway $gateway, Mailer $mailer, CryptHelper $cryptHelper)
    {
        $this->gateway = $gateway;
        $this->mailer = $mailer;
        $this->cryptHelper = $cryptHelper;
    }

    public function resetPasswordAction()
    {
        if (!isset($_POST['email'])) {
            return new ErrorView(
                'resetPassword', 'No email specified'
            );
        }
        $record = $this->gateway->findUserByEmail(
            $_POST['email']
        );
        if ($record === FALSE) {
            return new ErrorView(
                'resetPassword',
                'No user with email ' . $_POST['email']
            );
        }

        $code = $this->cryptHelper->getConfirmationCode();
        $this->gateway->updateUserWithConfirmationCode(
            $_POST['email'], $code
        );


        $this->mailer->send(
            $_POST['email'],
            'Password Reset',
            'Confirmation code: ' . $code
        );
        return new View('passwordResetRequested');
    }
}
