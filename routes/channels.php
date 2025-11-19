<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * BROADCAST CHANNELS FILE
 * ========================
 * Purpose: Defines WebSocket/Pusher broadcast channel authorization
 * Used for: Real-time features like live order tracking, notifications
 *
 * This file registers broadcast channels for:
 * - Private user channels (user-specific notifications)
 * - Order tracking channels (customer/restaurant/delivery real-time updates)
 * - Chat/messaging channels (customer support, delivery chat)
 * - Admin broadcast channels (system-wide alerts)
 *
 * How it works:
 * 1. When a client tries to subscribe to a private channel
 * 2. Laravel checks authorization using these callbacks
 * 3. Returns true/false to allow/deny subscription
 *
 * Channel Types:
 * - Public channels: No auth required (not defined here)
 * - Private channels: Auth required (prefix: private-)
 * - Presence channels: Auth + user info (prefix: presence-)
 *
 * WebSocket Implementation: Using Laravel WebSockets package
 * Frontend: Usually Echo.js listens to these channels
 */

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
