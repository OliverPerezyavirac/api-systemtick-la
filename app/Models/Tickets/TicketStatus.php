<?php

namespace App\Models\Tickets;

class TicketStatus
{
    const NEW = 'new';
    const OPEN = 'open';
    const IN_PROGRESS = 'in_progress';
    const RESOLVED = 'resolved';
    const CLOSED = 'closed';

    private static $validTransitions = [
        self::NEW => [self::OPEN],
        self::OPEN => [self::IN_PROGRESS],
        self::IN_PROGRESS => [self::RESOLVED],
        self::RESOLVED => [self::CLOSED],
        self::CLOSED => [self::OPEN],
    ];

    /**
     * Verifica si es una transición válida entre estados.
     */
    public static function isValidTransition($currentStatus, $newStatus)
    {
        if (!isset(self::$validTransitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, self::$validTransitions[$currentStatus]);
    }

    /**
     * Obtiene las transiciones válidas para un estado.
     */
    public static function getValidTransitions($currentStatus)
    {
        return self::$validTransitions[$currentStatus] ?? [];
    }

    /**
     * Obtiene todos los estados posibles.
     */
    public static function getAllStatuses()
    {
        return array_keys(self::$validTransitions);
    }
} 