<?php

namespace App\Console\Commands;

use App\Services\TraderAssignmentGeneratorService;
use Illuminate\Console\Command;

class GenerateTraderAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-trader-assignments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate trader assignments for all destinations';

    /**
     * Execute the console command.
     */
    public function handle(TraderAssignmentGeneratorService $traderAssignmentGeneratorService): int
    {
        try {
            $traderAssignmentGeneratorService->generateNew();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generating trader assignments: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
