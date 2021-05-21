<?php

namespace rOpenDev\Google;

trait SleepTrait
{
    /** @var int Time we need to wait between two request * */
    protected $sleep = 0;

    /**
     * Chainable `$waitBetweenRequests` setter.
     *
     * @return self
     */
    public function setSleep($seconds)
    {
        $this->sleep = $seconds * 1000000;

        return $this;
    }

    /**
     * Return the time the script need to sleep.
     *
     * @return int Microseconds
     */
    protected function getSleep()
    {
        $halfSleep = $this->sleep / 2;
        $sleepMin = (int) floor($this->sleep - $halfSleep);
        $sleepMax = (int) ceil($this->sleep + $halfSleep);

        return rand($sleepMin, $sleepMax);
    }

    /**
     * Exec sleep.
     *
     * @return int The time we rest
     */
    public function execSleep()
    {
        if ($this->previousRequestUsedCache) {
            return;
        }

        if ($this->sleep) {
            $sleep = $this->getSleep();
            usleep($sleep);

            return $sleep;
        }
    }

    /**
     * Exec a half sleep.
     *
     * @return int The time we rest
     */
    public function execHalfSleep()
    {
        if ($this->previousRequestUsedCache) {
            return;
        }

        if ($this->sleep) {
            $sleep = round($this->getSleep() / 2);
            usleep($sleep);

            return $sleep;
        }
    }
}
