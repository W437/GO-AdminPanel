# Flutter App - API Key Authentication Implementation

## Context
The backend API has been updated with API key authentication to prevent unauthorized access to public endpoints. All API requests must now include an `X-API-Key` header.

## Required Changes

### 1. Environment Configuration
The `API_KEY` is already added to your environment variables. Access it using your existing environment configuration pattern.

### 2. HTTP Client Modifications

#### Task: Add X-API-Key Header to All API Requests

**Files to Modify:**
- Primary API client (likely: `lib/data/api/api_client.dart` or `lib/helper/api_helper.dart`)
- Any custom HTTP interceptors
- Repository/service files that make direct HTTP calls

**Required Implementation:**

```dart
// If using http package:
import 'package:http/http.dart' as http;

Future<http.Response> makeApiRequest(String endpoint) async {
  final response = await http.get(
    Uri.parse('$baseUrl$endpoint'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-API-Key': apiKey,  // ADD THIS LINE TO ALL REQUESTS
    },
  );
  return response;
}

// If using Dio package:
import 'package:dio/dio.dart';

Dio getDioClient() {
  return Dio(
    BaseOptions(
      baseUrl: baseUrl,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-API-Key': apiKey,  // ADD THIS TO BASE OPTIONS
      },
    ),
  );
}

// If using GetX GetConnect:
class ApiClient extends GetConnect {
  @override
  void onInit() {
    httpClient.baseUrl = baseUrl;
    httpClient.defaultContentType = "application/json";
    httpClient.addRequestModifier<void>((request) {
      request.headers['X-API-Key'] = apiKey;  // ADD THIS MODIFIER
      return request;
    });
  }
}
```

### 3. Specific Changes Needed

#### A. Update Base API Client
Locate your main API client class and ensure ALL request methods include the header:
- GET requests
- POST requests
- PUT requests
- DELETE requests
- PATCH requests

#### B. Update Repository Pattern (if applicable)
If you're using repositories that bypass the main API client, add the header there too.

#### C. Update WebSocket/Real-time Connections (if applicable)
If using WebSockets for real-time features, add the API key to connection headers.

### 4. Common File Locations to Check

Search for these patterns in your codebase:
```bash
# Find files that make HTTP requests
grep -r "http.get\|http.post\|Dio()" lib/
grep -r "GetConnect\|BaseOptions" lib/
grep -r "baseUrl\|BASE_URL" lib/

# Find API client files
find lib/ -name "*api*.dart"
find lib/ -name "*client*.dart"
find lib/ -name "*repository*.dart"
```

### 5. Testing Checklist

After implementing changes, test these scenarios:

- [ ] App launches successfully and fetches initial config (`/api/v1/config`)
- [ ] User can browse restaurants and products (public endpoints)
- [ ] User can log in (authentication endpoints)
- [ ] Authenticated features work (orders, profile, etc.)
- [ ] Error handling works for invalid/missing API key
- [ ] App works on both Android and iOS

### 6. Error Handling

Add proper error handling for API key issues:

```dart
if (response.statusCode == 401) {
  final error = jsonDecode(response.body);
  if (error['error'] == 'API key required' || error['error'] == 'Invalid API key') {
    // Log error - this shouldn't happen if API key is correct
    print('ERROR: API Key authentication failed. Check environment configuration.');
    // Optionally show user-friendly error
    throw Exception('Unable to connect to server. Please update the app.');
  }
}
```

### 7. Security Best Practices

- ✅ Store API key in environment variables (already done)
- ✅ Do NOT commit the actual API key to version control
- ✅ Use `.env` or environment-specific config files
- ✅ Obfuscate the key in production builds if possible
- ✅ The key is in cleartext in the app - this is acceptable for app-only access control

### 8. Validation

After implementation, verify the header is being sent:

```dart
// Add debug logging temporarily
print('Request headers: ${request.headers}');
// Should show: X-API-Key: [your key]
```

Or use a network debugging tool:
- Charles Proxy
- Proxyman
- Flutter DevTools Network tab

## Expected Behavior

**Before:**
```
GET https://api.hopa.delivery/api/v1/config
Response: 401 {"error": "API key required"}
```

**After:**
```
GET https://api.hopa.delivery/api/v1/config
Headers: X-API-Key: pI4lAbcAy9LB0vLuPTxrq7WRow5PUsVDB5JeIH5hsqc=
Response: 200 { "business_name": "Hopa! Admin", ... }
```

## API Key Value

```
X-API-Key: pI4lAbcAy9LB0vLuPTxrq7WRow5PUsVDB5JeIH5hsqc=
```

This should be stored in your environment configuration and accessed via:
```dart
static final String apiKey = AppConfig.apiKey;
// or
static final String apiKey = dotenv.env['API_KEY']!;
// or however your app accesses environment variables
```

## Questions?

If the app shows errors after implementation:
1. Check the API key is correctly copied (no extra spaces)
2. Verify the header name is exactly `X-API-Key` (case-sensitive)
3. Ensure ALL API requests include the header
4. Check network logs to see what's being sent

## Summary

**What to do:**
1. Add `X-API-Key` header to all HTTP requests
2. Use the API key from environment variables
3. Test all app features still work
4. Handle 401 errors gracefully

**Time estimate:** 15-30 minutes depending on codebase structure
