<?php

use Symfony\Component\Intl\Timezones;

if (!function_exists('timezones')) {
    /**
     * Возвращает список часовых поясов.
     * 
     * @return array
     */
    function timezones() {
        $timezones = Timezones::getNames(config('app.locale'));

        $result = [];

        foreach ($timezones as $key => $name) {
            $tz = new \StdClass;

            $tz->offset = Timezones::getRawOffset($key);
            $tz->name = Timezones::getGmtOffset($key) . ' ' . $name;
            $tz->value = $key;

            $result[] = $tz;
        }

        array_multisort(array_column($result, 'offset'), SORT_ASC,
                        array_column($result, 'name'), SORT_ASC,
                        $result);

        return $result;
    }
}
