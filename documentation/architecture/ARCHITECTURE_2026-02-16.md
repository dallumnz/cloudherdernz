# CloudHerderNZ Architecture

**Date:** 2026-02-16
**Feature:** Posts Edit Refactor

## Implementation Plan

### 1. Flux UI Integration Overview
The goal is to replace standard Blade form elements in `resources/views/posts/edit.blade.php` with Flux UI components while preserving Livewire’s reactivity, validation, and submission logic.

#### Key Decisions
- **Component Mapping** – Map native inputs to Flux equivalents:
  - `<input type="text">` → `<flux:text-input>`
  - `<select>` → `<flux:select>`
  - `<textarea>` → `<flux:textarea>`
- **Validation Binding** – Use `wire:model` on Flux components; Livewire validation rules and error handling remain unchanged.
- **Error Display** – Wrap each field in a `<flux:error-message for="field">` to automatically show validation errors.
- **Styling Consistency** – Keep existing Tailwind utility classes where necessary. Flux components come with default styles that match the current design system.
- **Form Submission** – The form tag stays `wire:submit.prevent="updatePost"`; no controller changes needed.

### 2. File Changes
1. **resources/views/posts/edit.blade.php**
   - Add `@fluxui` directive at the top if not already present.
   - Replace each `<input>`, `<select>`, and `<textarea>` with corresponding Flux components, preserving `name`, `wire:model`, and any `required` or `placeholder` attributes.
   - Wrap fields in `<flux:form-group>` for consistent spacing.
2. **No new PHP files** – All changes are within the Blade view; Livewire component remains unchanged.

### 3. Asset Build
Run `npm run dev` (or `composer run dev`) to compile Tailwind and Flux assets after modifying the view.

### 4. Testing
- Verify that form fields bind correctly via Livewire.
- Check validation error messages appear within `<flux:error-message>` components.
- Ensure submission triggers the existing Livewire method (`updatePost`).

## Future Enhancements
- Extract a reusable Livewire component for post forms to use in both create and edit views.
- Leverage Flux UI’s built‑in validation helpers to reduce boilerplate.
