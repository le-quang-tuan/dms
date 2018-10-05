<?php

namespace Kitano\Laragrowl;

interface SessionStore
{
    /**
     * Flash a message to session.
     *
     * @param $name
     * @param $data
     *
     * @return void
     */
    public function write($name, $data);

    /**
     * Get a message from session.
     *
     * @param $name
     *
     * @return mixed
     */
    public function read($name);
}
