# Exceptions

## Purpose
Centralized exception handling for the GO-AdminPanel application. This directory manages how errors and exceptions are reported, logged, and rendered to users.

## Files

### `Handler.php`
The application's exception handler that extends Laravel's base exception handler.

**Key Responsibilities:**
- **Exception Reporting** - Controls which exceptions get logged
- **Exception Rendering** - Determines how exceptions are displayed to users
- **Form Validation** - Handles validation exception inputs
- **Custom Error Pages** - Renders custom error responses

**Configured Exceptions:**
- Defines which exceptions should NOT be reported (e.g., authentication, authorization, validation)
- Handles form request validation with input preservation

## How It Works

### Exception Flow
1. Exception occurs in the application
2. Handler determines if it should be reported (logged)
3. Handler renders appropriate response (JSON for API, HTML for web)
4. User receives formatted error message

### Customization Example

```php
// In Handler.php

// Don't report these exceptions
protected $dontReport = [
    AuthenticationException::class,
    ValidationException::class,
];

// Custom rendering
public function render($request, Throwable $exception)
{
    if ($exception instanceof CustomException) {
        return response()->json(['error' => 'Custom message'], 400);
    }

    return parent::render($request, $exception);
}
```

## Common Use Cases

### Adding Custom Exceptions
Create custom exception classes in this directory:
```php
namespace App\Exceptions;

class SubscriptionExpiredException extends \Exception
{
    //
}
```

### Customizing Error Responses
Modify `Handler.php` to control error responses for specific exception types.

## Best Practices
- Keep exception handling logic in `Handler.php`
- Create custom exception classes for business-specific errors
- Use appropriate HTTP status codes
- Provide helpful error messages (without exposing sensitive info)
- Log exceptions that need investigation
- Return JSON for API requests, HTML for web requests
