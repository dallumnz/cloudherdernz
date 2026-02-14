# Task: Add Search Functionality to Frontend

**Project:** cloudherdernz  
**Created:** 2026-02-15  
**Status:** Pending

---

## Overview

Add frontend search functionality to CloudHerder. The backend search is already implemented (20 tests passing). This task is for the UI/frontend implementation.

---

## Requirements

1. **Search Bar** — Add a search input to the header/navigation
2. **Search Results** — Display results in a dedicated search results page
3. **Real-time Search** — Use Livewire for instant results as user types
4. **Post Types** — Filter search by post type (Image, Video, Audio, Newsletter)
5. **Responsive** — Work on mobile and desktop

---

## Current State

- **Backend:** Search feature implemented with Scout database driver
- **Tests:** 20 tests passing
- **Models:** Posts are searchable (title, content, slug)
- **Missing:** Frontend UI components

---

## Deliverables

- [ ] Search input component (Livewire)
- [ ] Search results dropdown/modal
- [ ] Integration with existing search backend
- [ ] Post type filtering
- [ ] Mobile-responsive design
- [ ] Tests for new components

---

## Notes

- Use existing Livewire patterns from the project
- Follow Tailwind CSS conventions
- Check existing blade components for style consistency
- Search should work with polymorphic posts (ImagePost, VideoPost, AudioPost, Newsletter)

---

## Project Path

```
/home/dallum/projects/cloudherdernz/
```
