# HAWLAST WordPress theme

Classic theme for the HAWLAST blog. Matches the hawlast.com homepage design:
navy `#10204A`, orange `#E8590C`, warm tint `#FBF7F1`, self-hosted Inter.

## Performance notes

- No jQuery, no sliders, no icon fonts, no CSS framework.
- Critical CSS is inlined in `<head>` by `hawlast_head_critical()`;
  `style.css` loads with the `defer` strategy, so nothing render-blocking.
- One font file, `assets/fonts/inter-var-latin.woff2` (latin subset,
  variable weight), preloaded, `font-display: swap`.
- `assets/js/main.js` is deferred vanilla JS and only handles the mobile menu.
- Asset versions come from `filemtime()` via `hawlast_asset_version()`.

## SEO and analytics

`hawlast_meta_tags()` outputs canonical, Open Graph and Twitter Card tags, but
**only when no SEO plugin is active**, so tags are never duplicated. The same
applies to `hawlast_analytics()`, which stays silent when Site Kit, Exact
Metrica, MonsterInsights or AIOSEO's analytics module is already running.

## Menus

Two locations are registered: **primary** and **footer**. If the primary menu
has no items assigned, `hawlast_primary_menu_fallback()` prints the homepage
links so the header never looks broken.

## Tools

All read-only helpers, safe to run any time:

| Command | Purpose |
| --- | --- |
| `php tools/scan-mixed-content.php https://www.hawlast.com/blog` | Report non-https asset URLs in the rendered HTML. Prefix a local path with `@` to scan saved HTML. |
| `php tools/sample-url.php` | Print a permalink that has a featured image. |
| `php tools/make-screenshot.php` | Regenerate `screenshot.png`. Needs `HAWLAST_FONT` pointing at a TrueType Inter. |

## Deploying over HTTPS

See `DEPLOY-HTTPS.md`. The theme forces no redirect and edits no `.htaccess`.