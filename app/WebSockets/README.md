# WebSockets

## Purpose
Real-time WebSocket functionality for the GO-AdminPanel application. Provides live, bidirectional communication between clients and server for real-time features.

## Structure

### `/Handler`
WebSocket event handlers for processing real-time operations.

#### `DMLocationSocketHandler.php`
Handles real-time delivery person location tracking.

**Responsibilities:**
- Receives GPS coordinates from delivery personnel
- Broadcasts location updates to relevant clients (customers, vendors, admins)
- Stores location history for tracking
- Handles connection/disconnection events
- Manages delivery person online/offline status

**Events Handled:**
- `location.update` - When delivery person sends new GPS coordinates
- `delivery.connected` - When delivery person connects to socket
- `delivery.disconnected` - When delivery person goes offline

## How WebSockets Work

### Connection Flow
1. Client (mobile app/web) initiates WebSocket connection
2. Server authenticates the connection
3. Client subscribes to relevant channels
4. Server broadcasts events to subscribed clients
5. Connection remains open for bidirectional communication

### Traditional HTTP vs WebSocket

**HTTP (Request-Response):**
```
Client → Request → Server
Server → Response → Client
(Connection closes)
```

**WebSocket (Persistent Connection):**
```
Client ←→ WebSocket ←→ Server
(Connection stays open)
```

## Location Tracking Example

### Delivery Person Sends Location
```javascript
// From delivery person's mobile app
socket.emit('location.update', {
    delivery_man_id: 123,
    latitude: 23.8103,
    longitude: 90.4125,
    timestamp: Date.now()
});
```

### Server Processes (DMLocationSocketHandler)
```php
namespace App\WebSockets\Handler;

class DMLocationSocketHandler
{
    public function onLocationUpdate($socket, $data)
    {
        // Validate data
        $deliveryManId = $data['delivery_man_id'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];

        // Store location
        TrackDeliveryman::create([
            'delivery_man_id' => $deliveryManId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'created_at' => now(),
        ]);

        // Get active order for this delivery person
        $order = Order::where('delivery_man_id', $deliveryManId)
                     ->where('order_status', 'confirmed')
                     ->first();

        if ($order) {
            // Broadcast to customer
            $this->broadcastToChannel("order.{$order->id}", [
                'event' => 'delivery_location_updated',
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            // Broadcast to vendor
            $this->broadcastToChannel("restaurant.{$order->restaurant_id}", [
                'event' => 'delivery_location_updated',
                'order_id' => $order->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
        }
    }

    public function onConnect($socket, $data)
    {
        $deliveryManId = $data['delivery_man_id'];

        // Update online status
        DeliveryMan::where('id', $deliveryManId)->update([
            'online_status' => 'online',
            'last_seen' => now(),
        ]);

        // Join delivery person's channel
        $socket->join("delivery.{$deliveryManId}");
    }

    public function onDisconnect($socket)
    {
        $deliveryManId = $socket->deliveryManId;

        // Update offline status
        DeliveryMan::where('id', $deliveryManId)->update([
            'online_status' => 'offline',
            'last_seen' => now(),
        ]);
    }

    protected function broadcastToChannel($channel, $data)
    {
        // Broadcast to all connected clients on channel
        app('websocket')->to($channel)->emit('message', $data);
    }
}
```

### Client Receives Update
```javascript
// Customer's app listening for updates
socket.on('message', (data) => {
    if (data.event === 'delivery_location_updated') {
        updateMapMarker(data.latitude, data.longitude);
    }
});
```

## WebSocket Server Setup

### Laravel WebSockets (beyondcode/laravel-websockets)
```bash
# Install package
composer require beyondcode/laravel-websockets

# Publish config
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider"

# Run WebSocket server
php artisan websockets:serve
```

### Configuration
```php
// config/websockets.php
'apps' => [
    [
        'id' => env('PUSHER_APP_ID'),
        'name' => env('APP_NAME'),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'enable_client_messages' => true,
        'enable_statistics' => true,
    ],
],
```

## Broadcasting Events

### Using Laravel Broadcasting
```php
// In your event class
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeliveryLocationUpdated implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $latitude;
    public $longitude;
    public $orderId;

    public function __construct($latitude, $longitude, $orderId)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->orderId = $orderId;
    }

    public function broadcastOn()
    {
        return new Channel("order.{$this->orderId}");
    }

    public function broadcastAs()
    {
        return 'delivery.location.updated';
    }
}
```

### Dispatching Events
```php
// In controller or service
use App\Events\DeliveryLocationUpdated;

broadcast(new DeliveryLocationUpdated($lat, $lng, $orderId));
```

## Channel Authorization

### Private Channels
```php
// In routes/channels.php
Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    return $user->id === Order::find($orderId)->user_id;
});

Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    return $user->restaurant_id === $restaurantId;
});
```

## Client-Side Implementation

### JavaScript (Web)
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

// Listen to order updates
Echo.channel('order.123')
    .listen('.delivery.location.updated', (e) => {
        console.log(e.latitude, e.longitude);
        updateMap(e.latitude, e.longitude);
    });
```

### Mobile App (React Native)
```javascript
import io from 'socket.io-client';

const socket = io('https://yourapp.com:6001', {
    transports: ['websocket'],
    auth: {
        token: userToken
    }
});

socket.on('connect', () => {
    console.log('Connected to WebSocket');
});

socket.on('delivery.location.updated', (data) => {
    updateDeliveryLocation(data.latitude, data.longitude);
});

// Send location (delivery person app)
setInterval(() => {
    navigator.geolocation.getCurrentPosition((position) => {
        socket.emit('location.update', {
            delivery_man_id: deliveryManId,
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            timestamp: Date.now()
        });
    });
}, 5000); // Every 5 seconds
```

## Real-Time Use Cases

### 1. Live Order Status
```php
// When order status changes
broadcast(new OrderStatusChanged($order));
```

### 2. Real-Time Notifications
```php
// New order notification to vendor
broadcast(new NewOrderReceived($order))->toOthers();
```

### 3. Chat Messages
```php
// Real-time messaging
broadcast(new MessageSent($message, $conversation));
```

### 4. Live Dashboard Updates
```php
// Admin dashboard real-time stats
broadcast(new DashboardStatsUpdated($stats));
```

## Performance Considerations

### Throttling Location Updates
```php
// Limit to one update per 5 seconds per delivery person
Cache::remember("dm_location_{$deliveryManId}", 5, function () use ($data) {
    // Process location update
    return true;
});
```

### Cleaning Old Location Data
```php
// Scheduled task to clean old tracking data
TrackDeliveryman::where('created_at', '<', now()->subDays(7))->delete();
```

## Monitoring & Debugging

### WebSocket Dashboard
Access the WebSocket dashboard:
```
http://yourapp.com/laravel-websockets
```

### Logging
```php
\Log::channel('websocket')->info('Location update', $data);
```

## Best Practices
- ✅ Authenticate WebSocket connections
- ✅ Authorize channel subscriptions
- ✅ Throttle high-frequency events (location updates)
- ✅ Clean up old connection data
- ✅ Handle reconnection logic on client
- ✅ Use heartbeat/ping-pong to detect dead connections
- ✅ Implement graceful degradation (fallback to polling)
- ✅ Monitor WebSocket server performance
- ✅ Use SSL/TLS for secure connections (wss://)
- ✅ Scale WebSocket servers horizontally if needed

## Production Deployment

### Using Supervisor
```ini
[program:websockets]
command=/usr/bin/php /path/to/artisan websockets:serve
numprocs=1
autostart=true
autorestart=true
user=www-data
```

### Using Redis for Broadcasting
```php
// .env
BROADCAST_DRIVER=redis

// config/broadcasting.php
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
],
```

### Load Balancing
Use Redis to share state across multiple WebSocket servers for horizontal scaling.
