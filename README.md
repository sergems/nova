# Nova Debt Counselling Website

A PHP website with a live visitor chat widget, a staff chat dashboard, and administrator-managed agent accounts and chat assignments.

## Run locally

From the project root, run:

```sh
php -S 127.0.0.1:8000 -t public_html
```

Open `http://127.0.0.1:8000/`. The staff login is at `/admin/` (also `/admin/chat-dashboard.php`). PHP must be able to write to the private `secure_storage/` directory at the project root; it is outside `public_html/` and holds chat messages, user-account hashes, assignments, and rate-limit data. The application creates this directory when needed.

## Staff accounts and chat assignment

The `admin` account can create, update, deactivate, reset passwords for, and delete agent accounts. The admin can assign or unassign each visitor conversation. Agents only receive chats assigned to their username and can reply only while the assignment remains active. Agent accounts are created from the dashboard; new agent passwords must be at least 12 characters.

## Before production

Use HTTPS and a production PHP server, ensure `secure_storage/` remains outside the web document root with restrictive filesystem permissions, and back it up securely. Replace any development credentials before public launch. Do not commit or distribute `secure_storage/` runtime files or real chat transcripts. The source download intentionally excludes that directory and Git history.
