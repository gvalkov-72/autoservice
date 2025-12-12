<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\WorkOrder;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function monthly()
    {
        $month = request('month', Carbon::now()->format('Y-m'));
        $from  = Carbon::parse($month)->startOfMonth();
        $to    = Carbon::parse($month)->endOfMonth();

        $invoices = Invoice::whereBetween('issue_date', [$from, $to])->get();
        $totalSales = $invoices->sum('grand_total');
        $vatTotal   = $invoices->sum('vat_total');

        return view('admin.reports.monthly', compact('month', 'invoices', 'totalSales', 'vatTotal'));
    }
}