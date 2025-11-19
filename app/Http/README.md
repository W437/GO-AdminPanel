# Http

## Purpose
Handles all HTTP request processing for the GO-AdminPanel application. This directory contains controllers, middleware, and form request validation classes.

## Structure

### `/Controllers`
The heart of HTTP request handling, organized by user role and functionality.

#### `Controller.php`
Base controller class that all controllers extend.

#### `/Admin` (57 controllers)
Admin panel functionality for platform management:
- **User Management:** AdminController, CustomerController, VendorController, DeliveryManController
- **Orders:** OrderController, RefundController
- **Financial:** TransactionController, DisbursementController, PaymentRequestController
- **Products:** FoodController, CategoryController, CuisineController
- **Reports:** AdminTaxReportController, ReportController
- **Settings:** BusinessSettingsController, MailConfigController
- **Marketing:** CampaignController, BannerController, CouponController
- And many more...

#### `/Vendor` (29 controllers)
Restaurant vendor panel for restaurant owners:
- **Dashboard:** DashboardController - Analytics and overview
- **Orders:** POSController, OrderController - Order management
- **Products:** FoodController - Menu management
- **Reports:** ReportController - Sales analytics
- **Subscriptions:** SubscriptionController - Plan management
- **Settings:** RestaurantController - Restaurant configuration

#### `/Api`
API endpoints for mobile apps and external integrations:
- **V1:** Legacy API version
- **V2:** Current API version with improved structure

#### Root Level Controllers
Shared controllers used across the application:
- `WalletPaymentController.php` - Wallet operations
- `PaymentController.php` - Payment processing
- `VendorController.php` - Public vendor endpoints
- `NewsletterController.php` - Newsletter subscriptions

### `/Middleware` (15 files)
Request filtering and processing before reaching controllers:

#### Authentication
- `Authenticate.php` - User authentication verification
- `AdminMiddleware.php` - Admin role verification
- `VendorMiddleware.php` - Vendor role verification
- `DmTokenIsValid.php` - Delivery man API token validation
- `VendorTokenIsValid.php` - Vendor API token validation

#### Localization
- `Localization.php` - Language detection and setting
- `LocalizationMiddleware.php` - Alternative localization handler

#### Business Logic
- `Subscription.php` - Subscription status verification
- `ModulePermissionMiddleware.php` - Feature/module access control
- `MaintenanceMode.php` - Maintenance mode display

#### Security & Standard
- `VerifyCsrfToken.php` - CSRF protection
- `EncryptCookies.php` - Cookie encryption
- `TrimStrings.php` - Input sanitization

### `/Requests`
Form Request classes for validation and authorization:
- `CashBackAddRequest.php` - Cashback creation validation
- `CashBackUpdateRequest.php` - Cashback update validation
- `AdvertisementStoreRequest.php` - Advertisement validation
- **`/Story`** - Story creation and update validation

## Usage Patterns

### Controller Example
```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Handle request
        $orders = Order::paginate(25);
        return view('admin.orders.index', compact('orders'));
    }
}
```

### Middleware Usage
```php
// In routes/web.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
});

// In Controller
public function __construct()
{
    $this->middleware('subscription');
}
```

### Form Request Validation
```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashBackAddRequest extends FormRequest
{
    public function authorize()
    {
        return true; // or check permissions
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
```

## Request Flow

1. **Route** receives HTTP request
2. **Middleware** filters/processes request
3. **Form Request** validates input (if used)
4. **Controller** handles business logic
5. **Response** returned to client

## Best Practices

### Controllers
- Keep controllers thin - delegate to services
- Use dependency injection
- Return consistent response formats
- Handle exceptions properly
- Use resource controllers for REST

### Middleware
- Keep middleware focused on single responsibility
- Order matters (register in Kernel.php)
- Terminate method for post-response tasks
- Use middleware groups for common stacks

### Form Requests
- Use for complex validation
- Handle authorization in `authorize()` method
- Customize error messages in `messages()` method
- Transform input in `prepareForValidation()` if needed
