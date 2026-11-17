<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\DocumentTypeEnum;
use App\Models\Lib\DocumentCategory;
use Illuminate\Support\Facades\DB;

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
    public function handle(): int
    {
        $this->info('Starting document categories synchronization...');

        $forceActivate = (bool) $this->option('force-activate');
        $created = 0;
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach (DocumentTypeEnum::cases() as $type) {
                $existing = DocumentCategory::where('id', $type->value)->first();

                if (!$existing) {
                    DocumentCategory::create([
                        'id' => $type->value,
                        'code' => 'DCT-' . $type->value,
                        'parent_id' => $type->parentId(),
                        'name' => $type->label(),
                        'category' => 'TYPE',
                        'base_route' => $type->baseRoute(),
                        'route' => $type->baseRoute() . '.create',
                        'is_active' => true,
                    ]);
                    $created++;
                    $this->line(" <fg=green>[CREATED]</> {$type->value} - {$type->label()}");
                } else {
                    $updateData = [
                        'name' => $type->label(),
                        'base_route' => $type->baseRoute(),
                        'route' => $type->baseRoute() . '.create',
                    ];

                    if ($forceActivate) {
                        $updateData['is_active'] = true;
                    }

                    $existing->update($updateData);
                    $updated++;
                    $this->line(" <fg=cyan>[UPDATED]</> {$type->value} - {$type->label()}");
                }
            }

            DB::commit();

            DocumentCategory::invalidateCache();

            $this->newLine();
            $this->info("Synchronization complete! Created: {$created}, Updated: {$updated}. Cache refreshed.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Failed to sync document categories: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
