<?php

if (! function_exists('day_of_week')) {
    /**
     * Get the day of the week.
     */
    function day_of_week(?int $day = null): string
    {
        if ($day !== null && ($day < 0 || $day > 7)) {
            throw new InvalidArgumentException('Day must be between 0 and 7');
        }

        $dow = $day ?? now()->dayOfWeek;

        return $dow === 0 ? 7 : $dow;
    }
}
