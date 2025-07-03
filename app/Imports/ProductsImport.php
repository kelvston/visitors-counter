<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Normalize name
        $name = trim($row['name']);
        $barcode = isset($row['barcode']) ? trim($row['barcode']) : null;
        $unitType = strtolower(trim($row['unit_type']));
        $pricePerUnit = floatval($row['price_per_unit']);
        $pricePerUnit = floatval($row['cost_price']);
        $stock = floatval($row['stock']);

        if (!$name || !$unitType || $pricePerUnit <= 0 || $stock < 0) {
            return null; // skip invalid row
        }

        // Check if product exists (case-insensitive match)
        $product = Product::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if ($product) {
            // Update existing product
            $product->stock += $stock;
            $product->price_per_unit = $pricePerUnit; // Optional: update price
            $product->save();

            return null; // Skip model creation
        }

        // Create new product
        return new Product([
            'name'           => $name,
            'barcode'        => $barcode,
            'unit_type'      => $unitType,
            'price_per_unit' => $pricePerUnit,
            'stock'          => $stock,
        ]);
    }
}
