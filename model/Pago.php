<?php

class Pago
{
    private int $id;
    private int $reservaId;
    private int $usuarioId;
    private float $cantidad;
    private ?string $fechaPago;

    public function __construct(int $id, int $reservaId, int $usuarioId, float $cantidad, ?string $fechaPago = null)
    {
        $this->id = $id;
        $this->reservaId = $reservaId;
        $this->usuarioId = $usuarioId;
        $this->cantidad = $cantidad;
        $this->fechaPago = $fechaPago;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReservaId(): int
    {
        return $this->reservaId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getCantidad(): float
    {
        return $this->cantidad;
    }

    public function getFechaPago(): ?string
    {
        return $this->fechaPago;
    }
}

