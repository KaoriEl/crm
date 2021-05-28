<?php


namespace App\Traits;


use LogicException;

trait EnumStatusesTrait
{

    /**
     * Возвращает название статуса
     * @param $status
     * @return string|null
     */
    public static function getStatusName($status): ?string
    {
        return self::$statusesName[$status] ?? null;
    }

    /**
     * Возвращает название статуса
     * @param $status
     * @return string|null
     */
    public static function getStatusBadgeClass($status): ?string
    {
        return self::$statusesBadgeClass[$status] ?? null;
    }
}
