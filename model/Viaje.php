<?php

class Viaje
{
    private int $id;
    private int $organizadorId;
    private string $destino;
    private float $presupuestoLimite;
    private ?string $fechaInicio;
    private ?string $fechaFin;

    public function __construct(
        int $id,
        int $organizadorId,
        string $destino,
        float $presupuestoLimite,
        ?string $fechaInicio = null,
        ?string $fechaFin = null
    ) {
        $this->id = $id;
        $this->organizadorId = $organizadorId;
        $this->destino = $destino;
        $this->presupuestoLimite = $presupuestoLimite;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrganizadorId(): int
    {
        return $this->organizadorId;
    }

    public function getDestino(): string
    {
        return $this->destino;
    }

    public function getPresupuestoLimite(): float
    {
        return $this->presupuestoLimite;
    }

    public function getFechaInicio(): ?string
    {
        return $this->fechaInicio;
    }

    public function getFechaFin(): ?string
    {
        return $this->fechaFin;
    }
}

