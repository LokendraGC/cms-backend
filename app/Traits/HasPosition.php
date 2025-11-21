<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasPosition
{
    public static function reorderItems(array $itemIds)
    {
        try {
            DB::transaction(function () use ($itemIds) {
                foreach ($itemIds as $position => $itemId) {
                    static::where('id', $itemId)->update([
                        'position' => $position + 1,
                        'updated_at' => now()
                    ]);
                }
            });
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Failed to reorder items: ' . $e->getMessage());
        }
    }
}
