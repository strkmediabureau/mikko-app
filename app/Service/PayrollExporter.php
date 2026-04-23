<?php

namespace App\Service;

use App\DTO\PayrollSchedule;

/**
 * This class is responsible for exporting payroll schedules in CSV format.
 * It takes an array of PayrollSchedule objects and generates a CSV file with the relevant data.
 */
class PayrollExporter
{
    public function generatePayrollSchedule(array $data): void
    {
        $handle = fopen('php://output', 'w');

        // headers
        fputcsv($handle, [
            'Month',
            'Salary Payment Date',
            'Bonus Payment Date'
        ]);

        foreach ($data as $row) {
            /** @var PayrollSchedule $row */
            fputcsv($handle, [
                $row->month,
                $row->salaryPaymentDate,
                $row->bonusPaymentDate,
            ]);
        }

        fclose($handle);
    }
}