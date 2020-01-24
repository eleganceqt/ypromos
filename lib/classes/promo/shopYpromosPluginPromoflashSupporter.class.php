<?php

class shopYpromosPluginPromoflashSupporter
{
    /**
     * Get an instance of static.
     *
     * @return shopYpromosPluginPromoflashSupporter
     */
    public static function factory()
    {
        return new static();
    }

    public static function isPromoflashValid($promoflash)
    {
        $factory = static::factory();

        $compareFormat = 'Y.m.d';

        $todayInCompareFormat          = date($compareFormat);
        $startDateInCompareFormat      = date($compareFormat, strtotime($promoflash['end_date']));
        $endDateInCompareFormat        = date($compareFormat, strtotime($promoflash['end_date']));
        $sevenDaysForthInCompareFormat = date($compareFormat, strtotime($promoflash['start_date'] . '+7 day'));

        if ($factory->isValidDateFormat($promoflash['start_date'], 'Y-m-d 00:00:00') &&
            $factory->isValidDateFormat($promoflash['end_date'], 'Y-m-d 23:59:59')) {

            if ($endDateInCompareFormat > $sevenDaysForthInCompareFormat) {
                return false;
            }

            if ($todayInCompareFormat > $endDateInCompareFormat) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if start date input is valid.
     *
     * @param string $startDate
     * @param string $inFormat
     *
     * @return bool
     */
    public static function isStartDateInputValid($startDate, $inFormat = 'd.m.Y')
    {
        $factory = static::factory();

        $compareFormat = 'Y.m.d';

        if ($factory->isValidDateFormat($startDate, $inFormat)) {

            $todayInCompareFormat     = date($compareFormat);
            $startDateInCompareFormat = date($compareFormat, strtotime($startDate));

            if ($startDateInCompareFormat >= $todayInCompareFormat) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if end date input is valid.
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $inFormat
     *
     * @return bool
     */
    public static function isEndDateInputValid($startDate, $endDate, $inFormat = 'd.m.Y')
    {
        $factory = static::factory();

        $compareFormat = 'Y.m.d';

        if ($factory->isValidDateFormat($endDate, $inFormat)) {

            $todayInCompareFormat          = date($compareFormat);
            $endDateInCompareFormat        = date($compareFormat, strtotime($endDate));
            $sevenDaysForthInCompareFormat = date($compareFormat, strtotime($startDate . '+7 day'));

            if ($endDateInCompareFormat >= $todayInCompareFormat && $endDateInCompareFormat <= $sevenDaysForthInCompareFormat) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate a date to specified format.
     *
     * @param string $date
     * @param string $format
     *
     * @return bool
     */
    public function isValidDateFormat($date, $format)
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }
}