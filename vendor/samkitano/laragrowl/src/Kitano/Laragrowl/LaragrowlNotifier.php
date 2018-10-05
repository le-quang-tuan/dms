<?php

namespace Kitano\Laragrowl;

class LaragrowlNotifier
{
    /** @var SessionStore */
    protected $session;

    /** @var array */
    protected $messages;

    /** @var array */
    protected $types = ['default', 'info', 'success', 'warning', 'error'];

    /**
     * Instantiate a new flash notification
     *
     * @param SessionStore $session
     */
    function __construct(SessionStore $session)
    {
        $this->session = $session;
    }

    /**
     * Flash an info message.
     *
     * @param string       $message
     * @param string|null  $header
     * @param bool|string  $sticky
     *
     * @return $this
     */
    public function info($message, $header = null, $sticky = null)
    {
        $this->message($message, 'info', $header, $sticky);

        return $this;
    }

    /**
     * Flash a success message.
     *
     * @param string       $message
     * @param string|null  $header
     * @param bool|string  $sticky
     *
     * @return $this
     */
    public function success($message, $header = null, $sticky = null)
    {
        $this->message($message, 'success', $header, $sticky);

        return $this;
    }

    /**
     * Flash an error message.
     *
     * @param string       $message
     * @param string|null  $header
     * @param bool|string  $sticky
     *
     * @return $this
     */
    public function error($message, $header = null, $sticky = null)
    {
        $this->message($message, 'error', $header, $sticky);

        return $this;
    }

    /**
     * Flash a warning message.
     *
     * @param string       $message
     * @param string|null  $header
     * @param bool|string  $sticky
     *
     * @return $this
     */
    public function warning($message, $header = null, $sticky = null)
    {
        $this->message($message, 'warning', $header, $sticky);

        return $this;
    }

    /**
     * Flash a general message.
     *
     * @param string       $message
     * @param string       $type
     * @param string|null  $header
     * @param bool|string  $sticky
     * @param int|string   $life
     * @param string       $group
     *
     * @return $this
     */
    public function message($message, $type = null, $header = null, $sticky = null, $life = null, $group = null)
    {
        $this->messages = $this->session->read('laragrowl_messages');

        $jgrowl['message'] = $message;
        $jgrowl['type']    = in_array($type, $this->types)                                  ? $type : 'default';
        $jgrowl['header']  = $header ==  null                                               ? ''    : $header;
        $jgrowl['sticky']  = $sticky === true ||   $sticky == 'sticky' || $sticky == 'true' ? true  : false;
        $jgrowl['life']    = $life   ==  null || ! is_numeric($life)                        ? 6000  : $life;
        $jgrowl['group']   = is_null($group)                                                ? ''    : $group;

        array_push($this->messages, $jgrowl);

        $this->session->write( 'laragrowl_messages', $this->messages );

        return $this;
    }

}