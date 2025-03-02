# CLAUDE.md - Development Guidelines

## Build/Test/Lint Commands
- Start development server: `php artisan serve`
- Run all tests: `./vendor/bin/phpunit`
- Run single test: `./vendor/bin/phpunit --filter TestName`
- Code linting: `composer lint`
- Asset compilation: `npm run dev` or `npm run prod`
- Clear cache: `php artisan cache:clear`
- Database migrations: `php artisan migrate`
- Database seeding: `php artisan db:seed`

## Code Style Guidelines
- Follow PSR-12 coding standards
- Use type declarations where possible (PHP 7.3+/8.0 supported)
- Class naming: PascalCase (e.g., PropertyController)
- Method/variable naming: camelCase (e.g., getProperty)
- Constants: UPPER_CASE with underscores
- Database columns: snake_case
- Routes should be named and grouped by functionality
- Prefer dependency injection over facades when possible
- Validate input in controllers/requests
- Handle exceptions using try/catch or middleware
- Use Laravel's error handling mechanisms

## Project Structure Notes
- Custom module system in platform/ directory
- Plugin-based architecture for expandability
- Frontend assets managed via webpack.mix.js