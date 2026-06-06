---
name: implement-real-time-notifications-websocket
description: Workflow command scaffold for implement-real-time-notifications-websocket in ci-penjualan.
allowed_tools: ["Bash", "Read", "Write", "Grep", "Glob"]
---

# /implement-real-time-notifications-websocket

Use this workflow when working on **implement-real-time-notifications-websocket** in `ci-penjualan`.

## Goal

Implements or iterates on real-time notification features using websockets (Socket.IO), including client setup, server setup, UI integration, and notification handling.

## Common Files

- `public/libraries/js/script.js`
- `public/my-component/Socket.js`
- `app/Views/layouts/app.php`
- `app/Views/user/modal.php`
- `app/Views/layouts/navbar.php`
- `app/Config/Api.php`

## Suggested Sequence

1. Understand the current state and failure mode before editing.
2. Make the smallest coherent change that satisfies the workflow goal.
3. Run the most relevant verification for touched files.
4. Summarize what changed and what still needs review.

## Typical Commit Signals

- Add or update Socket.IO client initialization in public/libraries/js/script.js
- Update or create server-side websocket handler (e.g., public/my-component/Socket.js)
- Modify layout or modal views to inject websocket-related variables or UI (e.g., app/Views/layouts/app.php, app/Views/user/modal.php)
- Update notification display logic (e.g., using Swal.fire or dropdown in app/Views/layouts/navbar.php)
- Update configuration files if necessary (e.g., app/Config/Api.php)

## Notes

- Treat this as a scaffold, not a hard-coded script.
- Update the command if the workflow evolves materially.