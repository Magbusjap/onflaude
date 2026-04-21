# OnFlaude — Screenshots

This folder contains screenshots used in the main [README.md](../../README.md).

## Conventions

- **Format:** PNG (not JPEG — admin UI has text and thin lines, JPEG compression ruins them)
- **Width:** 1400–1800px (minimum 1400, or the image looks blurry on Retina)
- **Browser:** Chrome or Firefox, no browser chrome (just the page content), **light OS theme**
- **Zoom:** 100% (Cmd/Ctrl+0 before capturing)
- **Window width:** 1440–1680px (not fullscreen on a 27" monitor — too much empty space)
- **File naming:** lowercase, hyphens, `.png` (e.g. `dashboard.png`, not `Dashboard.PNG`)

## Before capturing

1. Make sure demo content is seeded: at least 3 posts, 2 pages, 2 categories, 5–10 media files in a couple of folders
2. Log in as an admin user with display name like **"Mikhail Ankudinov"** (clean, real-looking — not "admin" or "test")
3. Clear browser console from errors (F12 → Clear → no red)
4. No visible test/placeholder data (no "lorem ipsum", no "test post 123"). If there is — edit to realistic titles like "Getting started with OnFlaude", "How the theme system works", etc.
5. Hide any personal/sensitive info: real email addresses, real phone numbers, internal IPs

---

## Shot list (required — used in README)

### 1. `dashboard.png` — Admin Dashboard

**URL:** `/<admin_path>/` (e.g. `/magbusjap`)

**Shows the "admin panel works today" evidence.** Must capture:

- ✅ Sidebar on the left, **expanded** (not collapsed)
- ✅ Top bar visible with: site favicon, Add New dropdown, global search, user menu
- ✅ Welcome Banner widget with time-of-day greeting ("Good morning, Mikhail" / "Good evening, ...")
- ✅ StatsOverview widget (posts/pages/users counts)
- ✅ Screen Options button/icon visible (top right corner of the dashboard)
- ✅ Optionally: Screen Options panel **open** to show the feature

**Frame:** the whole browser viewport, sidebar to right edge. No browser chrome.

### 2. `post-editor.png` — Post editor

**URL:** `/<admin_path>/posts/<id>/edit` (edit an existing post with some content)

**Shows the richest single-screen feature of the admin.** Must capture:

- ✅ Title field with a realistic post title
- ✅ TipTap editor toolbar fully visible
- ✅ Some formatted content in the body (headings, bold, a bullet list, a code block — to show TipTap's range)
- ✅ Right sidebar with metadata panel **open**: Featured Image (with an actual thumbnail loaded, not empty), Categories, Tags, Status
- ✅ If possible: the right sidebar toggle button in the top-right of the editor

**Frame:** full editor viewport. Right sidebar expanded.

### 3. `media-library.png` — Media Library

**URL:** `/<admin_path>/media`

**Shows the custom media core.** Must capture:

- ✅ Left panel with folder tree (at least 2 folders visible, one selected/highlighted)
- ✅ Main grid with at least 6–8 thumbnails (mix of images and at least one non-image file icon)
- ✅ Thumbnails should look clean — real photos or stock images, not obvious "test1.png"
- ✅ Actions visible on hover (or one item selected with actions bar on top)
- ✅ File count visible ("12 files in folder")

**Frame:** full library viewport. Avoid the upload modal for this shot — it's the browsing view.

### 4. `settings.png` — Settings page

**URL:** `/<admin_path>/settings`

**Shows the Options system UI (WordPress `wp_options` analog).** Must capture:

- ✅ Settings page with tabs visible (General / Appearance / Security, or whichever are implemented)
- ✅ The **General** tab open (most important — shows site title, description, admin path, active theme)
- ✅ Form fields populated with realistic values
- ✅ Save button visible at the bottom

**Frame:** full settings page. Sidebar in background is OK.

### 5. `frontend.png` — Public site with admin bar

**URL:** `/` (home page of the default theme)

**Shows that the default theme works AND that the admin bar injection works.** Must capture:

- ✅ You must be **logged in as admin** for the admin bar to appear
- ✅ **Admin bar at the very top** of the page (black/dark strip, ~32px tall)
- ✅ Admin bar content: "OnFlaude" logo on left, "New" dropdown, user menu on right
- ✅ Main page content below: hero section, recent posts grid or list, clean layout
- ✅ Page should look finished — not a "lorem ipsum" placeholder

**Frame:** top half of the homepage is enough — header + hero + first content block. Admin bar MUST be in frame (it's the point of this shot).

---

## Shot list (optional — nice to have)

Not used in the main README directly, but can be added later in an expanded screenshots gallery or used in blog posts / social media.

### `sidebar-collapsed.png`
Admin with the sidebar collapsed (icon-only mode if implemented, otherwise the slim version). Shows the collapse feature in action.

### `media-upload.png`
Media Library with the upload modal open, a file being dragged in.

### `post-create-from-topbar.png`
The "Add New" dropdown in the top bar showing its options (New Post, New Page, New Media…).

### `welcome-banner-closeup.png`
Close-up of the Dashboard welcome banner with Screen Options panel open to the side.

---

## How to submit screenshots

1. Capture the PNG following the conventions above
2. Name it exactly as in the shot list: `dashboard.png`, `post-editor.png`, etc.
3. Drop the file into this folder (`docs/screenshots/`)
4. Commit and push — the screenshot shows up in the README automatically (image path is already referenced)

## Updating a screenshot

Just replace the PNG with the same filename and commit. The README reference stays the same.

---

## Current status

| File | Status |
|---|---|
| `dashboard.png` | ✅ captured |
| `post-editor.png` | 🚧 not yet captured |
| `media-library.png` | ✅ captured |
| `settings.png` | 🚧 not yet captured |
| `frontend.png` | 🚧 not yet captured |

Until the PNGs land, the README will show broken image icons in the Screenshots section — that's expected.
