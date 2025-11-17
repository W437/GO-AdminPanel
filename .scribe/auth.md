# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

    This API uses Laravel Passport for authentication. You can obtain a token by:

    <b>For Customers:</b> POST to <code>/api/v1/customer/auth/login</code>
    <b>For Vendors:</b> POST to <code>/api/v1/vendor/auth/login</code>
    <b>For Delivery Personnel:</b> POST to <code>/api/v1/auth/delivery-man/login</code>

    Include the token in the Authorization header as: <code>Bearer YOUR_TOKEN</code>
