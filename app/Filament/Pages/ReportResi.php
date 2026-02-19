<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\Order;
use App\Models\WarehouseMovement;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportResi extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static string|UnitEnum|null $navigationGroup = 'Report e Analytics';
    protected static ?string $navigationLabel = 'Report Resi';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Report Resi';
    protected string $view = 'filament.pages.report-resi';

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

        $totalReturns = Order::whereBetween('return_requested_at', [$from, $to])
            ->where('return_status', '!=', 'none')->count();

        $returnsByStatus = Order::whereBetween('return_requested_at', [$from, $to])
            ->where('return_status', '!=', 'none')
            ->select('return_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('return_status')->get();

        $totalReturnValue = Order::whereBetween('return_requested_at', [$from, $to])
            ->where('return_status', '!=', 'none')->sum('total');

        $totalOrdersInPeriod = Order::whereBetween('created_at', [$from, $to])->where('status', '!=', 'cancelled')->count();
        $returnRate = $totalOrdersInPeriod > 0 ? round(($totalReturns / $totalOrdersInPeriod) * 100, 2) : 0;

        $recentReturns = Order::whereBetween('return_requested_at', [$from, $to])
            ->where('return_status', '!=', 'none')
            ->with('user', 'items.product')
            ->latest('return_requested_at')
            ->limit(20)
            ->get();

        $warehouseReturns = WarehouseMovement::whereIn('reason', ['reso_cliente', 'reso_fornitore'])
            ->whereBetween('created_at', [$from, $to])
            ->with('product')
            ->select('reason', DB::raw('COUNT(*) as count'), DB::raw('SUM(ABS(quantity)) as total_qty'))
            ->groupBy('reason')->get();

        return compact('totalReturns', 'returnsByStatus', 'totalReturnValue', 'returnRate', 'recentReturns', 'warehouseReturns');
    }

    public function downloadPdf()
    {
        $data = $this->getViewData();
        $data['dateFrom'] = $this->dateFrom;
        $data['dateTo'] = $this->dateTo;
        $pdf = Pdf::loadView('pdf.report-resi', $data);
        return response()->streamDownload(fn () => print($pdf->output()), "report-resi-{$this->dateFrom}-{$this->dateTo}.pdf");
    }
}
