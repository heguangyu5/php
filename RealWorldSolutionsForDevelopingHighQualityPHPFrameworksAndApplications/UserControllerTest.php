<?php
require_once 'UserController.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'MockUsersTableDataGateway.php';

class UserControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->gateway = new MockUsersTableDataGateway();
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

    public function testDisplaysErrorViewWhenNoUserFound()
    {
        $_POST['email'] = 'nosense';
        $result = $this->controller->resetPasswordAction();
        $this->assertInstanceOf('ErrorView', $result);
    }
}
