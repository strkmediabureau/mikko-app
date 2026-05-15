<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PayrollApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user);
    }

    public function test_it_returns_current_year_payroll(): void
    {
        $response = $this->getJson('/api/payroll');

        $response->assertOk();

        $response->assertJsonStructure([
            '*' => [
                'month',
                'salaryPaymentDate',
                'bonusPaymentDate',
            ],
        ]);
    }

    public function test_it_returns_payroll_for_specific_year()
    {
        $response = $this->getJson('/api/payroll/'. 2027);

        $response->assertOk();

        $response->assertJsonCount(12);
    }

    public function test_it_filters_payroll_by_date_range()
    {
        $response = $this->getJson(
            '/api/payroll?from=2026-02&to=2026-04'
        );

        $response->assertOk();

        $response->assertJsonCount(3);
    }

    public function test_salary_is_not_paid_in_weekend()
    {
        $response = $this->getJson('/api/payroll/'. 2026);

        $response->assertOk();

        $data = $response->json();

        foreach ($data as $row) {

            $date = Carbon::parse($row['salaryPaymentDate']);

            $this->assertFalse(
                $date->isWeekend(),
                'Salary payment date falls on weekend'
            );
        }
    }

    public function test_bonus_rule_uses_first_wednesday_after_weekend()
    {
        $response = $this->getJson('/api/payroll/'. 2026);

        $response->assertOk();

        $data = $response->json();

        foreach ($data as $row) {

            $bonusDate = Carbon::parse($row['bonusPaymentDate']);

            $this->assertFalse($bonusDate->isWeekend());
        }
    }

    public function test_it_exports_csv()
    {
        $response = $this->get('/api/payroll/exporter');

        $response->assertOk();

        $response->assertHeader(
            'content-disposition',
            'attachment; filename=payroll.csv'
        );

    }
}
