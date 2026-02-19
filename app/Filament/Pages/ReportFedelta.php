<?php


namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;

use App\Models\LoyaltyCard;
use App\Models\LoyaltyTransaction;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportFedelta extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';
    protected static string|UnitEnum|null $navigationGroup = 'Report e Analytics';
    protected static ?string $navigationLabel = 'Report Fedeltà';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Report Fedeltà';
    protected string $view = 'filament.pages.report-fedelta';

    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function getViewData(): array
    {
        $totalCards = LoyaltyCard::where('is_active', true)->count();
        $cardsByTier = LoyaltyCard::where('is_active', true)
            ->select('tier', DB::raw('COUNT(*) as count'))
            ->groupBy('tier')->get();

        $totalPoints = LoyaltyCard::where('is_active', true)->sum('points');
        $totalSpent = LoyaltyCard::where('is_active', true)->sum('total_spent');

        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        $transactionsSummary = LoyaltyTransaction::whereBetween('created_at', [$from, $to])
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(ABS(points)) as total_points'))
            ->groupBy('type')->get();

        $topCustomers = LoyaltyCard::where('is_active', true)
            ->with('user')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return compact('totalCards', 'cardsByTier', 'totalPoints', 'totalSpent', 'transactionsSummary', 'topCustomers');
    }

    public function downloadPdf()
    {
        $data = $this->getViewData();
        $data['dateFrom'] = $this->dateFrom;
        $data['dateTo'] = $this->dateTo;
        $pdf = Pdf::loadView('pdf.report-fedelta', $data);
        return response()->streamDownload(fn () => print($pdf->output()), "report-fedelta-{$this->dateFrom}-{$this->dateTo}.pdf");
    }
}
