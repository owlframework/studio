# 🔌 API Directory

This directory is dedicated to handling all API communications, webhook listeners, and third-party service integrations for your application built on the Owl Framework.

---

## 📂 Purpose & Usage

The `api/` folder acts as the gateway for external clients, mobile applications, and third-party platforms (such as payment gateways, automated notifications, or external data feeds) to interact with your system, fast and secure.

*   **JSON Endpoints:** Expose structured data endpoints for frontend JavaScript apps or mobile clients.
*   **Webhooks:** Receive secure payloads from external providers (e.g., payment confirmations, crypto node events).
*   **Third-Party Services:** Isolate scripts communicating with external APIs away from your core MVC controllers.

---

## 🛡️ Best Practices

When building or adding new endpoints inside this directory, ensure you adhere to the framework's security standards:

1.  **Authentication & Security:** Validate incoming API keys, Bearer tokens, or cryptographic signatures (such as webhook HMAC verification) before processing requests.
2.  **Response Formatting:** Always return consistent JSON payloads with appropriate HTTP status codes (e.g., `200 OK`, `400 Bad Request`, `401 Unauthorized`).
3.  **Input Validation:** Pass all incoming request data through the framework's `Validator` before executing database queries or business logic.

---

## 📝 Example Endpoint Structure

```php
<?php
// Example structure for an API endpoint file inside /api/
header('Content-Type: application/json');

// Perform security and method checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Process payload and return response
echo json_encode([
    'status' => 'success',
    'message' => 'API request processed successfully'
]);