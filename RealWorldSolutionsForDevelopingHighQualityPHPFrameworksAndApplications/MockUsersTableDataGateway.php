<?php

require_once 'TableDataGateway.php';

class MockUsersTableDataGateway extends TableDataGateway
{
    public function findUserByEmail($email)
    {
        return array(
            'id'    => 42,
            'username' => '何广宇',
            'email' => 'heguangyu5',
            'code' => NULL
        );
    }

    public function updateUserWithConfirmationCode($email, $code)
    {
        throw new PDOException();
    }
}
