# Models

## Purpose
Eloquent ORM models representing database entities for the GO-AdminPanel application. Contains 120+ model classes that map to database tables and define relationships.

## Overview
Models are the heart of the application's data layer, providing an object-oriented interface to interact with the database.

## Major Model Categories

### Core Users & Roles
- `User.php` - Base user model
- `Admin.php` - Admin panel users
- `Vendor.php` - Restaurant owners/managers
- `VendorEmployee.php` - Restaurant staff
- `DeliveryMan.php` - Delivery personnel
- `Customer.php` - End customers

### Orders & Fulfillment
- `Order.php` - Main order model
- `OrderDetail.php` - Line items in orders
- `OrderTransaction.php` - Order payment transactions
- `OrderPayment.php` - Payment method details
- `OrderDeliveryHistory.php` - Delivery tracking
- `OrderCancelReason.php` - Cancellation reasons

### Restaurants & Products
- `Restaurant.php` - Restaurant/Vendor locations
- `Food.php` - Menu items/products
- `Category.php` - Product categories
- `Cuisine.php` - Cuisine types
- `Addon.php` - Product add-ons
- `Variation.php` - Product variations
- `Attribute.php` - Product attributes
- `Tag.php` - Product tags

### Financial Models
- `WalletPayment.php` - Wallet transactions
- `WalletTransaction.php` - Transaction history
- `Disbursement.php` - Payment disbursements
- `PaymentRequest.php` - Withdrawal requests
- `Refund.php` - Order refunds
- `AdminWallet.php` - Admin wallet
- `RestaurantWallet.php` - Vendor wallet

### Subscriptions
- `Subscription.php` - Active subscriptions
- `SubscriptionPackage.php` - Available plans
- `SubscriptionTransaction.php` - Subscription payments
- `SubscriptionSchedule.php` - Recurring billing schedule
- `SubscriptionBillingAndRefundHistory.php` - Billing history

### Marketing & Promotions
- `Coupon.php` - Discount coupons
- `Campaign.php` - Marketing campaigns
- `Banner.php` - Promotional banners
- `Advertisement.php` - Ad placements
- `CashBack.php` - Cashback offers

### Configuration & Settings
- `BusinessSetting.php` - Application settings
- `DataSetting.php` - Additional configuration
- `Setting.php` - General settings
- `MailConfig.php` - Email configuration
- `EmailTemplate.php` - Email templates

### Location & Logistics
- `Zone.php` - Service zones
- `RestaurantZone.php` - Restaurant zone mapping
- `Vehicle.php` - Delivery vehicles
- `Shift.php` - Delivery shifts
- `TrackDeliveryman.php` - Real-time tracking

### Reviews & Communication
- `Review.php` - Product reviews
- `DMReview.php` - Delivery person reviews
- `Conversation.php` - Chat conversations
- `Message.php` - Chat messages
- `Notification.php` - Push notifications

### Stories & Social
- `Story.php` - Story posts (like Instagram stories)
- `StoryView.php` - Story view tracking

### Analytics
- `VisitorLog.php` - Visitor tracking
- `Log.php` - Application logs
- `TimeLog.php` - Time-based logs
- `ReactService.php` - Reaction tracking

## Model Relationships

Models use Eloquent relationships:
```php
// One-to-Many
public function orders()
{
    return $this->hasMany(Order::class);
}

// Belongs To
public function restaurant()
{
    return $this->belongsTo(Restaurant::class);
}

// Many-to-Many
public function categories()
{
    return $this->belongsToMany(Category::class);
}
```

## Common Model Features

### Scopes
Models may include query scopes:
```php
// Global scope applied to all queries
protected static function booted()
{
    static::addGlobalScope(new RestaurantScope);
}

// Local scope
public function scopeActive($query)
{
    return $query->where('status', 1);
}
```

### Accessors & Mutators
```php
// Accessor
public function getFullNameAttribute()
{
    return $this->first_name . ' ' . $this->last_name;
}

// Mutator
public function setEmailAttribute($value)
{
    $this->attributes['email'] = strtolower($value);
}
```

### Casts
```php
protected $casts = [
    'email_verified_at' => 'datetime',
    'is_active' => 'boolean',
    'meta' => 'array',
];
```

## Best Practices
- Use meaningful model names (singular form)
- Define fillable or guarded properties
- Use relationships instead of manual joins
- Leverage accessors/mutators for data transformation
- Add scopes for common queries
- Use model events (created, updated, deleted)
- Keep business logic in services, not models
- Use eager loading to avoid N+1 queries
