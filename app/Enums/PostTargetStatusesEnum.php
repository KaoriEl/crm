<?php


namespace App\Enums;


use App\Traits\EnumStatusesTrait;

class PostTargetStatusesEnum
{
    use EnumStatusesTrait;

    public const SENT_FOR_MODERATION_STATUS = 'sent_for_moderation';

    public const SUCCESSFUL_MODERATED_STATUS = 'successful_moderated';

    public const NOT_SUCCESSFUL_MODERATED_STATUS = 'not_successful_moderated';

    public static $statusesName = [
        self::SENT_FOR_MODERATION_STATUS => 'Отправлено на модерацию',
        self::SUCCESSFUL_MODERATED_STATUS => 'Прошло модерацию',
        self::NOT_SUCCESSFUL_MODERATED_STATUS => 'Не прошло модерацию',
    ];

    public static $statusesBadgeClass = [
        self::SENT_FOR_MODERATION_STATUS => 'warning',
        self::SUCCESSFUL_MODERATED_STATUS => 'done',
        self::NOT_SUCCESSFUL_MODERATED_STATUS => 'danger',
    ];
}
