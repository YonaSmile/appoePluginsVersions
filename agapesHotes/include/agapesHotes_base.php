<?php
function getDayInCycle(DateTime $startDay, $cycles, DateTime $endDay)
{
    if($startDay <= $endDay) {

        $ptiInterval = $startDay->diff($endDay);
        $joursDiff = $ptiInterval->format('%r%a');

        if ($joursDiff > 0) {
            return ($joursDiff % ($cycles * 7))+1;
        }
    }
    return false;
}