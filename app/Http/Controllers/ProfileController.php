<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function todayDistribution(Request $request)
    {
        // Get today's sales
        $today = Carbon::today();
        $salesData = DB::table('sale_items') // Changed from Sale::whereDate to DB::table('sale_items')
        ->whereDate('sale_items.created_at', $today) // Specify table for created_at
        ->join('products', 'sale_items.product_id', '=', 'products.id') // Changed 'sales.product_id'
        ->selectRaw('products.name as label, SUM(sale_items.quantity) as total_quantity') // Changed 'sales.quantity'
        ->groupBy('products.name')
            ->get();


        $labels = [];
        $data = [];

        foreach ($salesData as $sale) {
            $labels[] = $sale->label;
            $data[] = (float) $sale->total_quantity; // Cast to float for Chart.js
        }

        // If no sales today, provide some default or empty data
        if (empty($labels)) {
            $labels = ['No Sales Today'];
            $data = [1]; // A small value to show a slice, or [0] to show nothing
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    // You also need a method for `sales.decrement` if you plan to use that button
    public function decrement(Request $request)
    {
        $type = $request->input('type'); // 'items', 'litre', or 'kg'

        // Implement your decrement logic here. This is a placeholder.
        // Example: Find the latest sale of a certain type and decrease its quantity.
        // This logic will depend heavily on how your sales are structured and what decrementing 'items', 'litre', 'kg' means.

        // Example (VERY basic and might need adjustment based on your DB schema):
        try {
            // This is a simplified example. You'll need to define what decrementing means.
            // For instance, if 'items', 'litre', 'kg' refer to different product types or different ways of logging sales.
            // Let's assume for a moment that 'type' refers to the unit_type of a product and you want to decrement the last sale of that unit_type.

            $lastSale = Sale::whereHas('product', function($query) use ($type) {
                // Assuming 'unit_type' is a column in your 'products' table
                // You might need to map 'items', 'litre', 'kg' to actual unit types.
                $query->where('unit_type', $type);
            })
                ->latest() // Get the latest sale
                ->first();

            if ($lastSale && $lastSale->quantity > 0) {
                // Decrement by 1, or by a specific unit relevant to the type
                $lastSale->quantity -= 1; // Example: Decrement by 1 unit
                if ($lastSale->quantity < 0) $lastSale->quantity = 0; // Ensure quantity doesn't go negative
                $lastSale->save();

                return response()->json(['success' => true, 'message' => 'Sale decremented successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'No relevant sales to decrement or quantity already zero.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error decrementing sale: ' . $e->getMessage()]);
        }
    }

}
