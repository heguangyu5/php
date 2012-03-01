<?php
require_once 'UserController.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'UsersTableDataGateway.php';
require_once 'Mailer.php';
require_once 'CryptHelper.php';

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
        $this->cryptHelper = $this->getMock(
            'CryptHelper', array(), array(), '', FALSE
        );
        $this->controller = new UserController($this->gateway, $this->mailer, $this->cryptHelper);
    }

    protected function tearDown()
    {
        unset($this->gateway);
        unset($this->mailer);
        unset($this->cryptHelper);
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

    public function testStoresConfirmationCode()
    {
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
        
        $this->cryptHelper
             ->expects($this->once())
             ->method('getConfirmationCode')
             ->will($this->returnValue('123456789'));

        $this->gateway
             ->expects($this->once())
             ->method('updateUserWithConfirmationCode')
             ->with('heguangyu5', '123456789');
      

       $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with('heguangyu5');

        $_POST['email'] = 'heguangyu5';

        $this->controller->resetPasswordAction();
    }
}
