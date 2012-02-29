<?php

require_once 'TableDataGateway.php';

class UsersTableDataGateway extends TableDataGateway
{
    protected $db;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function findUserByEmail($email)
    {
        $statement = $this->db->prepare(
            'SELECT * FROM Users WHERE email=:email;'
        );
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUserWithConfirmationCode($email, $code)
    {
        $statement = $this->db->prepare(
            'UPDATE Users SET code=:code WHERE email=:email;'
        );
        $statement->bindValue(':code', $code, PDO::PARAM_STR);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
    }
}
