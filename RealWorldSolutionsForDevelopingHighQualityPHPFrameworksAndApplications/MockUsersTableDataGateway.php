<?php

require_once 'TableDataGateway.php';

class MockUsersTableDataGateway extends TableDataGateway
{
    public function findUserByEmail($email)
    {
        return FALSE;
    }
}
