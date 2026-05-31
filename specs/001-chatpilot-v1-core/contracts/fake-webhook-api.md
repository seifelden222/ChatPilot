# Contract: Fake Provider Webhook API

## Endpoint

- Method: POST
- Path: /api/webhooks/fake/{channel}
- Auth: signed secret header scoped to channel/workspace (implementation detail deferred)
- Content-Type: application/json

## Request Body (Provider Payload)

```json
{
  "event_id": "evt_123",
  "event_type": "message.received",
  "occurred_at": "2026-05-31T12:00:00Z",
  "actor": {
    "identity": "fake:actor:42",
    "display_name": "Jane Doe"
  },
  "thread": {
    "id": "th_001"
  },
  "content": {
    "id": "ct_100",
    "text": "hello bot"
  },
  "metadata": {
    "raw": true
  }
}
```

## Synchronous Response

- 202 Accepted

```json
{
  "status": "accepted",
  "webhook_event_id": 12345,
  "queued": true
}
```

- 422 Unprocessable Entity (invalid payload)

```json
{
  "status": "rejected",
  "errors": {
    "event_id": ["The event_id field is required."]
  }
}
```

- 404 Not Found (channel not found for webhook path)

```json
{
  "status": "rejected",
  "message": "Channel not found."
}
```

## Behavioral Contract

- Raw payload MUST be persisted in WebhookEvent before queue dispatch.
- Endpoint MUST return quickly and MUST NOT perform heavy processing inline.
- Duplicate event_id in same workspace+channel scope MUST NOT create duplicate interaction effects.
