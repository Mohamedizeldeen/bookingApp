<?php

namespace App\Console\Commands;

use App\Services\AnalyticsService;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:generate {--date= : Specific date to generate analytics for (Y-m-d format)} {--company= : Specific company ID to generate analytics for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate analytics data for all companies or a specific company';

    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        parent::__construct();
        $this->analyticsService = $analyticsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();
        $companyId = $this->option('company');

        $this->info("Generating analytics for date: {$date->format('Y-m-d')}");

        if ($companyId) {
            // Generate analytics for specific company
            $company = Company::find($companyId);
            if (!$company) {
                $this->error("Company with ID {$companyId} not found.");
                return 1;
            }

            $this->info("Generating analytics for company: {$company->name}");
            $this->analyticsService->generateCompanyAnalytics($company, $date);
            $this->info("Analytics generated successfully for {$company->name}");
        } else {
            // Generate analytics for all companies
            $companies = Company::all();
            $this->info("Generating analytics for {$companies->count()} companies...");

            $progressBar = $this->output->createProgressBar($companies->count());
            $progressBar->start();

            foreach ($companies as $company) {
                try {
                    $this->analyticsService->generateCompanyAnalytics($company, $date);
                    $progressBar->advance();
                } catch (\Exception $e) {
                    $this->error("\nFailed to generate analytics for {$company->name}: " . $e->getMessage());
                }
            }

            $progressBar->finish();
            $this->info("\nAnalytics generation completed!");
        }

        return 0;
    }
}
