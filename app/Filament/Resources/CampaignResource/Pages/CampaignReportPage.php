<?php
namespace App\Filament\Resources\CampaignResource\Pages;

use App\Models\Campaign;
use App\Models\Gift;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class CampaignReportPage extends Page
{
    protected static ?string $title = 'Campaign Report';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.campaign-report';

    public function mount()
    {
        // You can preload any necessary data here
        $this->totalGifts = Gift::sum('amount');
        $this->totalCampaigns = Campaign::count();
        $this->campaignStats = Campaign::withCount('gifts')->get(); // This will show how many gifts each campaign has
    }

    public function render(): \Illuminate\View\View
    {
        return view('filament.pages.campaign-report', [
            'totalGifts' => $this->totalGifts,
            'totalCampaigns' => $this->totalCampaigns,
            'campaignStats' => $this->campaignStats,
        ]);
    }
}