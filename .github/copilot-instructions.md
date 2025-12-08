# MOORC Site - GitHub Copilot Instructions

## Project Overview

This is the official website for MOORC (Межрегиональная общественная организация развития спидкубинга) - the Russian Interregional Speedcubing Federation. The site manages competitions, participant registrations, rankings, and results for speedcubing events.

## Technology Stack

- **Backend**: PHP 8.2+, PSR-4 autoloading via Composer
- **Database**: PostgreSQL 16 with PDO
- **Frontend**: HTML, CSS (minimalist design), vanilla JavaScript
- **Infrastructure**: Docker Compose (optional)
- **Dependencies**: vlucas/phpdotenv for environment configuration

## Project Structure

```
src/
├── Controllers/      # Business logic controllers
├── Database.php      # Database connection singleton
├── Helpers.php       # Utility functions
├── Router.php        # URL routing
└── View.php          # Template rendering

views/               # HTML templates
├── layout.php       # Main layout wrapper
├── auth/            # Authentication pages
├── competitions/    # Competition pages
├── profile/         # User profile pages
└── ...

public/
├── index.php        # Application entry point
└── assets/          # CSS, JS, images

migrations/          # SQL migration files (numbered sequentially)
```

## Coding Standards

### PHP Code Style

- Use **PSR-4** autoloading with `App\` namespace for `src/` directory
- Use **strict types** declaration at the top of files where appropriate
- Follow **PSR-12** coding style
- Use **type hints** for parameters and return types
- Use **camelCase** for methods and variables
- Use **PascalCase** for class names
- Prefer **static methods** in utility classes like `Helpers`

### Naming Conventions

- Controllers: `{Feature}Controller.php` (e.g., `CompetitionController.php`)
- Methods: Descriptive action names (e.g., `index()`, `show()`, `store()`)
- Database tables: Plural snake_case (e.g., `users`, `competition_disciplines`)
- Database columns: Snake_case (e.g., `user_id`, `created_at`)

### HTML/View Templates

- Use PHP short echo tags `<?= ?>` for output
- **ALWAYS** use `Helpers::e()` for HTML output to prevent XSS attacks
- Keep business logic out of views
- Use Russian language for user-facing text
- Follow minimalist design approach

## Security Practices

### Critical Security Requirements

1. **SQL Injection Prevention**
   - ALWAYS use PDO prepared statements with parameter binding
   - NEVER concatenate user input into SQL queries
   - Use `execute()` with bound parameters, never `query()` with direct parameters
   - Example: `$stmt->execute(['id' => $id])` ✅ NOT `$stmt->query("... WHERE id = $id")` ❌

2. **XSS Prevention**
   - ALWAYS use `Helpers::e()` to escape HTML output
   - `Helpers::e()` wraps `htmlspecialchars()` with `ENT_QUOTES` and `UTF-8` encoding
   - Example: `<?= Helpers::e($user['name']) ?>` ✅

3. **Password Security**
   - Use `password_hash()` for storing passwords
   - Use `password_verify()` for checking passwords
   - Never store plain-text passwords

4. **Session Management**
   - Check `session_status()` before calling `session_start()`
   - Use `Helpers::isAuthenticated()` to check auth status
   - Use `Helpers::isAdmin()` to check admin privileges

### Authentication & Authorization

- `Helpers::isAuthenticated()` - Check if user is logged in
- `Helpers::isAdmin()` - Check if user has admin role (uses `is_admin` boolean field)
- `Helpers::currentUserId()` - Get current user ID from session
- Admin-only features require explicit `is_admin` check

## Database Conventions

### Database Operations

1. **Connection**
   - Use `Database::getInstance()->getConnection()` singleton pattern
   - Connection is configured via `.env` file

2. **Queries**
   - Always use prepared statements: `$pdo->prepare()` then `$stmt->execute()`
   - Bind parameters using associative arrays
   - Example:
     ```php
     $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
     $stmt->execute(['id' => $userId]);
     ```

3. **Transactions**
   - Use transactions for multi-step operations (e.g., creating competition + disciplines)
   - Pattern: `beginTransaction()` → operations → `commit()` / `rollBack()`

4. **Time Storage**
   - Times are stored as **centiseconds** (1/100 second) in the database
   - Use `Helpers::formatTime()` to convert centiseconds to readable format (mm:ss.ss)

5. **Migrations**
   - Apply migrations in sequential order: `001_init.sql`, `002_speedcubing_schema.sql`, `003_add_admin_role.sql`
   - Use `psql` command to apply migrations
   - Example: `psql -h 127.0.0.1 -p 5433 -U moorc -d moorc_dev -f migrations/001_init.sql`
   - Note: Use `.pgpass` file or prompt for password instead of exposing it in command history

### Database Schema Key Points

- `users` table has `is_admin` boolean field for admin privileges
- Competition dates use PostgreSQL `DATE` type
- Results times stored as INTEGER (centiseconds)
- Foreign keys use `ON DELETE CASCADE` for automatic cleanup

## Routing

Routes are defined in `public/index.php`:

```php
// Static GET route
$router->get('/path', fn() => $controller->method());

// POST route with request data
$router->post('/path', fn($req) => $controller->method($req));

// Dynamic parameter
$router->get('/path/{id}', fn($req, $id) => $controller->method((int)$id));
```

- Router supports GET and POST methods
- Dynamic routes use `{param}` syntax
- Route parameters are passed to callbacks as arguments

## View Rendering

Use `View::render()` for template rendering:

```php
return View::render('folder.template', [
    'title' => 'Page Title',
    'data' => $data
]);
```

- Template paths use dot notation (e.g., `'auth.login'` → `views/auth/login.php`)
- All views are wrapped by `layout.php` automatically
- Variables are extracted and available in templates

## Helper Functions

From `Helpers` class:
- `Helpers::e($string)` - Escape HTML (REQUIRED for all user output)
- `Helpers::formatTime($centiseconds)` - Format time from centiseconds
- `Helpers::formatDate($date, $format)` - Format dates
- `Helpers::isAuthenticated()` - Check if user is logged in
- `Helpers::isAdmin()` - Check if user is admin
- `Helpers::currentUserId()` - Get current user ID

## Development Workflow

### Environment Setup

1. Copy `.env.example` to `.env`
2. Configure database credentials in `.env`
3. Install dependencies: `composer install`
4. Start PostgreSQL: `docker compose up -d` or local installation
5. Apply migrations in order (001, 002, 003)
6. Start dev server: `composer start` or `php -S localhost:8000 -t public`

### Adding New Features

1. Create controller in `src/Controllers/`
2. Add routes in `public/index.php`
3. Create views in `views/`
4. Always validate and sanitize user input
5. Use prepared statements for database queries
6. Escape output with `Helpers::e()`

## Git Pager Settings

When using git commands, **always disable pagers** to avoid interactive output issues:
- Use `git --no-pager` for commands like `status`, `diff`, `log`, `show`
- Example: `git --no-pager status`, `git --no-pager diff`

## Language & Localization

- User-facing content is in **Russian**
- Code comments can be in English or Russian
- Variable names and function names use English
- Database field names use English

## Testing & Quality

- Before making changes, understand existing functionality
- Test database operations manually
- Verify admin-only features check `is_admin` properly
- Ensure all user input is validated and sanitized
- Check that SQL queries use prepared statements
- Verify XSS protection with `Helpers::e()`

## Special Notes

- A test admin account is created by migration 003 for development purposes only
- **NEVER use default credentials in production - always change passwords immediately**
- PostgreSQL runs on port 5433 when using Docker Compose
- Adminer (database UI) available at http://localhost:8080 when using Docker

## Best Practices Reminders

✅ DO:
- Use prepared statements for ALL database queries
- Escape ALL user output with `Helpers::e()`
- Check authentication/authorization before protected actions
- Use transactions for multi-table operations
- Follow PSR-4 and PSR-12 standards
- Keep views simple, logic in controllers
- Store times in centiseconds, format with `Helpers::formatTime()`

❌ DON'T:
- Concatenate user input into SQL queries
- Output unescaped user data to HTML
- Store plain-text passwords
- Use `query()` instead of prepared statements
- Put business logic in views
- Forget to check admin status for admin-only features
- Remove working code unless fixing a security vulnerability
