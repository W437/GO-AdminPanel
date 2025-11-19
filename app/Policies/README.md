# Policies

## Purpose
Laravel authorization policies for fine-grained access control. Policies organize authorization logic around models and actions.

## Files

### `StoryPolicy.php`
Authorization policy for the `Story` model.

**Methods:**
- `manage()` - Determines if a user can manage (view, edit, delete) a specific story

**Logic:**
- Checks if the authenticated user is a Vendor or VendorEmployee
- Validates that the story belongs to the user's restaurant
- Ensures proper ownership and role-based access

## How Policies Work

### Authorization Flow
1. Controller or code calls authorization check
2. Laravel resolves the appropriate policy
3. Policy method evaluates permission
4. Returns boolean (true = authorized, false = denied)

### Policy Structure
```php
namespace App\Policies;

use App\Models\User;
use App\Models\Story;

class StoryPolicy
{
    public function manage(User $user, Story $story)
    {
        // Check if user is vendor/employee
        if (!in_array($user->user_type, ['vendor', 'vendor_employee'])) {
            return false;
        }

        // Check if story belongs to user's restaurant
        return $story->restaurant_id === $user->restaurant_id;
    }
}
```

## Usage in Controllers

### Using Gates
```php
// Check authorization
if ($request->user()->can('manage', $story)) {
    // User is authorized
}

// Authorize or fail (throws exception)
$this->authorize('manage', $story);
```

### Middleware
```php
// In routes
Route::delete('/stories/{story}', [StoryController::class, 'destroy'])
    ->middleware('can:manage,story');
```

### In Blade Templates
```blade
@can('manage', $story)
    <button>Edit Story</button>
@endcan

@cannot('manage', $story)
    <p>You don't have permission to edit this story.</p>
@endcannot
```

## Policy Registration

Policies are auto-discovered by Laravel if they follow naming conventions:
- Model: `App\Models\Story`
- Policy: `App\Policies\StoryPolicy`

Or manually register in `AuthServiceProvider`:
```php
protected $policies = [
    Story::class => StoryPolicy::class,
];
```

## Common Policy Methods

### Resource Actions
```php
public function view(User $user, Story $story)
{
    return $story->is_public || $user->id === $story->user_id;
}

public function create(User $user)
{
    return $user->hasActiveSubscription();
}

public function update(User $user, Story $story)
{
    return $user->id === $story->user_id;
}

public function delete(User $user, Story $story)
{
    return $user->id === $story->user_id || $user->isAdmin();
}

public function restore(User $user, Story $story)
{
    return $user->isAdmin();
}

public function forceDelete(User $user, Story $story)
{
    return $user->isAdmin();
}
```

### Custom Actions
```php
public function publish(User $user, Story $story)
{
    return $user->id === $story->user_id && $story->status === 'draft';
}

public function share(User $user, Story $story)
{
    return $story->is_shareable;
}
```

## Before Method (Admin Override)

Grant access before checking individual methods:
```php
public function before(User $user, $ability)
{
    // Admins can do everything
    if ($user->isAdmin()) {
        return true;
    }

    // Continue to specific method check
    return null;
}
```

## Creating New Policies

```bash
# Generate policy
php artisan make:policy OrderPolicy --model=Order

# Generate policy with resource methods
php artisan make:policy OrderPolicy --model=Order --resource
```

## Guest Users

Allow guest user checks:
```php
public function view(?User $user, Story $story)
{
    // Nullable User type allows guest checks
    if ($story->is_public) {
        return true;
    }

    return $user && $user->id === $story->user_id;
}
```

## Policy Responses

### Simple Boolean
```php
public function update(User $user, Story $story)
{
    return $user->id === $story->user_id;
}
```

### With Custom Message
```php
use Illuminate\Auth\Access\Response;

public function update(User $user, Story $story)
{
    return $user->id === $story->user_id
        ? Response::allow()
        : Response::deny('You do not own this story.');
}
```

### With HTTP Status
```php
return $user->hasActiveSubscription()
    ? Response::allow()
    : Response::deny('Subscription required.', 403);
```

## Best Practices
- Keep policies focused on single model
- Use descriptive method names
- Return simple booleans for most cases
- Use custom messages for complex denials
- Implement `before()` for admin overrides
- Test policies thoroughly
- Consider team/organization ownership
- Document complex authorization logic
- Use policies instead of inline checks
- Combine with middleware for route protection

## Multi-Tenancy Considerations

The `StoryPolicy` demonstrates multi-tenant authorization:
```php
public function manage(User $user, Story $story)
{
    // Ensures users only access their restaurant's stories
    return $story->restaurant_id === $user->restaurant_id;
}
```

This pattern prevents data leakage between tenants (restaurants).
