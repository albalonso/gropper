<?php

class Servicio
{
    private int $id;
    private string $tipo;
    private string $descripcion;
    private float $precioTotal;
    private string $destino;
    private ?string $imagen;
    private string $detalle;

    public function __construct(
        int $id,
        string $tipo,
        string $descripcion,
        float $precioTotal,
        string $destino,
        ?string $imagen = null,
        string $detalle = ''
    ) {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->descripcion = $descripcion;
        $this->precioTotal = $precioTotal;
        $this->destino = $destino;
        $this->imagen = $imagen;
        $this->detalle = $detalle;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getPrecioTotal(): float
    {
        return $this->precioTotal;
    }

    public function getDestino(): string
    {
        return $this->destino;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function getDetalle(): string
    {
        return $this->detalle;
    }
}

