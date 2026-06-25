# Deployment Procedures

## CI/CD Pipeline
- **Linting:** Automated PHPCS check on push.
- **Testing:** Unit tests run via PHPUnit (if applicable).
- **Build:** No compilation required (Vanilla CSS/JS). Ensure all assets are properly enqueued in the main plugin file.

## Staging & Production
- **Staging:** Sync `main` branch to staging environment.
- **Production:** Deploy tagged releases to live server.
