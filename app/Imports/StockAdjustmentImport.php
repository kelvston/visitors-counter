<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class StockAdjustmentImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Basic validation for required fields
            if (empty($row['product_id']) || !is_numeric($row['product_id']) || empty($row['quantity']) || !is_numeric($row['quantity'])) {
                continue;
            }

            $product = Product::find($row['product_id']);

            if (!$product) {
                continue;
            }

            $currentStock = floatval($product->stock); // From DB
            $newPhysicalCount = floatval($row['quantity']); // From Excel

            $action = null;
            $stockChangeAmount = 0;

            // Determine adjustment type and amount
            if ($newPhysicalCount > $currentStock) {
                $action = 'add';
                $stockChangeAmount = $newPhysicalCount - $currentStock;
            } elseif ($newPhysicalCount < $currentStock) {
                $action = 'remove';
                $stockChangeAmount = $currentStock - $newPhysicalCount;
            } else {
                continue; // No adjustment needed
            }

            // ✅ Log before performing update
            Log::info("Processing row: Product ID = {$row['product_id']}, Current Stock (from DB) = {$currentStock}, New Physical Count = {$newPhysicalCount}, Action = {$action}");

            // Update stock
            $product->stock = $newPhysicalCount;

            // Optional price update
            if (!empty($row['new_price']) && is_numeric($row['new_price'])) {
                $product->price_per_unit = floatval($row['new_price']);
            }

            $product->save();

            // Log the adjustment to DB
            StockAdjustment::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'quantity' => $stockChangeAmount,
                'action' => $action,
                'type' => $action, // Ensure consistency
                'reason' => $row['reason'] ?? 'Excel import adjustment',
            ]);
        }
    }
}
