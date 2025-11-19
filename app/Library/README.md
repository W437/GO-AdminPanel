# Library

## Purpose
Third-party integrations and custom library classes for the GO-AdminPanel application. This directory houses specialized integration code and payment processing libraries.

## Structure

### Payment Processing
Core payment library classes:
- `Payment.php` - Main payment processing library
- `Payer.php` - Payer information and details class
- `Receiver.php` - Payment receiver/merchant class
- `Responses.php` - Payment response handling
- `Constant.php` / `Constants.php` - Library constants and configuration

### SSL Commerz Integration (`/SslCommerz`)
Bangladesh's leading payment gateway integration:

- `AbstractSslCommerz.php` - Base SSL Commerz class with core functionality
- `SslCommerzInterface.php` - Contract interface for SSL Commerz implementations
- `SslCommerzNotification.php` - Handles payment notifications and callbacks

## Usage Pattern

### Payment Processing
```php
use App\Library\Payment;
use App\Library\Payer;
use App\Library\Receiver;

// Create payer
$payer = new Payer();
$payer->setName('Customer Name');
$payer->setEmail('customer@example.com');

// Create receiver
$receiver = new Receiver();
$receiver->setName('Merchant Name');

// Process payment
$payment = new Payment();
$payment->setPayer($payer);
$payment->setReceiver($receiver);
$payment->setAmount(1000);
```

### SSL Commerz
```php
use App\Library\SslCommerz\SslCommerzNotification;

// Handle payment callback
$notification = new SslCommerzNotification();
$result = $notification->orderValidate($request);

if ($result) {
    // Payment successful
} else {
    // Payment failed
}
```

## When to Use This Directory

Add classes here when:
- Integrating third-party payment gateways
- Creating wrappers for external APIs
- Building reusable library code
- Implementing custom protocols or standards

## Integration Examples

### Adding New Payment Gateway
1. Create gateway directory (e.g., `/Stripe`)
2. Implement interface classes
3. Add notification handlers
4. Update payment routing in traits

## Best Practices
- Follow interface-based design
- Implement proper error handling
- Use dependency injection
- Keep gateway logic isolated
- Document API requirements
- Handle webhooks/callbacks securely
- Version control API changes
- Test with sandbox environments

## SSL Commerz Specific Notes
SSL Commerz is widely used in Bangladesh and supports:
- Credit/Debit cards
- Mobile banking (bKash, Rocket, Nagad)
- Internet banking
- Wallets

The integration handles:
- Transaction initialization
- Payment verification
- Refund processing
- Transaction validation
- IPN (Instant Payment Notification)
