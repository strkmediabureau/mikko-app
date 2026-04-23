<?php

namespace App\DTO;

class PayrollSchedule
{
    /**
     * @param  string  $month  The month for which the payroll schedule is generated.
     * @param  string  $salaryPaymentDate  The date on which the salary will be paid.
     * @param  string  $bonusPaymentDate  The date on which the bonus will be paid.
     */
    public function __construct(
        public string $month,
        public string $salaryPaymentDate,
        public string $bonusPaymentDate
    ) {}
}
