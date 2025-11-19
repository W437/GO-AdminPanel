# Jobs

## Purpose
Asynchronous and queued job processing for the GO-AdminPanel application. Jobs handle time-intensive tasks in the background to avoid blocking HTTP requests.

## Files

### `ProcessStoryMedia.php`
Queued job that handles video and image processing for stories.

**Responsibilities:**
- Video transcoding and optimization
- Image resizing and compression
- Thumbnail generation
- Media validation

**Why Queued?** Media processing is resource-intensive and can take several seconds. Processing in a queue prevents users from waiting for uploads to complete.

### `PurgeStoryMedia.php`
Queued job that cleans up expired story media files.

**Responsibilities:**
- Deleting old media files from storage
- Cleaning up database references
- Managing disk space

**Why Queued?** File deletion and cleanup can be done asynchronously without user interaction.

## How Jobs Work

### Queue Flow
1. Job is dispatched from controller/service
2. Job is added to queue (database, Redis, etc.)
3. Queue worker picks up the job
4. Job processes in the background
5. Result is stored or notification sent

### Dispatching Jobs

```php
use App\Jobs\ProcessStoryMedia;

// Dispatch immediately
ProcessStoryMedia::dispatch($story, $mediaFile);

// Dispatch with delay
ProcessStoryMedia::dispatch($story, $mediaFile)->delay(now()->addMinutes(5));

// Dispatch to specific queue
ProcessStoryMedia::dispatch($story, $mediaFile)->onQueue('media');
```

### Running Queue Workers

```bash
# Start queue worker
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=media,default

# Run supervisor for production
# (Configure supervisor to keep workers running)
```

## Creating New Jobs

```bash
# Generate a new job
php artisan make:job ProcessNewFeature
```

## Job Structure

```php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // Accept dependencies
    }

    public function handle()
    {
        // Job logic here
    }
}
```

## Best Practices
- Keep jobs focused on a single task
- Make jobs idempotent (safe to retry)
- Handle failures gracefully with try-catch
- Set appropriate retry limits
- Use job batching for related tasks
- Monitor failed jobs
- Consider job timeout settings

## Common Use Cases
- Email sending
- Image/video processing
- Report generation
- Data imports/exports
- Third-party API calls
- Database cleanup tasks
- Notification delivery
