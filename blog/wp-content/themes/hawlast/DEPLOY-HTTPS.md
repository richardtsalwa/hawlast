# HTTPS deployment note

These are server settings. The theme deliberately does not force any redirect,
does not touch `.htaccess`, and does not depend on them, so nothing here is
required for the theme to function. Do these in Settings before going live.

## 1. WordPress addresses

**Settings → General**, both fields:

- **WordPress Address (URL)**: `https://www.hawlast.com/blog`
- **Site Address (URL)**: `https://www.hawlast.com/blog`

Both must match exactly, including the trailing absence of a slash. Set them
**before** switching to the new theme so the theme never emits an `http://`
asset URL. The theme builds every URL from `home_url()` and
`get_template_directory_uri()`, so these two values decide the scheme.

If the values in `wp-config.php` (`WP_HOME` / `WP_SITEURL`) are defined, they
override the database settings and must be updated too.

## 2. Force HTTPS in cPanel

In cPanel, either:

- **SSL/TLS Status** → run **Force HTTPS Redirect** for the `hawlast.com`
  domain, or
- add a redirect in **Domains → Redirects**:
  - Type: Permanent (301)
  - From: `https://www.hawlast.com/blog`
  - To: `https://www.hawlast.com/blog`

Make sure a valid certificate is installed and AutoSSL is on for
`hawlast.com` and `www.hawlast.com` first, otherwise visitors hit a warning
before the redirect can help.

## 3. Mixed content check after launch

Once the above are set, run the bundled scanner against the live site:

```
"d:\xampp\php\php.exe" wp-content\themes\hawlast\tools\scan-mixed-content.php https://www.hawlast.com/blog
```

Then, in a browser:

1. Open `https://www.hawlast.com/blog` in a **hard reload** (Ctrl+Shift+R).
2. Open DevTools → **Console**. Any mixed-content warning appears here.
3. DevTools → **Network**, reload, and filter for the **Img**, **CSS** and
   **Font** columns. Any `http://` entry is a problem.
4. Repeat on a single post, an archive and a search results page.
5. Check `view-source:https://www.hawlast.com/blog` and confirm no `http://`
   remains except XML namespace strings (`xmlns`, `schema.org`, `w3.org`).

## 4. Third-party scripts that can introduce mixed content

The theme itself adds no third-party requests. The blog already has plugins that
do, and they are the most likely source of mixed-content warnings after launch:

- **AddThis** (`s7.addthis.com`) and the Quantcast tag injected by AddThis
  (`pixel.quantserve.com`, a protocol-relative URL).
- **Contact Form 7** and **Mailchimp for WP** assets.

If the Console flags them, deactivate or update those plugins. That is outside
the scope of this theme, so it was not changed here.

## 5. Analytics note

The homepage uses a Universal Analytics property, `UA-16346251-1`. Universal
Analytics stopped processing data in July 2023, so that tag reports nothing.
The theme copies it exactly as specified and switches to `gtag.js` the moment a
GA4 measurement ID is provided:

```php
// wp-config.php
define( 'HAWLAST_GA4_ID', 'G-XXXXXXXXXX' );
```

or with the `hawlast_measurement_id` filter in a small mu-plugin. If Site Kit or
another analytics plugin is active, the theme prints nothing at all, so visits
are never counted twice.