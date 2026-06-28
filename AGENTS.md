# AGENTS.md

Shared instructions for coding agents working in slothsoft packages. Keep package-specific purpose, architecture, commands, and notes in README.md.

## Runtime Environment

The file `.env` is the source of truth for runtime configuration. In particular, `PHP_VERSION` is authoritative, and all code in the package must work with it.

The machine is configured so `php` and `composer` use the correct PHP version. Run them directly.

## Tools

Use these tools directly:

| Tool | Use for |
|------|---------|
| `composer` | PHP dependencies |
| `php` | Running PHP |
| `npx` | One-off npm package execution |

`git` is read-only for agents. Use inspection commands such as `git status`, `git log`, `git diff`, `git show`, `git blame`, and `git branch --list` as needed. Never run mutating git commands such as `git commit`, `git add`, `git push`, `git pull`, `git merge`, `git rebase`, `git checkout`, `git switch`, `git reset`, `git stash`, `git tag`, or branch deletion.

The user handles all version control.

If the user says `ping`, reply with `pong` and nothing else.

## Testing

The PHPUnit config is `phpunit.xml`.

Run tests with:

```bash
vendor/bin/phpunit
```

## Documentation

The PHPDoc config is `phpdoc.xml`.

Generate documentation with:

```bash
vendor/bin/phpdoc
```

## MCP Servers

Use the connected MCP servers when they are relevant to the task.

When editing files in a JetBrains IDE project, use the JetBrains MCP after the edit to retrieve IDE inspections for the touched files. Treat this as part of validation alongside running tests or syntax checks, and report any remaining warnings that are not safe to fix.

## Agent Workflow

- Work from the current package root.
- Read `README.md` for package-specific context before making non-trivial changes.
- Prefer fast local inspection tools before making changes.
- Keep edits scoped to the requested task and relevant package boundary.
- Do not refactor unrelated code while fixing an issue.
- Use normal patch/edit tools for manual edits. Avoid shell write tricks that make changes hard to review.
- Never use destructive cleanup commands or revert user changes unless explicitly asked for that exact operation.
