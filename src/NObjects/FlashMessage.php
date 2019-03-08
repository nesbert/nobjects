<?php
namespace NObjects;

/**
 * Session messages helper used for application notices.
 *
 * @author Nesbert Hidalgo
 **/
class FlashMessage
{
    private static $messages = array();

    private $text;
    private $type;

    const SESSION_NAME = 'NFLASH_MESSAGE';
    const TYPE_NOTICE = 'notice';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';

    /**
     * @param $message
     * @param string $type
     */
    public function __construct($message, $type = self::TYPE_NOTICE)
    {
        $this->setText($message);
        $this->setType($type);
    }

    /**
     * Returns current messages.
     * @param bool $unsetMessages
     * @return FlashMessage|FlashMessage[]|bool
     */
    public static function get($unsetMessages = true)
    {
        $messages = self::getMessages();

        if (count($messages)) {
            if (count($messages) == 1) {
                $return = $messages[0];
            } else {
                $return = $messages;
            }
            if ($unsetMessages) {
                unset($_SESSION[self::SESSION_NAME]);
            }
            return $return;
        }

        return false;
    }

    /**
     * Count number of messages.
     *
     * @static
     * @return int
     */
    public static function count()
    {
        return count(self::getMessages());
    }

    /**
     * Sets and unsets $_SESSION['FLASH_MESSAGE']. Used by application notices.
     *
     * @param string $message
     * @param string $type
     * @return FlashMessage[]
     **/
    public static function notice($message, $type = self::TYPE_NOTICE)
    {
        // use array type for storing data
        self::$messages[] = array($message, $type);

        $_SESSION[self::SESSION_NAME] = &self::$messages;

        return self::getMessages();
    }

    /**
     * Alias for self::message($message, 'error').
     *
     * @param string $message Message for flash notice
     * @return FlashMessage[]
     **/
    public static function error($message = null)
    {
        return self::notice($message, self::TYPE_ERROR);
    }

    /**
     * Alias for self::message($message, 'warning').
     *
     * @param string $message Message for flash notice
     * @return FlashMessage[]
     **/
    public static function warning($message = null)
    {
        return self::notice($message, self::TYPE_WARNING);
    }

    /**
     * Alias for self::message($message, 'success').
     *
     * @param string $message Message for flash notice
     * @return FlashMessage[]
     **/
    public static function success($message = null)
    {
        return self::notice($message, self::TYPE_SUCCESS);
    }

    /**
     * @static
     * @return FlashMessage[]
     */
    public static function getMessages()
    {
        if (empty(self::$messages) && !empty($_SESSION[self::SESSION_NAME])) {
            self::$messages = &$_SESSION[self::SESSION_NAME];
        }

        $messages = array();

        foreach (self::$messages as $data) {
            $messages[] = new FlashMessage($data[0], $data[1]);
        }

        return $messages;
    }

    /**
     * Reset session message storage.
     *
     * @static
     * @return void
     */
    public static function reset()
    {
        self::$messages = array();
        if (isset($_SESSION[self::SESSION_NAME])) {
            unset($_SESSION[self::SESSION_NAME]);
        }
    }

    // getters & setters

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }
}
