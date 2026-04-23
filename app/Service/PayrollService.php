<?php

namespace App\Service;

use App\DTO\PayrollSchedule;
use Carbon\Carbon;

/**
 * Service to calculate payroll dates based on given parameters.
 *
 * The service can return payroll dates for:
 * - A specific year (full year)
 * - A range of months (from Y-m to Y-m)
 * - The remaining months of the current year (default)
 *
 * Each month includes:
 * - Salary date: Last day of the month, adjusted for weekends
 * - Bonus date: 15th of the month, adjusted for weekends
 */
class PayrollService
{
    public function getDates($year = null, $from = null, $to = null): array
    {
        // range provided: from=Y-m, to=Y-m
        if ($from && $to) {
            return $this->range($from, $to);
        }

        // year provided: no range
        if ($year) {
            return $this->fullYear($year);
        }

        // default: rest of the current year
        return $this->remainingYear();
    }

    private function remainingYear(): array
    {
        return $this->months(now()->year, now()->month, 12);
    }

    private function fullYear(int $year): array
    {
        return $this->months($year, 1, 12);
    }

    private function range(string $from, string $to): array
    {
        $start = Carbon::createFromFormat('Y-m', $from);
        $end = Carbon::createFromFormat('Y-m', $to);

        $months = [];

        while ($start <= $end) {
            $months[] = $this->buildMonthlyPayroll($start->year, $start->month);
            $start->addMonth();
        }

        return $months;
    }

    private function months(int $year, int $startMonth, int $endMonth): array
    {
        return collect(range($startMonth, $endMonth))
            ->map(fn ($month) => $this->buildMonthlyPayroll($year, $month))
            ->toArray();
    }

    /**
     * Build the payroll dates for a given month and year.
     *
     * @param  int  $year  The year for which to calculate the payroll dates.
     * @param  int  $month  The month for which to calculate the payroll dates.
     */
    private function buildMonthlyPayroll(int $year, int $month): PayrollSchedule
    {
        $date = Carbon::create($year, $month, 1);

        return new PayrollSchedule(
            month: $date->format('F'),
            salaryPaymentDate: $this->salaryPayDate($date)->toDateString(),
            bonusPaymentDate: $this->bonusPayDate($date)->toDateString(),
        );
    }

    /**
     * Calculate the salary pay date for a given month.
     *
     * The salary pay date is the last day of the month. If the last day falls on a weekend,
     * it is adjusted to the previous weekday (Friday).
     *
     * @param  Carbon  $date  The date representing the month and year for which to calculate the salary pay date.
     * @return Carbon The calculated salary pay date.
     */
    private function salaryPayDate(Carbon $date): Carbon
    {
        $lastDay = $date->copy()->endOfMonth();

        return $lastDay->isWeekend()
            ? $lastDay->previousWeekday()
            : $lastDay;
    }

    /**
     * Calculate the bonus pay date for a given month.
     *
     * The bonus pay date is the 15th of the month. If the 15th falls on a weekend,
     * it is adjusted to the next Wednesday.
     *
     * @param  Carbon  $date  The date representing the month and year for which to calculate the bonus pay date.
     * @return Carbon The calculated bonus pay date.
     */
    private function bonusPayDate(Carbon $date): Carbon
    {
        $bonusDay = $date->copy()->day(15);

        return $bonusDay->isWeekend()
            ? $bonusDay->next(Carbon::WEDNESDAY) // Adjust to next Wednesday if it falls on a weekend
            : $bonusDay;
    }
}
