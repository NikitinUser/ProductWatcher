<?php

namespace App\Enums;

class PricesResultEnum
{
    /**
     * Успешно получили новую цену
     */
    public const SUCCESS = 'success';

    /**
     * Не смогли получить новую цену, попробуем повторить
     */
    public const REPEAT = 'repeat';

    /**
     * Больше не можем отслеживать товар
     */
    public const UNAVAILABLE = 'unavailable';
}
