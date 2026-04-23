<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Service\PayrollExporter;
use App\Service\PayrollService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollController extends Controller
{
    public function __construct(
        private PayrollService $service,
        private PayrollExporter $exporter
    ) {}

    public function index(PayrollRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getDates(
                year: $request->year,
                from: $request->from,
                to: $request->to
            )
        );
    }

    public function exporter(PayrollRequest $request): StreamedResponse
    {
        $data = $this->service->getDates(
            year: $request->year,
            from: $request->from,
            to: $request->to
        );

        return response()->streamDownload(function () use ($data) {
            $this->exporter->generatePayrollSchedule($data);
        }, 'payroll.csv');
    }
}
