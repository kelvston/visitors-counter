<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockAdjustmentTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::select('id', 'name', 'stock', 'price_per_unit')->get();
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->stock,
            '', // Quantity (to be filled)
            '', // Reason (optional)
            '', // New Price (optional)
            '', // Action (auto determined during import)
        ];
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Current Stock',
            'Quantity',
            'Reason',
            'New Price (Optional)',
            'Action (Auto - Do not fill)',
        ];
    }
}
