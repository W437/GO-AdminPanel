# Exports

## Purpose
Excel and CSV export functionality for the GO-AdminPanel application. This directory contains 57+ export classes that generate downloadable reports using the Maatwebsite/Excel library.

## Overview
Each export class defines how data should be formatted and exported to spreadsheet formats (Excel, CSV). These are used throughout the admin and vendor panels to generate reports.

## Export Categories

### Orders & Transactions
- `OrderExport.php` - Order listing export
- `OrderRefundExport.php` - Refunded orders report
- `OrderReportExport.php` - Detailed order analytics
- `OrderTransactionReportExport.php` - Transaction history

### Financial Reports
- `TransactionReportExport.php` - All transaction records
- `DisbursementExport.php` - Payment disbursements
- `VendorTransactionReportExport.php` - Vendor-specific transactions
- `AdminTaxReportExport.php` - Tax collection reports

### Restaurant Management
- `RestaurantListExport.php` - Restaurant directory
- `RestaurantFoodExport.php` - Restaurant menu items
- `RestaurantOrderlistExport.php` - Restaurant order history
- `RestaurantWalletTransactionExport.php` - Restaurant wallet activity

### Delivery Management
- `DeliveryManListExport.php` - Delivery personnel directory
- `DeliveryManEarningExport.php` - Delivery earnings report
- `DeliveryManReviewExport.php` - Delivery person ratings

### Customer Management
- `CustomerListExport.php` - Customer directory
- `CustomerOrderExport.php` - Customer order history
- `CustomerWalletTransactionExport.php` - Customer wallet transactions

### Products & Categories
- `FoodListExport.php` - Product catalog
- `CategoryExport.php` - Category listing
- `CuisineExport.php` - Cuisine types
- `FoodReviewExport.php` - Product reviews

### Subscriptions
- `SubscriptionPackageExport.php` - Available subscription plans
- `SubscriptionReportExport.php` - Subscription analytics

### Campaigns & Marketing
- `CampaignReportExport.php` - Campaign performance

## Usage Pattern

```php
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

// In a controller
public function export()
{
    return Excel::download(new OrderExport(), 'orders.xlsx');
}
```

## Creating New Exports

```bash
# Generate a new export class
php artisan make:export NewReportExport --model=ModelName
```

## Export Class Structure

```php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExampleExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Return the data to export
        return Model::all();
    }

    public function headings(): array
    {
        // Define column headers
        return ['ID', 'Name', 'Email', 'Created At'];
    }
}
```

## Features
- Automatic column headers
- Data filtering and transformation
- Multiple sheet support
- Custom styling options
- Large dataset handling with chunking
- Multiple format support (XLSX, CSV, PDF)

## Best Practices
- Keep export logic simple and focused
- Use query optimization to avoid memory issues
- Implement chunking for large datasets
- Format dates and numbers consistently
- Include only necessary columns
- Add filters for date ranges and status
