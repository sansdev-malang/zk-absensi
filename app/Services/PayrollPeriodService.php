<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Setting;

class PayrollPeriodService
{
    /**
     * Get the dynamic start and end day of the payroll period.
     * Default to 27 and 26 if not set in database.
     */
    public function getPeriodDays(): array
    {
        $startDay = Setting::where('key', 'payroll_start_date')->value('value') ?? 27;
        $endDay = Setting::where('key', 'payroll_end_date')->value('value') ?? 26;
        
        return [(int) $startDay, (int) $endDay];
    }

    /**
     * Calculate the current period based on a reference date (or now).
     * If startDay > endDay (e.g. 27 to 26), it spans across months.
     * If startDay < endDay (e.g. 1 to 30), it's within the same month.
     */
    public function calculatePeriod(Carbon $referenceDate): array
    {
        [$startDay, $endDay] = $this->getPeriodDays();
        
        $start = $referenceDate->copy();
        $end = $referenceDate->copy();

        if ($startDay > $endDay) {
            // Spans across two months (e.g., 27 to 26)
            if ($referenceDate->day <= $endDay) {
                // E.g. Date is 15 May. Period is 27 April - 26 May.
                $start->subMonth()->setDay($startDay);
                $end->setDay($endDay);
            } else {
                // E.g. Date is 28 May. Period is 27 May - 26 June.
                $start->setDay($startDay);
                $end->addMonth()->setDay($endDay);
            }
        } else {
            // Within the same month (e.g., 1 to 30)
            $start->setDay($startDay);
            $end->setDay($endDay);
        }

        // Ensure we don't accidentally set a day that doesn't exist (e.g. Feb 31)
        // Carbon's setDay handles overflow gracefully, but just in case, we can use startOfMonth/endOfMonth for edges.
        
        return [$start->toDateString(), $end->toDateString()];
    }

    /**
     * Get predefined past periods for dropdowns
     */
    public function getPredefinedPeriods(int $count = 3): array
    {
        [$startDay, $endDay] = $this->getPeriodDays();
        
        $periods = [];
        $tempNow = Carbon::now();
        
        for ($i = 0; $i < $count; $i++) {
            $start = $tempNow->copy();
            $end = $tempNow->copy();

            if ($startDay > $endDay) {
                if ($tempNow->day <= $endDay) {
                    $start->subMonth()->setDay($startDay);
                    $end->setDay($endDay);
                } else {
                    $start->setDay($startDay);
                    $end->addMonth()->setDay($endDay);
                }
            } else {
                $start->setDay($startDay);
                $end->setDay($endDay);
            }

            $label = "Periode " . $end->isoFormat('MMMM YYYY') . " (" . $start->format('d M') . " - " . $end->format('d M') . ")";
            $periods[] = [
                'label' => $label,
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ];
            
            // Move back 1 month for the next iteration
            $tempNow->subMonth();
        }
        
        return $periods;
    }
}
