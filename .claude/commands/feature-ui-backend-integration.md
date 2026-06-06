---
name: feature-ui-backend-integration
description: Workflow command scaffold for feature-ui-backend-integration in ci-penjualan.
allowed_tools: ["Bash", "Read", "Write", "Grep", "Glob"]
---

# /feature-ui-backend-integration

Use this workflow when working on **feature-ui-backend-integration** in `ci-penjualan`.

## Goal

Implements a new feature that involves backend controller, route, and corresponding frontend view/modal updates for a new entity or test scenario.

## Common Files

- `app/Controllers/`
- `app/Config/Routes.php`
- `app/Views/*/index.php`
- `app/Views/*/modal.php`
- `writable/debugbar/index.html`

## Suggested Sequence

1. Understand the current state and failure mode before editing.
2. Make the smallest coherent change that satisfies the workflow goal.
3. Run the most relevant verification for touched files.
4. Summarize what changed and what still needs review.

## Typical Commit Signals

- Add or update backend controller (e.g., app/Controllers/...)
- Update routing configuration (e.g., app/Config/Routes.php)
- Create or update frontend view files (e.g., app/Views/feature/index.php, app/Views/feature/modal.php)
- Optionally, update debug or test output (e.g., writable/debugbar/index.html)

## Notes

- Treat this as a scaffold, not a hard-coded script.
- Update the command if the workflow evolves materially.