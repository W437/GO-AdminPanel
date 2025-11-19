# Services

## Purpose
Domain-specific service classes representing the newer, cleaner architecture for the GO-AdminPanel application. Services encapsulate business logic following domain-driven design principles.

## Files

### `StoryService.php`
Comprehensive business logic for the Stories feature (social media-style stories).

**Responsibilities:**
- Creating story drafts
- Attaching media (images/videos) to stories
- Managing story lifecycle (draft → published → expired)
- Validating story content and media
- Coordinating with file management
- Story privacy and visibility controls

**Key Methods:**
- `createDraft()` - Creates a new story in draft status
- `attachMedia()` - Processes and attaches media files
- `publish()` - Publishes a story
- `expire()` - Marks stories as expired after 24 hours
- `delete()` - Removes story and associated media

### `StoryFeedService.php`
Handles story feed generation and caching.

**Responsibilities:**
- Generating personalized story feeds
- Caching feed data for performance
- Filtering stories by zone/location
- Ordering stories by relevance
- Handling story view tracking

**Key Methods:**
- `getFeed()` - Retrieves story feed for a user
- `refreshCache()` - Invalidates and rebuilds feed cache
- `trackView()` - Records story views

## Service Layer Architecture

### Why Services?
Services provide a cleaner alternative to the older CentralLogics pattern:

**Before (CentralLogics):**
```php
// In Controller
$story = StoryLogic::create_story($request);
```

**After (Services):**
```php
// In Controller
$story = $this->storyService->createDraft($request);
```

### Benefits
- **Testability:** Easier to unit test
- **Dependency Injection:** Clean dependency management
- **Single Responsibility:** Each service handles one domain
- **Type Hinting:** Better IDE support and type safety
- **Reusability:** Used across controllers, commands, jobs

## Service Structure

```php
namespace App\Services;

class StoryService
{
    protected $mediaService;
    protected $notificationService;

    public function __construct(MediaService $mediaService, NotificationService $notificationService)
    {
        $this->mediaService = $mediaService;
        $this->notificationService = $notificationService;
    }

    public function createDraft(array $data): Story
    {
        // Validate data
        $this->validateStoryData($data);

        // Create story
        $story = Story::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'status' => 'draft',
            'title' => $data['title'],
        ]);

        return $story;
    }

    public function publish(Story $story): bool
    {
        if (!$this->canPublish($story)) {
            throw new \Exception('Cannot publish story');
        }

        $story->update(['status' => 'published', 'published_at' => now()]);

        // Send notifications
        $this->notificationService->notifyFollowers($story);

        return true;
    }

    protected function validateStoryData(array $data): void
    {
        // Validation logic
    }

    protected function canPublish(Story $story): bool
    {
        // Business rules
        return $story->status === 'draft' && $story->media->count() > 0;
    }
}
```

## Usage in Controllers

### Dependency Injection
```php
namespace App\Http\Controllers\Vendor;

use App\Services\StoryService;

class StoryController extends Controller
{
    protected $storyService;

    public function __construct(StoryService $storyService)
    {
        $this->storyService = $storyService;
    }

    public function store(Request $request)
    {
        $story = $this->storyService->createDraft($request->validated());

        return response()->json(['story' => $story], 201);
    }

    public function publish(Story $story)
    {
        $this->storyService->publish($story);

        return response()->json(['message' => 'Story published']);
    }
}
```

### Method Injection
```php
public function index(StoryFeedService $feedService)
{
    $feed = $feedService->getFeed(auth()->user());

    return view('stories.feed', compact('feed'));
}
```

## Creating New Services

```bash
# No artisan command - create manually
# Create file: app/Services/YourService.php
```

### Service Template
```php
namespace App\Services;

class OrderProcessingService
{
    protected $paymentService;
    protected $inventoryService;

    public function __construct(
        PaymentService $paymentService,
        InventoryService $inventoryService
    ) {
        $this->paymentService = $paymentService;
        $this->inventoryService = $inventoryService;
    }

    public function processOrder(Order $order): bool
    {
        // Complex business logic here
        return true;
    }
}
```

## Service Patterns

### Transaction Management
```php
use Illuminate\Support\Facades\DB;

public function createOrderWithPayment(array $data): Order
{
    return DB::transaction(function () use ($data) {
        $order = Order::create($data);
        $this->paymentService->charge($order);
        $this->inventoryService->reduceStock($order);
        return $order;
    });
}
```

### Event Dispatching
```php
use App\Events\StoryPublished;

public function publish(Story $story): bool
{
    $story->update(['status' => 'published']);

    event(new StoryPublished($story));

    return true;
}
```

### Error Handling
```php
public function deleteStory(Story $story): bool
{
    try {
        $this->mediaService->deleteStoryMedia($story);
        $story->delete();
        return true;
    } catch (\Exception $e) {
        \Log::error('Story deletion failed: ' . $e->getMessage());
        throw new StoryDeletionException('Unable to delete story');
    }
}
```

### Caching
```php
use Illuminate\Support\Facades\Cache;

public function getPopularStories(): Collection
{
    return Cache::remember('popular_stories', 3600, function () {
        return Story::where('views', '>', 1000)
                    ->orderBy('views', 'desc')
                    ->limit(10)
                    ->get();
    });
}
```

## Service Registration

### Binding in Service Provider
```php
// app/Providers/AppServiceProvider.php

public function register()
{
    $this->app->singleton(StoryService::class, function ($app) {
        return new StoryService(
            $app->make(MediaService::class),
            $app->make(NotificationService::class)
        );
    });
}
```

### Auto-Resolution
Laravel automatically resolves services with type-hinted constructors. No manual binding needed for simple cases.

## Testing Services

```php
use Tests\TestCase;
use App\Services\StoryService;
use App\Models\Story;

class StoryServiceTest extends TestCase
{
    protected $storyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storyService = app(StoryService::class);
    }

    public function test_creates_draft_story()
    {
        $data = [
            'title' => 'Test Story',
            'description' => 'Test Description',
        ];

        $story = $this->storyService->createDraft($data);

        $this->assertInstanceOf(Story::class, $story);
        $this->assertEquals('draft', $story->status);
    }

    public function test_publishes_story_with_media()
    {
        $story = Story::factory()->create(['status' => 'draft']);
        $story->media()->create(['path' => 'test.jpg']);

        $result = $this->storyService->publish($story);

        $this->assertTrue($result);
        $this->assertEquals('published', $story->fresh()->status);
    }
}
```

### Mocking Services in Tests
```php
use Mockery;

public function test_controller_uses_service()
{
    $mockService = Mockery::mock(StoryService::class);
    $mockService->shouldReceive('createDraft')
                ->once()
                ->andReturn(new Story());

    $this->app->instance(StoryService::class, $mockService);

    $response = $this->post('/stories', ['title' => 'Test']);

    $response->assertStatus(201);
}
```

## Best Practices
- **Single Responsibility:** One service per domain/feature
- **Dependency Injection:** Inject dependencies via constructor
- **Type Hinting:** Use type hints for parameters and returns
- **Exceptions:** Throw meaningful exceptions for errors
- **Transactions:** Wrap multi-step operations in DB transactions
- **Logging:** Log important operations and errors
- **Caching:** Cache expensive operations
- **Events:** Dispatch events for side effects
- **Testing:** Write unit tests for service methods
- **Documentation:** Document complex business logic
- **Naming:** Use descriptive method names (verbs)

## Migration from CentralLogics

### Gradual Migration Strategy
1. Keep CentralLogics for existing features
2. Use Services for new features
3. Gradually refactor high-traffic CentralLogics to Services
4. Maintain backward compatibility during transition

### Example Migration
```php
// Old (CentralLogics/StoryLogic.php)
class StoryLogic
{
    public static function create_story($data)
    {
        // Logic here
    }
}

// New (Services/StoryService.php)
class StoryService
{
    public function createDraft(array $data): Story
    {
        // Same logic, better structure
    }
}
```

## When to Create a Service
- Complex business logic beyond simple CRUD
- Logic reused across multiple controllers
- Need for transaction management
- Multiple external service integrations
- Domain-specific operations
- Complex validation rules
- Multi-step processes
