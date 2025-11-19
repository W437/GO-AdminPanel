# Mail

## Purpose
Mailable classes for sending emails throughout the GO-AdminPanel application. Contains 40+ email templates using Laravel's Mailable system.

## Overview
Each Mailable class represents a specific type of email that can be sent. They define the email content, subject, recipients, and attachments.

## Email Categories

### Account Management
- `UserStatus.php` - User account status changes
- `AdminPasswordResetMail.php` - Admin password reset emails
- `UserPasswordResetMail.php` - User password reset emails
- `EmailVerification.php` - Email address verification
- `LoginVerification.php` - Two-factor authentication codes
- `PasswordResetRequestMail.php` - Password reset requests

### Registration & Onboarding
- `CustomerRegistration.php` - Welcome email for new customers
- `VendorSelfRegistration.php` - Vendor signup confirmation
- `DmSelfRegistration.php` - Delivery person signup confirmation
- `RestaurantRegistration.php` - Restaurant approval/registration

### Orders
- `OrderPlaced.php` - Order confirmation to customer
- `PlaceOrder.php` - Order notification to vendor
- `OrderVerificationMail.php` - Order verification code
- `RefundedOrderMail.php` - Refund processed notification

### Subscriptions
- `SubscriptionSuccessful.php` - Subscription activation
- `SubscriptionRenewOrShift.php` - Renewal or plan change
- `SubscriptionPlanUpdate.php` - Plan modification
- `SubscriptionDeadLineWarning.php` - Expiration warnings
- `SubscriptionCancel.php` - Cancellation confirmation

### Payments & Withdrawals
- `WithdrawRequestMail.php` - Withdrawal request notification
- `OfflinePaymentMail.php` - Offline payment confirmation
- `AddFundToWallet.php` - Wallet top-up notification

### Notifications & Alerts
- `DmSuspendMail.php` - Delivery person suspension notice
- `CampaignRequestMail.php` - Campaign request status
- `VendorCampaignRequestMail.php` - Vendor campaign updates
- `AdvertisementStatusMail.php` - Ad approval/rejection

### Cash Collection
- `CollectCashMail.php` - Cash collection reminders

### Testing & Utility
- `TestEmailSender.php` - Email configuration testing
- `ContactMail.php` - Contact form submissions

## Usage Pattern

### Sending Email
```php
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;

// Send immediately
Mail::to($customer->email)->send(new OrderPlaced($order));

// Queue email for background sending
Mail::to($customer->email)->queue(new OrderPlaced($order));

// Send to multiple recipients
Mail::to($customer->email)
    ->cc($admin->email)
    ->bcc($supervisor->email)
    ->send(new OrderPlaced($order));
```

### Mailable Structure
```php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Order Confirmation')
                    ->view('emails.order-placed')
                    ->with(['order' => $this->order]);
    }
}
```

### Email Template (Blade)
```blade
{{-- resources/views/emails/order-placed.blade.php --}}
<h1>Order Confirmation</h1>
<p>Thank you for your order #{{ $order->id }}</p>
<p>Total: ${{ $order->total }}</p>
```

## Configuration

Email settings are managed through:
- `MailConfig` model - Database-driven email configuration
- `config/mail.php` - Default email settings
- `BusinessSetting` - SMTP credentials and settings

## Creating New Mailables

```bash
# Generate new mailable
php artisan make:mail NewEmailName
```

## Features

### Attachments
```php
public function build()
{
    return $this->view('emails.invoice')
                ->attach('/path/to/file.pdf')
                ->attachData($pdfData, 'invoice.pdf');
}
```

### CC and BCC
```php
public function build()
{
    return $this->view('emails.notification')
                ->cc('manager@example.com')
                ->bcc('admin@example.com');
}
```

### Reply-To
```php
public function build()
{
    return $this->view('emails.contact')
                ->replyTo($this->contactEmail);
}
```

### Custom Headers
```php
public function build()
{
    return $this->view('emails.message')
                ->withSwiftMessage(function ($message) {
                    $message->getHeaders()->addTextHeader(
                        'X-Custom-Header', 'Value'
                    );
                });
}
```

## Best Practices
- Use queues for non-critical emails to improve performance
- Keep email templates responsive (mobile-friendly)
- Provide plain text alternative for HTML emails
- Include unsubscribe links for marketing emails
- Test emails with various email clients
- Use meaningful subject lines
- Personalize content with user data
- Handle email failures gracefully
- Monitor email delivery rates
- Respect email rate limits

## Testing

```bash
# Test email configuration
php artisan tinker
Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

## Email Queue Processing

```bash
# Process email queue
php artisan queue:work --queue=emails

# Monitor failed emails
php artisan queue:failed
```
