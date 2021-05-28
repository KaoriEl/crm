<?php


namespace App\UseCases\Statistics;


use Carbon\Carbon;

class DateUseCase
{
    /**
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    public function getRangeDates($dateStart, $dateEnd): array
    {
        $dateStart = Carbon::createFromFormat('Y-m-d', $dateStart);
        $dateEnd = Carbon::createFromFormat('Y-m-d', $dateEnd);

        $resultArray = [];
        while ($dateStart->diffInDays($dateEnd) !== 0) {
            $resultArray[] = $dateStart->format('Y-m-d');
            $dateStart->add(1, 'day');
        }

        return $resultArray;
    }
}
