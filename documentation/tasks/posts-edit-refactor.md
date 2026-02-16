# Task: Rebuild posts/edit.blade.php with Flux components

## Context
The file `resources/views/posts/edit.blade.php` was created for testing purposes and uses raw HTML/Tailwind styling instead of the Flux component library used elsewhere in the project.

## Reference Files
- `resources/views/categories/edit.blade.php` — simple form pattern
- `resources/views/admin/pages/edit.blade.php` — simple form pattern
- `resources/views/posts/edit.blade.php` — current (needs rewrite)

## Goal
Rewrite `posts/edit.blade.php` to match the pattern used in other edit views:
- Use simpler HTML/Tailwind classes (consistent with categories/pages)
- Remove custom dark mode handling if not needed
- Keep the form logic and all fields intact
- The complex type-specific fields (image/video/audio/newsletter) should remain but styled consistently

## Additional: Route Consistency Check
- Review all routes in the project for naming consistency
- Look for any anomalies or inconsistencies in route naming conventions
- Flag any routes that don't follow the established patterns

## Notes
- The form has many fields due to the polymorphic post types — that's fine
- Focus on matching the styling/structure pattern, not removing functionality
- Check if Flux components should be used; if so, apply them consistently
- For route review: check `routes/web.php` and any API route files
