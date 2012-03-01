<?php
require_once 'UserController.php';
require_once 'PHPUnit/Framework/TestCase.php';

class UserControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->gateway = $this->getMock(
            'UsersTableDataGateway', array(), array(), '', FALSE
        );
        $this->controller = new UserController($this->gateway);
    }

    protected function tearDown()
    {
        unset($this->gateway);
        unset($this->controller);
        $_POST = array();
    }

//    public function testDisplayErrorViewWhenNoEmailAddressGiven()
//    {
//        $_POST['email'] = '';
//        $view = $this->controller->resetPasswordAction();
//        $this->assertInstanceOf('ErrorView', $view);
//    }
//
//    public function testDisplayViewWhenEmailAddressGiven()
//    {
//        $_POST['email'] = 'heguangyu5';
//        $view = $this->controller->resetPasswordAction();
//        $this->assertInstanceOf('View', $view);
//    }

    public function testDisplaysErrorViewWhenNoUserFound()
    {
        $this->gateway
             ->expects($this->once())
             ->method('findUserByEmail')
             ->will($this->returnValue(FALSE));

        $_POST['email'] = 'nosense';
        $result = $this->controller->resetPasswordAction();
        $this->assertInstanceOf('ErrorView', $result);
    }
}
