# Rules

## Purpose
Custom validation rules for the GO-AdminPanel application. These classes extend Laravel's validation system with application-specific validation logic.

## Files

### `WordValidation.php`
Custom validation rule for word-based validation.

**Purpose:** Validates input based on word patterns, word count, or word-specific requirements.

**Implementation:** Extends Laravel's `ValidationRule` interface.

## How Custom Rules Work

### Rule Structure
```php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class WordValidation implements Rule
{
    public function passes($attribute, $value)
    {
        // Validation logic
        // Return true if valid, false if invalid
        return str_word_count($value) >= 3;
    }

    public function message()
    {
        // Error message when validation fails
        return 'The :attribute must contain at least 3 words.';
    }
}
```

## Usage in Controllers

### Direct Usage
```php
use App\Rules\WordValidation;

$request->validate([
    'description' => ['required', new WordValidation()],
]);
```

### With Parameters
```php
class WordValidation implements Rule
{
    protected $minWords;

    public function __construct($minWords = 3)
    {
        $this->minWords = $minWords;
    }

    public function passes($attribute, $value)
    {
        return str_word_count($value) >= $this->minWords;
    }
}

// Usage
$request->validate([
    'description' => ['required', new WordValidation(5)],
]);
```

### In Form Requests
```php
use App\Rules\WordValidation;

public function rules()
{
    return [
        'description' => ['required', 'string', new WordValidation()],
        'title' => ['required', 'string', new WordValidation(2)],
    ];
}
```

## Creating New Rules

```bash
# Generate new validation rule
php artisan make:rule CustomRule
```

This creates a new rule class in `app/Rules/`.

## Common Validation Rule Patterns

### Conditional Validation
```php
public function passes($attribute, $value)
{
    if ($this->someCondition) {
        return $this->validateConditionA($value);
    }

    return $this->validateConditionB($value);
}
```

### Database Validation
```php
use Illuminate\Support\Facades\DB;

public function passes($attribute, $value)
{
    return DB::table('restaurants')
        ->where('slug', $value)
        ->where('status', 'active')
        ->exists();
}
```

### Complex Pattern Matching
```php
public function passes($attribute, $value)
{
    // Validate phone number format
    return preg_match('/^\+?[1-9]\d{1,14}$/', $value);
}
```

### Multiple Field Validation
```php
class ValidRestaurantTime implements Rule
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function passes($attribute, $value)
    {
        $openTime = $this->request->input('open_time');
        $closeTime = $value;

        return strtotime($closeTime) > strtotime($openTime);
    }

    public function message()
    {
        return 'Close time must be after open time.';
    }
}
```

## Invokable Rules (Laravel 8+)

Simpler syntax using `__invoke()`:
```php
namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class Uppercase implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        if (strtoupper($value) !== $value) {
            $fail('The :attribute must be uppercase.');
        }
    }
}
```

## Rule Examples

### Unique Ignoring Soft Deletes
```php
class UniqueNotDeleted implements Rule
{
    protected $table;
    protected $column;
    protected $ignoreId;

    public function __construct($table, $column, $ignoreId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value)
    {
        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->whereNull('deleted_at');

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return 'The :attribute already exists.';
    }
}
```

### Alpha Numeric with Specific Characters
```php
class AlphaNumDash implements Rule
{
    public function passes($attribute, $value)
    {
        // Allow letters, numbers, dashes, and underscores only
        return preg_match('/^[a-zA-Z0-9_-]+$/', $value);
    }

    public function message()
    {
        return 'The :attribute may only contain letters, numbers, dashes and underscores.';
    }
}
```

### File Size with Type Check
```php
class ImageMaxSize implements Rule
{
    protected $maxSizeMB;

    public function __construct($maxSizeMB = 5)
    {
        $this->maxSizeMB = $maxSizeMB;
    }

    public function passes($attribute, $value)
    {
        if (!$value instanceof \Illuminate\Http\UploadedFile) {
            return false;
        }

        // Check if image
        if (!in_array($value->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
            return false;
        }

        // Check size (in KB)
        return $value->getSize() <= ($this->maxSizeMB * 1024);
    }

    public function message()
    {
        return "The :attribute must be an image (jpg, png) and less than {$this->maxSizeMB}MB.";
    }
}
```

## Best Practices
- Keep rules focused on single validation concern
- Use descriptive class names (e.g., `ValidPhoneNumber`, `UniqueEmail`)
- Return clear, actionable error messages
- Use dependency injection for database/service access
- Make rules reusable across the application
- Consider performance for database queries
- Test rules thoroughly with edge cases
- Document complex validation logic
- Use Laravel's built-in rules when possible
- Combine with other validation rules

## Testing Custom Rules

```php
use Tests\TestCase;
use App\Rules\WordValidation;

class WordValidationTest extends TestCase
{
    public function test_validates_minimum_words()
    {
        $rule = new WordValidation(3);

        $this->assertTrue($rule->passes('description', 'This has three words'));
        $this->assertFalse($rule->passes('description', 'Two words'));
    }

    public function test_returns_correct_error_message()
    {
        $rule = new WordValidation();

        $this->assertIsString($rule->message());
    }
}
```

## When to Use Custom Rules vs. Closures

### Use Custom Rule Classes When:
- Logic is reused across multiple places
- Validation is complex
- You need testability
- You want clear organization

### Use Closure Validation When:
- One-off validation
- Simple logic
- Rapid prototyping

```php
// Closure example
$request->validate([
    'name' => [
        'required',
        function ($attribute, $value, $fail) {
            if (strtoupper($value) !== $value) {
                $fail('The '.$attribute.' must be uppercase.');
            }
        },
    ],
]);
```
