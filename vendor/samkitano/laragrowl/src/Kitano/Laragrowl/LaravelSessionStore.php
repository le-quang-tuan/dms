<?php

namespace Kitano\Laragrowl;

use Illuminate\Session\Store;

class LaravelSessionStore implements SessionStore
{
    /** @var \Illuminate\Session\Store */
    private $session;

    /**
     * @param \Illuminate\Session\Store $session
     */
    function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Flash a message to session.
     *
     * @param $name
     * @param $data
     */
    public function write($name, $data)
    {
        $this->session->flash($name, $data);
    }

    /**
     * Get a message from session.
     *
     * @param  string $key
     *
     * @return array
     */
    public function read($key)
    {
        return (array) $this->session->get($key);
    }
}
