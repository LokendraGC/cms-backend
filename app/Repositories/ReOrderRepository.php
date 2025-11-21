<?php

namespace App\Repositories;

use App\Models\Position;
use Illuminate\Support\Facades\DB;

class ReOrderRepository
{
    public function reorderItems(array $itemIds, string $modelType)
    {
        try {
            DB::transaction(function () use ($itemIds, $modelType) {
                foreach ($itemIds as $position => $itemId) {
                    Position::updateOrCreate(
                        [
                            'positionable_id' => $itemId,
                            'positionable_type' => $modelType
                        ],
                        [
                            'position' => $position + 1,
                            'updated_at' => now()
                        ]
                    );
                }
            });

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to reorder items: ' . $e->getMessage());
        }
    }
}
