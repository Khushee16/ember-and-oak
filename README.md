# Ember & Oak — Wood-Fired Kitchen

A multi-page landing site for a **fictional** wood-fired restaurant in Portland, Oregon. Dark, ember-toned design with a full interactive menu, opening hours, and a reservation flow with a real PHP backend option and a custom WordPress theme.

**This is a self-directed portfolio piece, not client work.** Ember & Oak is an invented restaurant; there are no real customers, testimonials, or business stats here.

## Pages

| Page | Contents |
| --- | --- |
| `index.html` | Home — hero, about the hearth, feature cards, featured dishes, booking CTA |
| `menu.html` | Full menu by category (Starters, Mains, Wood-Fired, Desserts, Drinks), tab-filtered from a JS data object |
| `reservations.html` | Reservation form + opening-hours table (highlights today) |

All three pages share `style.css` and `script.js`. `index.html` stays the home page so the existing live demo URL keeps working.

## Features

- Sticky header with mobile hamburger menu and scroll shadow
- Full-screen hero with layered ember gradient, animated scroll hint, and quick facts
- Scroll-reveal animations (IntersectionObserver, disabled under `prefers-reduced-motion`)
- Interactive menu tabs with hover states and entrance animation
- Reservation form with client-side validation: name, phone, date (no past dates, 60-day window), time slots, party size 1–12, plus a confirmation state
- Opening-hours table that highlights today's row automatically

## Stack

HTML, CSS (custom properties, grid/flexbox) on top of **Bootstrap 5** (CDN), vanilla **JavaScript** (no build step), **PHP** for the reservation backend, and a custom **WordPress theme**. Hero and dish photos are placeholders from picsum.photos.

## Reservation backend (`reserve.php`)

`reserve.php` is a plain, dependency-free PHP script (PHP 7.4+, no framework, no database). It accepts the form's JSON POST, validates name, phone, date (today through 60 days ahead, no past dates), time slot, and party size (1–12), appends the booking to `data/reservations.json`, and returns a JSON confirmation. `data/.htaccess` blocks direct web access to the stored bookings on Apache hosts.

**Important — hosting note:** the PHP backend runs on a PHP host, **not** on GitHub Pages. GitHub Pages is static hosting with no PHP, so when the form is submitted there (or from `file://`), the `fetch()` to `reserve.php` fails and the form shows a clearly-labelled demo success state — *"Demo mode — no backend connected"* — instead of pretending a real booking was made. The same note is in an HTML comment at the top of `reservations.html`.

## WordPress theme (`wp-theme/ember-oak/`)

A complete custom theme matching the site's ember design. See [`wp-theme/README-WORDPRESS.md`](wp-theme/README-WORDPRESS.md) for install steps.

- `style.css` (theme header + stylesheet), `functions.php` (theme support, nav menu, enqueues, post types, form handler), `header.php`, `footer.php`, `index.php`, `front-page.php`, `page-menu.php`, `page-reservations.php`, `assets/js/main.js`
- Menu items are editable content: a **Dish** custom post type with a **Dish Category** taxonomy (price + optional tag per dish)
- Reservations are handled in WordPress and stored as private **Reservation** posts

## Run locally

**Static site (front-end demo, no backend):**

```bash
python3 -m http.server 8000
# open http://localhost:8000 — reservations will show the demo success state
```

**With the PHP backend** (requires PHP 7.4+):

```bash
php -S localhost:8000
# open http://localhost:8000/reservations.html — bookings are saved to data/reservations.json
```

Or just open `index.html` directly in a browser (the `file://` demo state applies to reservations).

## Live demo

https://khushee16.github.io/ember-and-oak/

The live demo is the static site on GitHub Pages: home, menu, and reservations pages all work, and the reservation form shows the labelled demo success state (no backend on Pages).
