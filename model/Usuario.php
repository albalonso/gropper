<?php

class Usuario
{
    protected int $id;
    protected string $nombre;
    protected string $email;
    protected string $tipo;

    public function __construct(int $id, string $nombre, string $email, string $tipo)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->tipo = $tipo;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function esOrganizador(): bool
    {
        return $this->tipo === 'organizador';
    }

    public function esAcompanante(): bool
    {
        return $this->tipo === 'acompanante';
    }
}

