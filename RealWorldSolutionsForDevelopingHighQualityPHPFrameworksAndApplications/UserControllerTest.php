<?php
require_once 'UserController.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'UsersTableDataGateway.php';

class UserControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $db = new PDO('sqlite::memory:');
        $db->exec(file_get_contents('schema.sql'));
        $db->exec("insert into Users(username, email) values('何广宇', 'heguangyu5')");

        $this->gateway = new UsersTableDataGateway($db);
        $this->controller = new UserController($this->gateway);
    }

    protected function tearDown()
    {
        unset($this->gateway);
        unset($this->controller);
        $_POST = array();
    }

    public function testDisplayErrorViewWhenNoEmailAddressGiven()
    {
        $_POST['email'] = '';
        $view = $this->controller->resetPasswordAction();
        $this->assertInstanceOf('ErrorView', $view);
    }

    public function testDisplayViewWhenEmailAddressGiven()
    {
        $_POST['email'] = 'heguangyu5';
        $view = $this->controller->resetPasswordAction();
        $this->assertInstanceOf('View', $view);
    }
}
