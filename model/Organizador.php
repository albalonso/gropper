<?php

require_once __DIR__ . '/Usuario.php';

class Organizador extends Usuario
{
    public function __construct(int $id, string $nombre, string $email)
    {
        parent::__construct($id, $nombre, $email, 'organizador');
    }
}

