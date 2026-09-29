---
name: deploy_server
description: Deploys local updates/changes to the remote server. Triggers when user says "update the server", "deploy to server", "deploy", or similar.
---

# Deploy Server Skill

Automate local code deployment to remote server using ssh credentials.

## Server Connection
- Command: `ssh -i C:\Users\Admin\.ssh\modestik_nopw modestik@209.42.27.61`
- Remote Directory: `/home/modestik/public_html`

## Trigger Actions
When user says "update the server" or "deploy":
1. Run PowerShell script `deploy.ps1` to upload changed files.
2. Ask user if they need Laravel commands run (e.g. `php artisan migrate`, `php artisan optimize:clear`).
3. If yes, run them over SSH.
