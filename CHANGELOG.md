# Changelog

All notable changes to the Miss Bulgaria Voting plugin are documented in this file.

## [1.0.2] - 2026-10-02

### Improvements & Fixes:

- Individual Countdown Priority Override:
  - Enabled individual candidate countdown to take precedence over the Universal Counter.
  - Allows specific candidates to have their own closing deadline while global voting is active.
  - Updated backend meta box description to clarify the override behavior.

- Responsive Card Image & Layout Fixes:
  - Candidate photos now maintain their natural aspect ratio (`height: auto !important`) without cropping or stretching.
  - Removed restrictive fixed-height rules across mobile and tablet breakpoints.
  - Cleaned up duplicate media queries for candidate card containers.

## [1.0.1] - 2026-09-30

### New Features:

- Added Universal Voting Countdown system.
  - Admin can control a global voting closing date/time.
  - Universal countdown can be enabled/disabled from settings.
  - Individual candidate countdown remains available.
  - Added priority logic between universal and individual countdowns.

- Added configurable maximum votes per candidate.
  - Removed hard-coded vote limit.
  - Admin can set any vote limit from backend settings.
  - Empty value allows unlimited votes.

- Fixed duplicate Candidate Region filters.
  - Frontend now displays unique regions only.
  - Candidates from the same region appear under one filter.

- Improved automatic ranking system.
  - Candidate ranking is now fully vote-based.
  - Ranking is protected from theme ordering and third-party post order plugins.
  - Highest votes always appear first.

- Added candidate ID support in candidate shortcode.
  - Admin can display selected candidates only using IDs.

  Example:
  ```text
  [miss_bulgaria_candidates ids="101,103,106"]
  ```

- Added Candidate ID column in backend candidate management.
  - Easier candidate management and shortcode usage.

- Added Shortcode Reference section inside Miss Bulgaria settings.
  - Admins can view all available shortcodes and usage examples directly from backend.

### Bug Fixes:

- Fixed ranking/order conflicts.
- Improved candidate filtering reliability.
- Improved admin usability.

---

## [1.0.0] - 2026-09-28

- Initial release of Miss Bulgaria Voting platform.
