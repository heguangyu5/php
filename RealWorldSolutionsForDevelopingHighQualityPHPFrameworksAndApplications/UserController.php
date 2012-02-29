<?php

require_once 'CryptHelper.php';
require_once 'View.php';
require_once 'ErrorView.php';
require_once 'UsersTableDataGateway.php';

class UserController
{
    protected $gateway;

    public function __construct(TableDataGateway $gateway)
    {
        $this->gateway = $gateway;
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

        $code = CryptHelper::getConfirmationCode();
        $this->gateway->updateUserWithConfirmationCode(
            $_POST['email'], $code
        );


        mail(
            $_POST['email'],
            'Password Reset',
            'Confirmation code: ' . $code
        );
        return new View('passwordResetRequested');
    }
}
