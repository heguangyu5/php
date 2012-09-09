<?php
require_once 'Zend/Mime.php';
require_once 'Zend/Mime/Decode.php';
require_once 'Zend/Mail/Protocol/Smtp.php';


class Smtp
{
    /**
     * Remote smtp hostname or i.p.
     *
     * @var string
     */
    protected $_host;


    /**
     * Port number
     *
     * @var integer|null
     */
    protected $_port;


    /**
     * Local client hostname or i.p.
     *
     * @var string
     */
    protected $_name = 'localhost';


    /**
     * Authentication type OPTIONAL
     *
     * @var string
     */
    protected $_auth;


    /**
     * Config options for authentication
     *
     * @var array
     */
    protected $_config;


    /**
     * Instance of Zend_Mail_Protocol_Smtp
     *
     * @var Zend_Mail_Protocol_Smtp
     */
    protected $_connection;

    protected $_rawHeader;
    protected $_rawContent;
    protected $_returnPath;
    protected $_recipients;

    /**
     * Constructor.
     *
     * @param  string $host OPTIONAL (Default: 127.0.0.1)
     * @param  array|null $config OPTIONAL (Default: null)
     * @return void
     *
     * @todo Someone please make this compatible
     *       with the SendMail transport class.
     */
    public function __construct($host = '127.0.0.1', Array $config = array())
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }

        $this->_host = $host;
        $this->_config = $config;
    }


    /**
     * Class destructor to ensure all open connections are closed
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->_connection instanceof Zend_Mail_Protocol_Smtp) {
            try {
                $this->_connection->quit();
            } catch (Zend_Mail_Protocol_Exception $e) {
                // ignore
            }
            $this->_connection->disconnect();
        }
    }


    /**
     * Sets the connection protocol instance
     *
     * @param Zend_Mail_Protocol_Abstract $client
     *
     * @return void
     */
    public function setConnection(Zend_Mail_Protocol_Abstract $connection)
    {
        $this->_connection = $connection;
    }


    /**
     * Gets the connection protocol instance
     *
     * @return Zend_Mail_Protocol|null
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    public function setRawHeader($header)
    {
        $this->_rawHeader = $header;
    }

    public function setRawContent($content)
    {
        $this->_rawContent = str_replace("\r\n", "\n", $content);
    }

    public function setReturnPath($returnPath)
    {
        $this->_returnPath = $returnPath;
    }

    public function setRecipients(Array $recipients)
    {
        $this->_recipients = $recipients;
    }

    /**
     * Send an email via the SMTP connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @return true or false
     */
    public function sendMail()
    {
        // If sending multiple messages per session use existing adapter
        if (!($this->_connection instanceof Zend_Mail_Protocol_Smtp)) {
            // Check if authentication is required and determine required class
            $connectionClass = 'Zend_Mail_Protocol_Smtp';
            if ($this->_auth) {
                $connectionClass .= '_Auth_' . ucwords($this->_auth);
            }
            if (!class_exists($connectionClass)) {
                require_once 'Zend/Loader.php';
                Zend_Loader::loadClass($connectionClass);
            }
            $this->setConnection(new $connectionClass($this->_host, $this->_port, $this->_config));
            $this->_connection->connect();
            $this->_connection->helo($this->_name);
        } else {
            // Reset connection to ensure reliable transaction
            $this->_connection->rset();
        }

        // Set sender email address
        $this->_connection->mail($this->_returnPath);

        // Set recipient forward paths
        foreach ($this->_recipients as $recipient) {
            $this->_connection->rcpt($recipient);
        }

        try {
            // Issue DATA command to client
            $this->_connection->data($this->_rawHeader . Zend_Mime::LINEEND . $this->_rawContent);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
