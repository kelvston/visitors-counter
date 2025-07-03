@extends('layouts.header')

@section('content')
    <div class="container-fluid px-4">
        <h2 class="mb-4">🛠 System Settings</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <ul class="nav nav-tabs mb-3" id="settingsTabs" role="tablist">
                @php
                    $tabs = ['Shop', 'Receipt', 'Discount', 'Checkout', 'Stock', 'Product', 'Alerts', 'Employee', 'Backup'];
                @endphp
                @foreach($tabs as $i => $tab)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($i==0) active @endif" id="{{ strtolower($tab) }}-tab" data-bs-toggle="tab"
                                data-bs-target="#{{ strtolower($tab) }}" type="button" role="tab">
                            {{ $tab }}
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content border p-3 bg-white shadow-sm rounded" id="settingsTabsContent">
                {{-- SHOP TAB --}}
                <div class="tab-pane fade show active" id="shop" role="tabpanel">
                    <x-setting-input label="Shop Name" name="shop_name" :value="$settings['shop_name']->value ?? ''" />
                    <x-setting-input label="Shop Address" name="shop_address" :value="$settings['shop_address']->value ?? ''" />
                    <x-setting-input label="Phone" name="contact_phone" :value="$settings['contact_phone']->value ?? ''" />
                    <x-setting-input label="Email" name="contact_email" type="email" :value="$settings['contact_email']->value ?? ''" />
                    <x-setting-input label="Currency Symbol" name="currency_symbol" :value="$settings['currency_symbol']->value ?? ''" />
                    <x-setting-select label="Date Format" name="date_format" :value="$settings['date_format']->value ?? ''" :options="['DD/MM/YYYY','MM/DD/YYYY']" />
                    <x-setting-select label="Time Format" name="time_format" :value="$settings['time_format']->value ?? ''" :options="['24-hour','AM/PM']" />
                    <x-setting-input label="Time Zone" name="time_zone" :value="$settings['time_zone']->value ?? ''" />
                    <x-setting-textarea label="Operating Hours" name="operating_hours" :value="$settings['operating_hours']->value ?? ''" />
{{--                    <x-setting-file label="Upload Logo" name="shop_logo" />--}}
                </div>

                {{-- RECEIPT TAB --}}
                <div class="tab-pane fade" id="receipt" role="tabpanel">
                    <x-setting-textarea label="Receipt Footer Text" name="receipt_footer_text" :value="$settings['receipt_footer_text']->value ?? ''" />
                    <x-setting-checkbox label="Print Logo on Receipt" name="print_logo" :checked="$settings['print_logo']->value ?? false" />
                    <x-setting-checkbox label="Include Customer Details" name="include_customer_details" :checked="$settings['include_customer_details']->value ?? false" />
                    <x-setting-input label="Receipt Number Start" type="number" name="receipt_number_sequence" :value="$settings['receipt_number_sequence']->value ?? ''" />
                    <x-setting-checkbox label="Email Receipts" name="email_receipts" :checked="$settings['email_receipts']->value ?? false" />
                    <x-setting-select label="Invoice Template" name="invoice_template" :value="$settings['invoice_template']->value ?? ''" :options="['default','compact','custom1']" />
                </div>

                {{-- DISCOUNT TAB --}}
                <div class="tab-pane fade" id="discount" role="tabpanel">
                    <x-setting-checkbox label="Enable Manual Discounts" name="allow_manual_discounts" :checked="$settings['allow_manual_discounts']->value ?? false" />
                </div>

                {{-- CHECKOUT TAB --}}
                <div class="tab-pane fade" id="checkout" role="tabpanel">
                    <x-setting-select label="After Sale Action" name="checkout_behavior" :value="$settings['checkout_behavior']->value ?? ''" :options="['print_receipt','new_sale','main_menu']" />
                </div>

                {{-- STOCK TAB --}}
                <div class="tab-pane fade" id="stock" role="tabpanel">
                    <x-setting-textarea label="Stock Adjustment Reasons (comma separated)" name="stock_adjustment_reasons" :value="$settings['stock_adjustment_reasons']->value ?? ''" />
                    <x-setting-checkbox label="Allow Manual Stock Adjustments" name="allow_stock_adjustments" :checked="$settings['allow_stock_adjustments']->value ?? false" />
                    <x-setting-checkbox label="Audit Stock Changes" name="audit_stock_changes" :checked="$settings['audit_stock_changes']->value ?? false" />
                </div>

                {{-- PRODUCT TAB --}}
                <div class="tab-pane fade" id="product" role="tabpanel">
                    <x-setting-select label="Default Unit of Measure" name="default_unit" :value="$settings['default_unit']->value ?? ''" :options="['pcs','kg','liter']" />
                </div>

                {{-- ALERTS TAB --}}
                <div class="tab-pane fade" id="alerts" role="tabpanel">
                    <x-setting-input label="Low Stock Threshold" type="number" name="default_low_stock_threshold" :value="$settings['default_low_stock_threshold']->value ?? ''" />
                </div>

                {{-- EMPLOYEE TAB --}}
                <div class="tab-pane fade" id="employee" role="tabpanel">
                    <x-setting-checkbox label="Enable Role Assignment on Employee Creation" name="role_assignment_enabled" :checked="$settings['role_assignment_enabled']->value ?? false" />
                </div>

                {{-- BACKUP TAB --}}
                <div class="tab-pane fade" id="backup" role="tabpanel">
                    <x-setting-checkbox label="Enable Daily Backup" name="enable_daily_backup" :checked="$settings['enable_daily_backup']->value ?? false" />
                    <x-setting-input label="Backup Time (e.g., 23:00)" type="time" name="backup_time" :value="$settings['backup_time']->value ?? ''" />
                    <x-setting-input label="Retention Days" type="number" name="backup_retention_days" :value="$settings['backup_retention_days']->value ?? ''" />
                    <x-setting-select label="Backup Destination" name="backup_destination" :value="$settings['backup_destination']->value ?? ''" :options="['local','s3','gdrive']" />
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection
