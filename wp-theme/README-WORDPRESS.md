# Ember & Oak — WordPress Theme

A custom WordPress theme for the fictional Ember & Oak wood-fired restaurant, matching the standalone site's ember design (dark charcoal-brown, ember orange, Fraunces + Inter, Bootstrap 5). This is the WordPress version of the portfolio project in this folder — it is **not** a client build.

## What's in the theme (`wp-theme/ember-oak/`)

| File | What it does |
| --- | --- |
| `style.css` | Theme header + full stylesheet (same ember design as the static site) |
| `functions.php` | Theme support, Primary nav menu, asset enqueues (Bootstrap 5 + Google Fonts via CDN), the Dish post type, the Reservation post type, and the booking-form handler |
| `header.php` / `footer.php` | Shared header (logo/nav) and footer |
| `front-page.php` | Home page: hero, about, feature cards, featured dishes, booking CTA |
| `page-menu.php` | **Template: Menu Page** — lists every Dish grouped by Dish Category, with tab switching |
| `page-reservations.php` | **Template: Reservations Page** — booking form + opening hours |
| `index.php` | Fallback template for posts, archives, and search |
| `assets/js/main.js` | Mobile nav, header state, scroll reveals, menu tabs, hours table, date bounds |

## How the menu works (editable content)

Menu items are a custom post type, **Dish**, with a **Dish Category** taxonomy — no hard-coded menu in the theme.

1. In the dashboard go to **Dishes → Add New** for each dish. The dish title is its name, the content is its description.
2. In the **Dish Details** box on the right, set the **price** and an optional **tag** (e.g. `Vegetarian`, `For two`).
3. Assign a category (e.g. Starters, Mains, Wood-Fired, Desserts, Drinks) under **Dishes → Dish Categories**. Create the categories first if they don't exist.
4. Dishes appear automatically on the Menu page, grouped under their category tabs.

## How reservations work

The form on the Reservations page posts to WordPress (`admin-post.php`) and is validated server-side in `functions.php` with the same rules as the standalone `reserve.php`: name (2+ chars), phone (10–15 digits), date (today through 60 days ahead), one of the listed time slots, party size 1–12. Valid bookings are saved as **Reservation** posts (Dashboard → Reservations) — they are not public. The page then shows a success or error notice.

## Install on any WordPress host

1. **Zip the theme:** zip the `ember-oak` folder (the folder itself, not its parent) — e.g. `ember-oak.zip`.
2. In the WordPress dashboard go to **Appearance → Themes → Add New → Upload Theme**, upload the zip, then **Activate**.
3. **Create the pages** (Pages → Add New):
   - **Home** — no template needed; set it as the front page below.
   - **Menu** — set **Template: Menu Page** in Page Attributes.
   - **Reservations** — set **Template: Reservations Page**.
4. **Set the front page:** Settings → Reading → "Your homepage displays" → *A static page* → Homepage: **Home**.
5. **Set the menu:** Appearance → Menus → create a menu with Home, Menu, Reservations (and any other pages) → assign it to **Primary Menu**. If you skip this, the theme shows a built-in fallback menu with the same three links.
6. **Add dishes** as described above, so the Menu page and the home page's featured dishes are populated. Until you do, the home page shows three sample dishes so the layout is visible.
7. Optional: set a logo under **Appearance → Customize → Site Identity**.

## Requirements

- WordPress 6.0+, PHP 7.4+
- No plugins required. Bootstrap 5 and the Google Fonts are loaded from CDN, so the site needs internet access to render them (same as the static version).
