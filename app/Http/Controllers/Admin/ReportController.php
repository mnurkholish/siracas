<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(Request $request)
    {
        $report = $this->reportService->build(
            $request->query('bulan'),
            $request->query('tahun')
        );

        return view('admin.reports.index', $report);
    }

    public function export(Request $request)
    {
        $report = $this->reportService->build(
            $request->query('bulan'),
            $request->query('tahun')
        );

        $filename = sprintf(
            'laporan-siracas-%s-%s.xlsx',
            str_pad((string) $report['period']['month'], 2, '0', STR_PAD_LEFT),
            $report['period']['year']
        );

        return Excel::download(new ReportExport($report), $filename);
    }
}
