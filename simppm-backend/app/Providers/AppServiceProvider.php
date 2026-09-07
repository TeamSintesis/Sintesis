<?php

namespace App\Providers;

use App\Models\Monev;
use App\Models\PencairanDana;
use App\Models\Proposal;
use App\Models\Spj;
use App\Observers\MonevObserver;
use App\Observers\PencairanDanaObserver;
use App\Observers\ProposalObserver;
use App\Observers\SpjObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Mendaftarkan Observer yang menegakkan aturan bisnis inti SIMPPM
     * pada event 'saving' model terkait -- lihat app/Observers/*.php dan
     * app/Services/ValidasiAturanBisnisService.php untuk detail aturan.
     */
    public function boot(): void
    {
        Proposal::observe(ProposalObserver::class);
        Monev::observe(MonevObserver::class);
        Spj::observe(SpjObserver::class);
        PencairanDana::observe(PencairanDanaObserver::class);
    }
}
