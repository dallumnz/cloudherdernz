# Test Prompt: Analytics Capture Middleware

**Project:** `/home/dallum/projects/cloudherder.nz`

**Feature:** Analytics Capture Middleware

**Requirements:**
- Create a Laravel middleware that captures page/view analytics
- Track: page URL, user ID (if authenticated), IP address, user agent, referrer
- Store in `analytics_events` table
- Exclude admin routes from tracking
- Provide accessor method to retrieve page views

**Deliverables:**
- Migration: `create_analytics_events_table.php`
- Model: `AnalyticsEvent.php`
- Middleware: `CaptureAnalytics.php`
- Service: `AnalyticsService.php` with helper methods
- Register middleware in `Kernel.php`

**Run:**
- Migrations must pass
- Tests must pass

---

## Dev-Manager Spawn Prompt

When ready to test, spawn @fullstack-dev with:

```yaml
agentId: fullstack-dev
label: Analytics Middleware
task: |
  Implement analytics capture middleware for CloudHerder.nz
  
  Project: /home/dallum/projects/cloudherder.nz
  
  Requirements:
  - Create Laravel middleware: CaptureAnalytics
  - Track: page URL, user_id, IP, user_agent, referrer
  - Store in analytics_events table with UUID primary key
  - Exclude /admin/* routes from tracking
  - Create AnalyticsService with:
    - getPageViews($url, $days = 30)
    - getTopPages($limit = 10)
  - Create model: AnalyticsEvent
  - Create migration: create_analytics_events_table
  
  Expected Files:
  - database/migrations/2026_02_13_xxxxxx_create_analytics_events_table.php
  - app/Models/AnalyticsEvent.php
  - app/Http/Middleware/CaptureAnalytics.php
  - app/Services/AnalyticsService.php
  - tests/Feature/AnalyticsTest.php
  
  Current State:
  - Fresh migration to create analytics_events table
  - Routes exist in routes/web.php and routes/api.php
  
  Deliverables:
  - Migration runs successfully
  - Tests pass
  - Return to Dev-Manager for Code-Review
```

---

## After Fullstack-Dev Returns

Dev-Manager should:

1. Spawn @code-reviewer with:
```yaml
agentId: code-reviewer
label: Analytics Review
task: |
  Review analytics middleware implementation
  
  Project: /home/dallum/projects/cloudherder.nz
  
  Files Created:
  - Migration, Model, Middleware, Service, Tests
  
  Focus: Security and data privacy
  
  Return: List of issues found (if any).
```

2. After Code-Reviewer passes, run:
```bash
bash /home/dallum/projects/agent-agency/.opencode/scripts/version-architecture.sh
```

3. Generate handoff:
```bash
python /home/dallum/projects/agent-agency/.opencode/scripts/handoff.py generate \
    --path /home/dallum/projects/cloudherder.nz \
    --task "Analytics Capture Middleware" \
    --completed "CaptureAnalytics middleware" "AnalyticsEvent model" "AnalyticsService" "Migration" "Tests" \
    --next-steps "Register middleware in Kernel.php" "Add to routes"
```
