<?php

class ParticipanteViaje
{
    private int $viajeId;
    private int $usuarioId;
    private string $estadoInvitacion;

    public function __construct(int $viajeId, int $usuarioId, string $estadoInvitacion)
    {
        $this->viajeId = $viajeId;
        $this->usuarioId = $usuarioId;
        $this->estadoInvitacion = $estadoInvitacion;
    }

    public function getViajeId(): int
    {
        return $this->viajeId;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getEstadoInvitacion(): string
    {
        return $this->estadoInvitacion;
    }
}

