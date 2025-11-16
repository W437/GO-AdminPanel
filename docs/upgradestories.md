You are updating the backend and database to support Instagram-style text overlays on stories.
The mobile frontend will send structured overlay data as JSON.
You must update the schema in the repo and then run migrations on the deployed DB via SSH.

1. Goal

Extend the existing story system so a story can have:

A media asset (photo or video).

One or more text overlays, with position, style, and size information.

Provide endpoints so the frontend can:

Upload media.

Create a story with overlays using a clear JSON shape.

Ensure the story fetch endpoints return these overlays (even if the current viewer doesn’t yet draw them).

2. Database changes

Assume there is an existing stories table (or equivalent).
You need to add fields (or confirm existing ones) so we can store text overlays.

2.1 Required columns on stories

Make sure stories has at least:

id

user_id

type – image / video

media_url

thumbnail_url (especially for videos)

duration_seconds

created_at

expires_at (if stories expire)

New columns to add:

overlays – JSON or JSONB column storing an array of overlay objects.

has_overlays – boolean (default false) for easy querying/filtering.

The overlay JSON for each text element should match what the frontend sends, e.g.:

{
  "id": "local-id-or-uuid",
  "text": "THIS IS CLICKING ON TEXT TO CREATE OR EDIT",
  "position": { "x": 0.5, "y": 0.3 }, // 0–1 normalized
  "scale": 1.2,
  "rotation": 0,
  "fontFamily": "Directional",
  "stylePreset": "directional",
  "color": "#FFFFFFFF",
  "backgroundColor": "#000000CC",
  "backgroundMode": "pill",
  "alignment": "center",
  "zIndex": 1
}




You do not need complex validation at DB level; just store the JSON and expose it back.

2.2 Migrations

Add migration files in the repo that:

Add overlays (JSON/JSONB, nullable).

Add has_overlays (boolean, default false).

Apply migrations:

Locally / dev.

In the deployment database using SSH (follow existing project’s migration procedure).

3. Media upload endpoint

If not already present, ensure there is an endpoint to upload story media.

Input:

Authenticated user.

File (image or video).

type: "image" or "video".

Behavior:

Store file (e.g., object storage/CDN).

For videos, create a thumbnail image and store it too.

Output:

mediaUrl: URL to the stored image/video.

thumbnailUrl: URL to thumbnail (mandatory for video, optional for image).

Optionally durationSeconds for video if you can extract it.

This endpoint will be called before creating the story.

4. Create story endpoint

Expose an API endpoint that the frontend uses after media upload.

Method: POST
Body (JSON):

type: "image" or "video".

mediaUrl: string.

thumbnailUrl: string (required for video).

durationSeconds: integer (frontend sends; backend can clamp/validate).

overlays: array of overlays (JSON) as described above.

Behavior:

Validate:

type is allowed.

mediaUrl and thumbnailUrl look valid.

durationSeconds is within acceptable range (e.g. 1–30 sec for images; up to configured max for video).

overlays is either an empty array or an array of JSON objects.

Insert a new story:

Set overlays to the received JSON.

Set has_overlays = true if the array is non-empty, otherwise false.

Return the created story object.

Response payload:

Include at least:

id

userId

type

mediaUrl

thumbnailUrl

durationSeconds

overlays

createdAt

expiresAt

This shape should be compatible with existing story fetch endpoints.

5. Fetch story endpoints

Update existing “get stories” endpoints so that:

They include the overlays field in the story object.

They keep all existing fields unchanged so current clients don’t break.

No rendering or server-side compositing is required; this is purely metadata storage and retrieval for now.

6. Video behavior (for clarity with frontend)

The looping and tap-to-mute behavior is handled entirely on the frontend in the editor screen:

Backend does not need to store mute state.

Backend just serves the video file via mediaUrl and, optionally, thumbnailUrl.

7. Deliverables

You should:

Update models/entities to include overlays (+ has_overlays if used).

Add database migrations and run them locally.

Run the same migrations on the deployment DB via SSH.

Ensure:

Media upload endpoint returns mediaUrl and thumbnailUrl.

Create story endpoint accepts overlay JSON and stores it.

Story fetch endpoints return overlays to the client.