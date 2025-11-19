# Traits

## Purpose
Reusable code mixins that can be included in multiple classes. Traits provide a mechanism for code reuse in PHP's single inheritance model.

## Files

### `Payment.php`
Payment link generation and gateway routing.

**Responsibilities:**
- Generates payment URLs for different gateways
- Routes payment requests to appropriate processors
- Handles payment gateway selection logic

**Supported Gateways:**
- SSL Commerz
- Stripe
- PayPal
- Razorpay
- Senang Pay
- Paytabs
- Paystack
- Flutterwave
- Paymob
- Mercadopago

### `PaymentGatewayTrait.php`
Common payment gateway utilities and helper methods.

**Responsibilities:**
- Payment configuration retrieval
- Gateway status checking
- Payment formatting utilities
- Common payment validations

### `PlaceNewOrder.php`
Order creation and placement logic.

**Responsibilities:**
- Validates order data
- Creates order records
- Processes order items
- Handles order calculations (tax, delivery fee, discounts)
- Manages order status initialization

### `Processor.php`
Data processing utilities.

**Responsibilities:**
- Data transformation and formatting
- Input sanitization
- Response formatting
- Common data manipulation operations

### `SmsGateway.php`
SMS sending functionality across multiple providers.

**Responsibilities:**
- SMS provider selection
- Message formatting
- API integration for SMS gateways
- Error handling for SMS failures

### `NotificationDataSetUpTrait.php`
Notification data preparation and formatting.

**Responsibilities:**
- Prepares notification payload
- Formats notification data for different channels
- Handles notification templating

### `HasUuid.php`
UUID generation for models.

**Responsibilities:**
- Automatically generates UUIDs for model primary keys
- Provides UUID boot method

### `AddonHelper.php`
Addon/plugin system utilities.

**Responsibilities:**
- Addon detection and loading
- Addon configuration management
- Addon route registration

### `ReportFilter.php`
Common report filtering utilities.

**Responsibilities:**
- Date range filtering (today, week, month, year)
- Status filtering
- Restaurant/Zone filtering
- Common query builders for reports

## Using Traits

### Basic Usage
```php
namespace App\Http\Controllers;

use App\Traits\Payment;

class CheckoutController extends Controller
{
    use Payment;

    public function processPayment($order)
    {
        // Use trait method
        $paymentUrl = $this->generatePaymentUrl($order, 'stripe');

        return redirect($paymentUrl);
    }
}
```

### Multiple Traits
```php
use App\Traits\Payment;
use App\Traits\SmsGateway;
use App\Traits\NotificationDataSetUpTrait;

class OrderController extends Controller
{
    use Payment, SmsGateway, NotificationDataSetUpTrait;

    public function placeOrder($request)
    {
        // Use methods from all traits
        $paymentUrl = $this->generatePaymentUrl($order, 'stripe');
        $this->sendSms($customer->phone, 'Order placed');
        $notification = $this->prepareNotificationData($order);
    }
}
```

### In Models
```php
namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuid;

    // UUID automatically generated on creation
}
```

## Trait Examples

### Payment Trait
```php
namespace App\Traits;

trait Payment
{
    public function generatePaymentUrl($order, $gateway)
    {
        switch ($gateway) {
            case 'stripe':
                return $this->stripePaymentUrl($order);
            case 'paypal':
                return $this->paypalPaymentUrl($order);
            default:
                throw new \Exception('Unsupported gateway');
        }
    }

    protected function stripePaymentUrl($order)
    {
        // Stripe payment logic
        return "https://stripe.com/pay?amount={$order->total}";
    }

    protected function paypalPaymentUrl($order)
    {
        // PayPal payment logic
        return "https://paypal.com/pay?amount={$order->total}";
    }
}
```

### SMS Gateway Trait
```php
namespace App\Traits;

trait SmsGateway
{
    public function sendSms($phone, $message)
    {
        $gateway = config('sms.default_gateway');

        return match ($gateway) {
            'twilio' => $this->sendViaTwilio($phone, $message),
            'nexmo' => $this->sendViaNexmo($phone, $message),
            default => throw new \Exception('Invalid SMS gateway'),
        };
    }

    protected function sendViaTwilio($phone, $message)
    {
        // Twilio API call
    }

    protected function sendViaNexmo($phone, $message)
    {
        // Nexmo API call
    }
}
```

### Report Filter Trait
```php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ReportFilter
{
    public function applyDateFilter(Builder $query, $filter)
    {
        return match ($filter) {
            'today' => $query->whereDate('created_at', today()),
            'week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('created_at', now()->month),
            'year' => $query->whereYear('created_at', now()->year),
            default => $query,
        };
    }

    public function applyCustomDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
```

### UUID Trait
```php
namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
```

## Trait Conflict Resolution

When two traits have methods with the same name:

```php
trait Logger
{
    public function log($message)
    {
        echo "Logger: $message";
    }
}

trait Debugger
{
    public function log($message)
    {
        echo "Debugger: $message";
    }
}

class MyClass
{
    use Logger, Debugger {
        Logger::log insteadof Debugger;  // Use Logger's log
        Debugger::log as debugLog;        // Alias Debugger's log
    }
}

$obj = new MyClass();
$obj->log('test');      // Uses Logger::log
$obj->debugLog('test'); // Uses Debugger::log
```

## Accessing Trait Properties

### Using Class Properties
```php
trait Payment
{
    protected $gateway;

    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    public function processPayment()
    {
        // Use $this->gateway
    }
}
```

### Requiring Methods in Traits
```php
trait NotificationDataSetUpTrait
{
    abstract protected function getNotificationChannel();

    public function prepareNotificationData($order)
    {
        $channel = $this->getNotificationChannel();
        // Prepare data based on channel
    }
}

class OrderController
{
    use NotificationDataSetUpTrait;

    protected function getNotificationChannel()
    {
        return 'firebase';
    }
}
```

## Best Practices

### DO's
- ✅ Use traits for cross-cutting concerns
- ✅ Keep traits focused on single responsibility
- ✅ Use descriptive trait names
- ✅ Document trait methods clearly
- ✅ Make trait methods protected when possible
- ✅ Use traits for reusable behavior across unrelated classes

### DON'Ts
- ❌ Don't use traits as a substitute for proper inheritance
- ❌ Don't create god traits with too many methods
- ❌ Don't couple traits too tightly to specific classes
- ❌ Don't store state in traits (use properties carefully)
- ❌ Don't override trait methods without good reason

## Common Use Cases

### 1. Cross-Cutting Concerns
```php
trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::create(['action' => 'created', 'model' => get_class($model)]);
        });
    }
}
```

### 2. API Response Formatting
```php
trait ApiResponder
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], $code);
    }

    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}
```

### 3. File Uploads
```php
trait FileUploadable
{
    public function uploadFile($file, $directory = 'uploads')
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs($directory, $filename, 'public');
    }

    public function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
```

### 4. Query Scopes
```php
trait Searchable
{
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
                     ->orWhere('description', 'LIKE', "%{$term}%");
    }
}
```

## Testing Traits

```php
use Tests\TestCase;

class PaymentTraitTest extends TestCase
{
    use Payment;

    public function test_generates_stripe_payment_url()
    {
        $order = Order::factory()->create(['total' => 100]);

        $url = $this->generatePaymentUrl($order, 'stripe');

        $this->assertStringContainsString('stripe.com', $url);
    }
}
```

## When to Use Traits vs Services

### Use Traits When:
- Code is used across unrelated classes
- Functionality is simple and stateless
- You need to share methods with models
- Implementing interface methods

### Use Services When:
- Complex business logic
- Need dependency injection
- Stateful operations
- Need to mock in tests
