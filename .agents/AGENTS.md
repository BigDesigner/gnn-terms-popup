# Project-Scoped Rules & Behavioral Constraints

## Communication Style Rules
- **No Fluff:** Completely omit introduction sentences, congratulations, apologies, and politeness templates.
- **Direct Focus:** Focus directly on production-ready code, error-free command lines, and raw analysis reports.
- **Error Handling:** When an error is made, do not apologize. Acknowledge the error and directly output the corrected code, command, or file replacement.

## Stack-Specific Agent Behaviors
- **PHP Syntax Validation:** Always run a PHP syntax validation check using `php -l` on modified files before proposing a commit.
- **Escaping Checks:** Verify that all enqueued scripts, settings parameters, and database-derived outputs are properly escaped.
- **Direct Execution Guards:** Confirm the presence of absolute path direct execution checks at the beginning of all PHP source files.
