<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\Order;
use App\Models\Invoice;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportGenerale extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string|UnitEnum|null $navigationGroup = 'Report e Analytics';
    protected static ?string $navigationLabel = 'Report Generale';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Report Generale';
    protected string $view = 'filament.pages.report-generale';

    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function getViewData(): array
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        $orders = Order::whereBetween('created_at', [$from, $to])->where('status', '!=', 'cancelled');
        $totalRevenue = (clone $orders)->sum('total');
        $totalOrders = (clone $orders)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalTax = (clone $orders)->sum('tax_amount');
        $totalDiscount = (clone $orders)->sum('discount_amount');

        $ordersByStatus = Order::whereBetween('created_at', [$from, $to])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')->get();

        $ordersByPayment = Order::whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')->get();

        $ordersBySource = Order::whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'cancelled')
            ->select('source', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('source')->get();

        $invoicesSummary = Invoice::whereBetween('issue_date', [$from->toDateString(), $to->toDateString()])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')->get();

        return compact(
            'totalRevenue', 'totalOrders', 'avgOrderValue', 'totalTax', 'totalDiscount',
            'ordersByStatus', 'ordersByPayment', 'ordersBySource', 'invoicesSummary'
        );
    }

    public function downloadPdf()
    {
        $data = $this->getViewData();
        $data['dateFrom'] = $this->dateFrom;
        $data['dateTo'] = $this->dateTo;
        $pdf = Pdf::loadView('pdf.report-generale', $data);
        return response()->streamDownload(fn () => print($pdf->output()), "report-generale-{$this->dateFrom}-{$this->dateTo}.pdf");
    }
}
