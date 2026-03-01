# Markdown Implementation Task

**Project:** CloudHerder NZ  
**Priority:** High  
**Assigned:** Agent Agency (via Software Factory)

---

## Goal

Implement markdown support for the CloudHerder admin dashboard and public frontend.

## Requirements

### Admin Dashboard (Writing)
- Integrate EasyMDE or similar markdown editor in the admin post editor
- The editor should be a Livewire component usable in the admin
- Editor must support:
  - Live preview
  - Toolbar with common formatting (bold, italic, headings, lists, links, code)
  - Proper handling of paste events for images (optional stretch goal)

### Public Frontend (Rendering)
- Use Laravel's built-in `Str::markdown()` to render markdown content
- Ensure markdown is properly sanitized (XSS protection)
- Style rendered markdown with Tailwind Typography (`prose` classes)
- Support light/dark mode for rendered content

### Technical Details
- Post content is stored as raw markdown in the database
- Create an accessor on the Post model (e.g., `contentHtml()`) that returns rendered HTML
- All content partials should use the accessor, not raw content
- Ensure proper escaping/sanitization

## Files to Modify
- `app/Livewire/` - Create markdown editor component
- `app/Models/Post.php` - Add contentHtml accessor
- `resources/views/partials/content/*.blade.php` - Update to use accessor
- `resources/views/livewire/` - Add editor UI
- `resources/css/app.css` - Ensure Tailwind Typography is configured

## Testing
- Unit tests for the contentHtml accessor (XSS prevention, markdown rendering)
- Integration tests for the editor component
- Frontend tests verifying rendered output

## Success Criteria
1. Admin can write posts using a markdown editor
2. Public frontend correctly renders markdown content
3. XSS vulnerabilities are prevented
4. Light/dark mode works for rendered content
5. All tests pass

---

*Created: 2026-03-01*
