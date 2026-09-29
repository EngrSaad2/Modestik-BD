# Agent Behavior and Instructions

## Deployment instructions
Whenever the user asks to "update the server" or "deploy":
1. Deploy local changes to the remote server using the `deploy_server` skill.
2. Locate the changed files (using the script `deploy.ps1` under `.agents/skills/update-server/deploy.ps1`).
3. Run the script: `powershell -File .agents/skills/update-server/deploy.ps1`.
4. Ask the user if they want to run `php artisan migrate` or other artisan commands on the remote server.
5. If yes, run them via:
   `ssh -i C:\Users\Admin\.ssh\modestik_nopw modestik@209.42.27.61 "cd public_html && php artisan migrate"`
