<?php
require_once 'UserController.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'UsersTableDataGateway.php';
require_once 'Mailer.php';

class UserControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->gateway = $this->getMock(
            'UsersTableDataGateway', array(), array(), '', FALSE
        );
        $this->mailer = $this->getMock(
            'Mailer', array(), array(), '', FALSE
        );
        $this->controller = new UserController($this->gateway, $this->mailer);
    }

    protected function tearDown()
    {
        unset($this->gateway);
        unset($this->mailer);
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

    public function testSendsEmailToTheUser()
    {
        $_POST['email'] = 'heguangyu5';

        $this->gateway
             ->expects($this->once())
             ->method('findUserByEmail')
             ->with('heguangyu5')
             ->will($this->returnValue(
                array(
                    'id' => 42,
                    'username' => '何广宇',
                    'email' => 'heguangyu5',
                    'code' => NULL
                )
             ));

       $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with('heguangyu5');

       $this->controller->resetPasswordAction();
    }
}
