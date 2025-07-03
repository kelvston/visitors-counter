<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Handle file upload (logo)
        if ($request->hasFile('shop_logo')) {
            $file = $request->file('shop_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/uploads/settings', $filename);
            Setting::set('shop_logo', $filename, 'shop');
        }

        // 2. Handle other fields
        $excluded = ['_token', 'shop_logo'];
        foreach ($request->except($excluded) as $key => $value) {
            $group = $this->detectGroup($key);
            Setting::set($key, is_array($value) ? json_encode($value) : $value, $group);
        }

        // 3. Handle unchecked checkboxes (set to "off" if missing)
        $checkboxes = [
            'role_assignment_enabled', 'allow_stock_adjustments', 'audit_stock_changes',
            'allow_manual_discounts', 'print_logo', 'include_customer_details',
            'email_receipts', 'enable_daily_backup'
        ];
        foreach ($checkboxes as $box) {
            if (!$request->has($box)) {
                Setting::set($box, 'off', $this->detectGroup($box));
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    private function detectGroup($key)
    {
        return match (true) {
            str_starts_with($key, 'shop_') => 'shop',
            str_starts_with($key, 'receipt_') => 'receipt',
            str_starts_with($key, 'invoice_') => 'receipt',
            str_starts_with($key, 'discount') => 'discount',
            str_starts_with($key, 'checkout') => 'checkout',
            str_starts_with($key, 'role_'), str_starts_with($key, 'employee_') => 'employee',
            str_starts_with($key, 'stock_'), str_starts_with($key, 'audit_') => 'stock',
            str_starts_with($key, 'alert_'), str_starts_with($key, 'default_low') => 'alerts',
            str_starts_with($key, 'product_'), str_starts_with($key, 'default_unit') => 'product',
            str_starts_with($key, 'backup_'), str_starts_with($key, 'enable_daily_backup') => 'backup',
            default => null
        };
    }
}

