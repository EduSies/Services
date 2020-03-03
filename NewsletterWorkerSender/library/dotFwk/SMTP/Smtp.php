<?php
class dotFwk_SMTP_Smtp extends Zend_Mail_Transport_Smtp
{
    /**
     * Send a mail using this transport
     *
     * @param  Zend_Mail $mail
     * @access public
     * @return void
     * @throws Zend_Mail_Transport_Exception if mail is empty
     */
    public function send(Zend_Mail $mail)
    {
        $connection = $this->getConnection();
        if ($connection instanceof Zend_Mail_Protocol_Smtp) {
            $connection->resetLog();
        }
        return parent::send($mail);
    }
}
