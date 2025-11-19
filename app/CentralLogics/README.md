# CentralLogics

## Purpose
Core business logic layer that serves as the service layer for the GO-AdminPanel application. This directory contains reusable business logic that can be called from controllers, commands, or other parts of the application.

## Structure

### Root Level Classes
Business logic classes for main entities:
- `RestaurantLogic.php` - Restaurant management operations
- `ProductLogic.php` - Product/Food item operations
- `OrderLogic.php` - Order processing logic
- `FileManagerLogic.php` - File management operations
- `CustomerLogic.php` - Customer-related operations
- `CouponLogic.php` - Coupon/Discount logic
- `CategoryLogic.php` - Category management
- `CampaignLogic.php` - Campaign operations
- `BannerLogic.php` - Banner management
- `SMSModule.php` - SMS gateway integration
- `Helpers.php` - Extended helper methods using PaymentGatewayTrait

### Subdirectories (Specialized Services)

#### `/Config`
Business settings management and configuration caching
- `ConfigService.php` - Handles business settings retrieval and caching

#### `/Pricing`
Price calculation logic
- `PricingService.php` - Handles dynamic pricing, discounts, and calculations

#### `/Formatting`
Data transformation and formatting
- `DataFormatter.php` - Transforms data between different formats

#### `/Notifications`
Push notifications and notification management
- Push notification services
- Notification configuration
- Utility services for notification delivery

#### `/Orders`
Order-related notifications and processing
- `OrderNotificationService.php` - Handles all order notification logic

#### `/Access`
Permission and access control
- `AccessService.php` - Manages role-based access control

#### `/Localization`
Language and translation handling
- `TranslationService.php` - Manages multi-language support

#### `/Finance`
Financial operations and calculations
- `FinanceService.php` - Handles financial calculations, commissions, earnings

#### `/Info`
General information retrieval
- `InfoService.php` - Provides system and business information

#### `/Logistics`
Delivery and logistics operations
- `LogisticsService.php` - Manages delivery operations and assignments

#### `/Inventory`
Stock and inventory management
- `InventoryService.php` - Handles product stock levels

#### `/Media`
Image and media handling
- `MediaService.php` - Manages media uploads, processing, and storage

#### `/Payments`
Payment-related utilities
- `PaymentUtilityService.php` - Common payment processing utilities

#### `/Presentation`
UI and presentation logic
- `PresentationService.php` - Handles data preparation for views

#### `/Subscription`
Subscription plan management
- `SubscriptionService.php` - Manages vendor subscription plans and billing

## Usage Pattern

```php
use App\CentralLogics\OrderLogic;

// In a controller
$order = OrderLogic::create_order($request);
```

## Design Philosophy
CentralLogics follows the **Service Layer Pattern**, separating business logic from controllers to:
- Promote code reusability
- Make business logic testable
- Keep controllers thin and focused on HTTP concerns
- Centralize business rules in one location

## Note
This represents the older architecture. Newer features may use the `/app/Services` directory for a more domain-driven approach.
