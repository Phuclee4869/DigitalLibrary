<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use App\Exports\LibraryReportExport;
use Illuminate\Http\Request;

class ReportController extends Controller {
    protected $reportRepo;

    public function __construct(ReportRepository $reportRepo) {
        $this->reportRepo = $reportRepo;
    }

    public function index() {
        $summary = $this->reportRepo->getSummary();
        $topBooks = $this->reportRepo->getTopBorrowedBooks(10);
        $monthlyStats = $this->reportRepo->getMonthlyBorrowAndFines();

        return view('reports.index', compact('summary', 'topBooks', 'monthlyStats'));
    }

    public function exportExcel(ReportRepository $repo) {
        $export = new LibraryReportExport($repo);
        return $export->exportCsvResponse();
    }
}