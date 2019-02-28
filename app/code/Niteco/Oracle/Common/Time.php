<?php
/**
 * Niteco
 * User: tinh.ngo
 * Date: 2/28/2019
 * Time: 11:27 AM
 */

namespace Niteco\Oracle\Common;

class Time {

    protected $timezoneInterface;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    )
    {
        $this->timezoneInterface = $timezoneInterface;
    }

    /**
     * @param string $dateTime
     * @return string $dateTime as time zone
     */
    public function getTimeAccordingToTimeZone($dateTime)
    {
        // for get current time according to time zone
        $today = $this->timezoneInterface->date()->format('m/d/y H:i:s');

        // for convert date time according to magento time zone
        $dateTimeAsTimeZone = $this->timezoneInterface
            ->date(new \DateTime($dateTime))
            ->format('m/d/y H:i:s');
        return $dateTimeAsTimeZone;
    }

}