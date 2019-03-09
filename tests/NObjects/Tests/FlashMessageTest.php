<?php
namespace NObjects\Tests;

use NObjects\FlashMessage;

class FlashMessageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FlashMessage
     */
    private $o;

    public function setUp()
    {
        FlashMessage::reset();
        $this->o = new FlashMessage('Hello world!');
    }

    public function testNotice()
    {
        FlashMessage::notice('Notice message!');
        $this->assertEquals('Notice message!', $_SESSION[FlashMessage::SESSION_NAME][0][0]);
        $this->assertEquals(FlashMessage::TYPE_NOTICE, $_SESSION[FlashMessage::SESSION_NAME][0][1]);
    }

    public function testError()
    {
        FlashMessage::error('Error message!');
        $this->assertEquals('Error message!', $_SESSION[FlashMessage::SESSION_NAME][0][0]);
        $this->assertEquals(FlashMessage::TYPE_ERROR, $_SESSION[FlashMessage::SESSION_NAME][0][1]);
    }

    public function testWarning()
    {
        FlashMessage::warning('Warning message!');
        $this->assertEquals('Warning message!', $_SESSION[FlashMessage::SESSION_NAME][0][0]);
        $this->assertEquals(FlashMessage::TYPE_WARNING, $_SESSION[FlashMessage::SESSION_NAME][0][1]);
    }

    public function testSuccess()
    {
        FlashMessage::success('Success message!');
        $this->assertEquals('Success message!', $_SESSION[FlashMessage::SESSION_NAME][0][0]);
        $this->assertEquals(FlashMessage::TYPE_SUCCESS, $_SESSION[FlashMessage::SESSION_NAME][0][1]);
    }

    public function testGetMessages()
    {
        $messages = FlashMessage::getMessages();
        $this->assertEquals(0, count($messages));

        $list = array(
            array('Email required!', FlashMessage::TYPE_ERROR),
            array('Boom!', FlashMessage::TYPE_NOTICE),
        );

        foreach ($list as $notice) {
            FlashMessage::notice($notice[0], $notice[1]);
        }

        $messages = FlashMessage::getMessages();
        $this->assertEquals(2, count($messages));

        foreach ($list as $k => $notice) {
            $this->assertEquals($notice[0], $messages[$k]->getText());
            $this->assertEquals($notice[1], $messages[$k]->getType());
        }

        // loading from session
        FlashMessage::reset();

        $_SESSION[FlashMessage::SESSION_NAME] = array(
            array('Session message!', FlashMessage::TYPE_SUCCESS)
        );

        $messages = FlashMessage::getMessages();
        $this->assertEquals(1, count($messages));
    }

    public function testGet()
    {
        $this->assertFalse(FlashMessage::get());
        FlashMessage::success($msg = 'User saved!');

        if ($message = FlashMessage::get()) {
            $this->assertEquals($msg, $message->getText());
        } else {
            $this->fail('Expected message!');
        }

        FlashMessage::success($msg2 = 'User profile saved!');

        if ($messages = FlashMessage::get()) {
            $this->assertEquals($msg, $messages[0]->getText());
            $this->assertEquals($msg2, $messages[1]->getText());
        } else {
            $this->fail('Expected messages!');
        }
    }

    public function testCount()
    {
        FlashMessage::error('Email required!');
        FlashMessage::error('Name Required!');
        $this->assertEquals(2, FlashMessage::count());
    }

    public function testReset()
    {
        FlashMessage::error('Email required!');
        FlashMessage::error('Name Required!');
        FlashMessage::reset();

        $this->assertEquals(0, FlashMessage::count());
        $this->assertTrue(!isset($_SESSION[FlashMessage::SESSION_NAME]));
    }

    public function testSetGetText()
    {
        $this->assertEquals($this->o, $this->o->setText('test1'));
        $this->assertEquals('test1', $this->o->getText());
    }

    public function testSetGetType()
    {
        $this->assertEquals($this->o, $this->o->setType(FlashMessage::TYPE_ERROR));
        $this->assertEquals(FlashMessage::TYPE_ERROR, $this->o->getType());
    }
}
