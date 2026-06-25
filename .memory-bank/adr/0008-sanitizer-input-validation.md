# ADR 0008: Sanitizer Input Validation

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
WordPress admin forms process arrays of settings values. If malformed or crafted non-array data is submitted to the settings API, or if the sanitization callback is invoked directly with invalid arguments, it can lead to PHP type errors, warnings, or unexpected security vulnerabilities due to type confusion.

## Decision
Implement a strict `is_array()` type check at the beginning of the settings sanitizer callback. If the input is not a valid array, immediately abort processing and return the default settings block.

## Consequences
- Protects the option saving pipeline from warnings and unexpected execution paths.
- Enhances code robustness and security hardening (Defense in Depth).

## Evidence
- `if (!is_array($input)) { return $this->get_defaults(); }` in `gnn-terms-popup.php` -> `sanitize()`.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
