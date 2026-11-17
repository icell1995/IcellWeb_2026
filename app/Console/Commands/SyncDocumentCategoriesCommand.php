<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Doc\DocumentFeatureFlagService;

class SyncDocumentCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:sync {--force-activate : Reset all document statuses to active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync document types from DocumentTypeEnum to lib.document_categories table without overriding existing is_active status';

    /**
     * Execute the console command.
     */
    public function handle(DocumentFeatureFlagService $service): int
    {
        $this->info('Starting document categories synchronization...');

        $forceActivate = (bool) $this->option('force-activate');

        try {
            $stats = $service->syncFromEnum($forceActivate);

            $this->newLine();
            $this->info("Synchronization complete! Created: {$stats['created']}, Updated: {$stats['updated']}. Cache refreshed.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to sync document categories: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
