<?php

class Reserva
{
    private int $id;
    private int $viajeId;
    private int $servicioId;
    private int $personas;
    private float $precioAplicado;

    public function __construct(int $id, int $viajeId, int $servicioId, int $personas, float $precioAplicado)
    {
        $this->id = $id;
        $this->viajeId = $viajeId;
        $this->servicioId = $servicioId;
        $this->personas = $personas;
        $this->precioAplicado = $precioAplicado;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getViajeId(): int
    {
        return $this->viajeId;
    }

    public function getServicioId(): int
    {
        return $this->servicioId;
    }

    public function getPersonas(): int
    {
        return $this->personas;
    }

    public function getPrecioAplicado(): float
    {
        return $this->precioAplicado;
    }
}

