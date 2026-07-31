<div align="center">

# ⭐ Reviewfic

### Testimonials, Customer Reviews, Google Reviews & WooCommerce Reviews for WordPress

[![Version](https://img.shields.io/badge/version-1.2.47-brightgreen.svg)](https://github.com/hasanet/reviewfic)
[![WordPress](https://img.shields.io/badge/WordPress-5.4%2B-blue.svg)](https://wordpress.org)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WP%206.9-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Collect, manage, and display customer reviews on any WordPress site. Pull in live reviews from **Google**, **Yelp**, **WooCommerce**, and **WordPress.org** — no API key needed for the last two — or connect Contact Form 7, WPForms, Fluent Forms, or Gravity Forms. Showcase everything with 10 display templates, sliders, pagination, and full design control. Zero coding required.

👉 **[Official Demo](https://themefic.com/reviewfic/)** &nbsp;|&nbsp; 👉 **[Get Support](https://portal.themefic.com/support/)** &nbsp;|&nbsp; 👉 **[Documentation](https://themefic.com/reviewfic/docs)** &nbsp;|&nbsp; 👉 **[Facebook Group](https://www.facebook.com/groups/themefic)**

[WordPress.org Plugin Page](https://wordpress.org/plugins/reviewfic) · [Support](https://portal.themefic.com/support/) · [Report a Bug](https://github.com/hasanet/reviewfic/issues)

</div>

---

## ✨ Features

| Feature | Description |
|---|---|
| ⭐ **Star Ratings** | 1–5 stars with half-star precision and a live visual preview in the admin |
| 👤 **Reviewer Details** | Name, Designation (job title), and Company — shown as "CEO · Acme Corp" |
| 🖼️ **Reviewer Avatars** | Circular profile photos via the media library, with a generated initials-avatar fallback for sources with no photo (e.g. WordPress.org) |
| 🏷️ **Review Source Badges** | Color-coded badges for Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon + any custom platform. Hide entirely with `show_source="no"` |
| 📂 **Review Sources Taxonomy** | Manage platforms from **Reviewfic → Review Sources** — add, rename, or delete anytime |
| 🗂️ **Review Categories** | Organise reviews into categories and filter per shortcode |
| 🎨 **10 Display Templates** | Classic, Quote, Minimal, Dark, Centered, Split, Glow, Score, Magazine, Neon Dark |
| 🎠 **Slider Mode** | Swipeable carousel with arrows, dots, autoplay, speed, loop, and pause-on-hover — works on every live source too |
| 📄 **Pagination** | Numbered pagination with configurable per-page count and smart ellipsis — works on every live source too |
| 🎨 **Design Options** | Per-shortcode color pickers, box shadow presets, border radius, and column gap — reusable across live sources via a saved config ID |
| ⚙️ **Shortcode Generator** | Visual builder — create named configs, get a permanent `[reviewfic id="X"]` shortcode that any live source can reuse |
| 📐 **Responsive Grid** | 1–4 column layout, collapses to single column on mobile automatically |
| 📝 **Review Collection Form** | `[reviewfic_form]` — let customers submit reviews directly from any page. Pending approval by default. |
| 🔗 **Contact Form 7 Integration** | Map CF7 fields to Reviewfic fields via a built-in tab in the CF7 editor. No coding needed. |
| 🔌 **WPForms / Fluent Forms / Gravity Forms** | Connect any form from these plugins via Reviewfic → Form Integrations. Per-form field mapping, source, and status. |
| 🔴 **Live Google Reviews** | `[reviewfic_google place_id="..."]` — fetch and display live Google Places reviews. Cached 12h. Detailed admin diagnostics on error. |
| 🟡 **Live Yelp Reviews** | `[reviewfic_yelp business_id="..."]` — fetch and display live Yelp Fusion reviews. Cached 12h. |
| 🟣 **Live WooCommerce Reviews** | `[reviewfic_woocommerce product_id="..."]` — showcase existing WC product reviews anywhere on the site. No API key needed. |
| 🔵 **Live WordPress.org Reviews** | `[reviewfic_wporg plugin="..."]` — pull your plugin or theme's live WordPress.org reviews. No API key needed. |
| ♻️ **One-Click Cache Refresh** | Clear all cached live review data (Google, Yelp, WordPress.org) instantly from the Live Reviews admin page |
| 📤 **Import / Export** | Export all reviews as CSV or JSON. Import from either format — new sources/categories created automatically. |
| 🛒 **WooCommerce Integration** | Post-purchase review emails, replace WC reviews tab with any Reviewfic template, auto-tag reviews by product |
| ✈️ **Tourfic Integration** | Post-booking review emails, replace the listing review section, auto-tag reviews by hotel/tour/apartment name |
| ⚡ **Built for Speed** | CSS/JS load on-demand only on pages that need them — never sitewide — and JS is split by feature (slider vs. form) |
| ❓ **Get Help** | Dedicated admin page linking to support, feature requests, and documentation |
| 🔌 **Our Plugins** | One-click install for other Themefic plugins directly from the WordPress admin |
| ♿ **Accessible Slider** | Touch swipe, keyboard arrow keys, and ARIA labels |
| 🔌 **Zero Dependencies** | No jQuery plugins, no external CDN calls on the frontend |

---

## 📦 Installation

**From WordPress admin (recommended)**

1. Go to **Plugins → Add New** and search for **Reviewfic**
2. Click **Install Now** → **Activate**

**Manual install**

1. Download the latest zip from [Releases](https://github.com/hasanet/reviewfic/releases)
2. Go to **Plugins → Add New → Upload Plugin**
3. Upload the zip and click **Activate Plugin**

---

## 🚀 Quick Start

### 1. Add a review

Go to **Reviewfic → Add New Review**. Fill in:

| Field | Description |
|---|---|
| Title | Review headline or the reviewer's key quote |
| Body | Full review text |
| Star Rating | 1 to 5, increments of 0.5 |
| Review Source | Platform the review came from (dropdown from your taxonomy) |
| Reviewer Name | The reviewer's full name |
| Designation | Job title or role (e.g. CEO, Marketing Manager) |
| Company | Reviewer's company name |
| Reviewer Photo | Upload via the WordPress media library |

### 2. Manage review sources

Go to **Reviewfic → Review Sources** to add, rename, or remove platforms. The following are added automatically on first activation:

`Google` · `Trustpilot` · `G2` · `Capterra` · `Facebook` · `Yelp` · `Amazon`

Any platform not in this list gets a neutral dark badge. Known platforms get their brand colors automatically.

### 3. Create a shortcode

Go to **Reviewfic → Shortcode Generator → Create New Shortcode**. Configure all options, save, and copy your permanent shortcode:

```
[reviewfic id="21"]
```

Paste it into any page, post, or widget. If you later change any option in the generator, every instance of that shortcode updates automatically — no need to edit your pages. The same config ID also works on the live Google, Yelp, WooCommerce, and WordPress.org shortcodes (`id="21"` on any of them), giving every live source the exact same template, slider, pagination, and design.

---

## 🎨 Templates

| # | Name | Layout |
|---|---|---|
| 1 | **Classic** | Source badge → stars → title → content → client row at bottom |
| 2 | **Quote** | Avatar + name at top → large italic quote → stars → source badge. Orange left border on warm background. |
| 3 | **Minimal** | No card border — thin accent top line only. Pure typography, editorial feel. |
| 4 | **Dark** | Dark (`#111827`) card, white text, gold stars, accent-colored avatar ring, subtle corner glow. |
| 5 | **Centered** | Everything center-aligned — large avatar at top, centered stars, centered italic quote. Ideal for a single featured testimonial. |
| 6 | **Split** | Two-panel card — coloured left strip (avatar, name, badge) paired with a white content panel (stars, title, content). |
| 7 | **Glow** | White card with a soft green glow border, large italic quote, avatar + name footer row. |
| 8 | **Score** | A circular star-score bubble floats in the top-right corner alongside badge, stars, title, and content. |
| 9 | **Magazine** | No card background — editorial pull-quote style with a large opening quote mark and a two-column byline. |
| 10 | **Neon Dark** | Deep dark card with a teal-to-purple gradient header strip and a stars + badge footer row. |

---

## 🎠 Slider Options

When slider mode is enabled, the following options can be configured per shortcode — and apply identically whether you're sliding through your own reviews or any live source (Google, Yelp, WooCommerce, WordPress.org):

| Option | Values | Default |
|---|---|---|
| Navigation Arrows | show / hide | show |
| Dot Indicators | show / hide | show |
| Autoplay | on / off | off |
| Autoplay Speed | milliseconds | 4000 |
| Infinite Loop | on / off | on |
| Pause on Hover | on / off | on |

Slider also supports **touch swipe** on mobile and **keyboard arrow keys** for accessibility.

---

## 🔧 Shortcode Reference

### Recommended — ID-based

```
[reviewfic id="21"]
```

Create a config in the Shortcode Generator and use the ID. Edit options anytime without touching the shortcode. The same ID works on every live shortcode below too.

### Collect reviews — submission form

```
[reviewfic_form]
```

Renders a styled review submission form on any page. Customers fill in their name, rating, review, designation, company, and platform source. Submissions are saved as pending by default.

| Attribute | Default | Description |
|---|---|---|
| `require_approval` | `yes` | Set to `no` to publish submissions immediately |
| `success_message` | `"Thank you! …"` | Custom text shown after a successful submission |
| `show_source` | `yes` | Show or hide the platform/source dropdown |
| `redirect` | _(none)_ | URL to redirect to after a successful submission |

### Live reviews — Google, Yelp, WooCommerce, WordPress.org

```
[reviewfic_google place_id="ChIJ..." id="12" max="5"]
[reviewfic_yelp business_id="..." id="12" max="3"]
[reviewfic_woocommerce product_id="123" id="12" max="10"]
[reviewfic_wporg plugin="your-plugin-slug" id="12" max="5"]
```

All four accept `id` (saved Shortcode Generator config), `template` (1–10), `columns`, `slider` + its 6 sub-options, `pagination` + `per_page`, `show_avatar`, and `show_source`. See **Design Options** below for the full attribute list available via `id`.

### Examples

```
[reviewfic id="21"]

[reviewfic_form]

[reviewfic_form require_approval="no" redirect="https://example.com/thank-you"]

[reviewfic_form show_source="no" success_message="Thanks! We'll review your submission shortly."]

[reviewfic_google place_id="ChIJN1t_tDeuEmsRUsoyG83frY4" columns="3" template="1"]

[reviewfic_yelp business_id="gary-danko-san-francisco" slider="yes" template="2"]

[reviewfic_woocommerce product_id="123" template="8" show_source="no"]

[reviewfic_wporg plugin="contact-form-7" template="9"]
```

---

## 🎨 Design Options

Each shortcode configuration supports independent visual overrides — no CSS required:

| Option | Description |
|---|---|
| **Card Background** | Fill color for each review card |
| **Text Color** | Body/content text color |
| **Reviewer Name Color** | Color of the reviewer's name |
| **Designation & Company Color** | Color of the "CEO · Acme Corp" sub-line |
| **Star Color** | Color of the star rating icons |
| **Accent Color** | Template 2 left border · Template 3 top bar · Source badge borders |
| **Card Border Color** | Color of the card's outer border |
| **Box Shadow** | None / Subtle / Medium / Strong |
| **Border Radius** | 0–24px slider |
| **Column Gap** | 0–60px slider |

All values are applied as CSS custom properties on the shortcode wrapper, so multiple shortcodes on the same page are fully independent. Save these settings once in a Shortcode Generator config, then reuse the same `id` across your own reviews and every live source.

---

## 📤 Import / Export

Go to **Reviewfic → Import / Export** to bulk-manage reviews.

**Export** — Download all reviews as:
- **CSV** — opens in Excel or Google Sheets
- **JSON** — structured data for developers or cross-site migration

**Import** — Upload a `.csv` or `.json` file to bulk-create reviews. New sources and categories are created automatically if they don't exist. Rows with both title and content empty are skipped. New reviews default to **pending** unless the file specifies otherwise.

---

## 🛒 WooCommerce Integration

Go to **Reviewfic → WooCommerce** to configure four WooCommerce-specific features.

### 1. Post-Purchase Review Request Email

| Setting | Options |
|---|---|
| Enable | On / Off |
| Delay | Immediately, 1, 2, 3, 5, 7, or 14 days after order completion |
| Landing Page | Any published WordPress page (place `[reviewfic_form]` on it) |
| Email Subject | Customisable with `{customer_name}`, `{order_id}`, `{site_name}` |
| Email Body | Customisable — a styled CTA button is added automatically |

The email is scheduled via WP-Cron. For single-product orders, the review link includes the product ID so reviews are auto-tagged.

### 2. Replace WooCommerce Reviews Tab

When enabled, replaces WooCommerce's default product reviews tab with Reviewfic's templates. Choose template, columns, slider, and avatar/badge visibility directly — or select a full Shortcode Generator config for complete control, including pagination and design.

### 3. Auto-Tag Reviews by Product

When a customer arrives via the review request email and submits a review via `[reviewfic_form]`, Reviewfic automatically creates or assigns a Reviewfic **Category** named after the product and tags the review. This lets you filter product-specific reviews in any shortcode using `category="product-name"`.

### 4. Native On-Site Reviews Shortcode

```
[reviewfic_woocommerce product_id="123"]
```

Display existing WooCommerce reviews anywhere — independent of the tab replacement, no API key needed.

---

## ✈️ Tourfic Integration

Go to **Reviewfic → Tourfic** to configure the same three core features for hotel, tour, apartment, and car rental bookings.

| Feature | Notes |
|---|---|
| Post-Booking Review Request Email | Same configurable delay and merge-tag email template as WooCommerce |
| Replace Tourfic Review Section | Works for hotel, tour, and apartment listings. Not available for car rentals — Tourfic embeds that review block directly into its template with no hook to intercept. |
| Auto-Tag Reviews by Service | Tags reviews with a prefixed category, e.g. `Hotel: Grand Hyatt`, `Tour: Desert Safari` |

Both the tab/section replacement and the request email support the same "Saved Display Config" selector as WooCommerce.

---

## 🔴 Live Reviews — Google, Yelp, WooCommerce & WordPress.org

Pull reviews directly from external platforms — or from data you already have — and display them using Reviewfic's templates.

### Setup

Go to **Reviewfic → Live Reviews**:

| Source | API Key Needed | Free Tier / Notes |
|---|---|---|
| Google | [Places API key](https://developers.google.com/maps/documentation/places/web-service/get-api-key) | Requires billing enabled on the Cloud project; $200/month credit covers most sites |
| Yelp | [Fusion API key](https://www.yelp.com/developers/documentation/v3/authentication) | Up to 3 review excerpts on the free Base plan |
| WooCommerce | **None** | Reads your store's existing reviews directly |
| WordPress.org | **None** | Reads the public review RSS feed for any plugin/theme slug |

All four sources are cached for **12 hours**. Use the **Clear Live Review Cache** button on the same page to force a fresh fetch immediately.

### Shortcodes

```
[reviewfic_google place_id="ChIJN1t_tDeuEmsRUsoyG83frY4" columns="3" template="1" max="5"]

[reviewfic_yelp business_id="gary-danko-san-francisco" columns="3" template="1" max="3"]

[reviewfic_woocommerce product_id="123" columns="3" template="1" max="10"]

[reviewfic_wporg plugin="contact-form-7" columns="3" template="1" max="5"]
```

All four support all 10 templates, full slider sub-options, pagination, columns, `show_avatar`, `show_source`, and the full design color system — either directly via attributes, or all at once via a saved config `id`.

**Finding your IDs:**
- Google Place ID → [Place ID Finder](https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder) — must start with `ChIJ`, not be a Maps URL or business name
- Yelp Business ID → the slug in your Yelp URL (e.g. `yelp.com/biz/gary-danko-san-francisco`)
- WordPress.org plugin slug → the slug in the plugin's URL (e.g. `wordpress.org/plugins/contact-form-7` → `contact-form-7`)

### Troubleshooting Google/Yelp errors

Admins see detailed diagnostics instead of a generic error:

| Error | Likely Cause |
|---|---|
| `REQUEST_DENIED` | Invalid key, missing API permission, or an HTTP-referrer key restriction (these requests come from your server, not a browser) |
| `OVER_QUERY_LIMIT` | Quota exceeded, or billing not enabled on the Google Cloud project |
| `INVALID_REQUEST` / `NOT_FOUND` | Malformed or incorrect Place ID |
| `OK` with zero reviews | Google's own limitation — Place Details only returns up to 5 algorithmically-chosen reviews, sometimes none |

---

## 🔌 Form Integrations (WPForms / Fluent Forms / Gravity Forms)

Go to **Reviewfic → Form Integrations** to connect forms from these plugins. Each tab lists all forms from the installed plugin with:

- Enable toggle per form
- Review status (pending / published)  
- Review source dropdown (from your taxonomy)
- Field mapping: name, designation, company, rating, title, content, photo

Connected forms are automatically styled to match Reviewfic's form design on the frontend.

---

## 📝 Collecting Reviews from Customers

### Built-in Form

Place `[reviewfic_form]` on any page or post. A styled form is rendered with fields for name, designation, company, star rating, review title, review content, platform source, and a **drag-and-drop photo upload** with live circular preview. Submissions are saved as **pending** by default — approve them from **Reviewfic → All Reviews**.

### Contact Form 7 Integration

If you prefer to use an existing Contact Form 7 form:

1. Install and activate Contact Form 7
2. Open any CF7 form in the editor
3. Click the **Reviewfic** tab
4. Enable the integration and choose the review status (pending or published)
5. Map your CF7 field names to Reviewfic fields:

| Reviewfic Field | CF7 Field Name (example) |
|---|---|
| Reviewer Name | `your-name` |
| Star Rating (1–5) | `your-rating` |
| Review Content | `your-message` |
| Designation | `your-job-title` |
| Company | `your-company` |
| Review Title | `your-review-title` |
| Review Source (slug) | `your-platform` |

Every submission of that CF7 form will automatically create a review in Reviewfic using the mapped values.

---

## 🏷️ Source Badge Colors

Known platforms automatically receive their brand colors:

| Platform | Color |
|---|---|
| Google | `#4285F4` Blue |
| Trustpilot | `#00B67A` Green |
| G2 | `#FF492C` Red |
| Capterra | `#FF3E72` Pink |
| Facebook | `#1877F2` Blue |
| Yelp | `#FF1A1A` Red |
| Amazon | `#FF9900` Orange |
| Any custom platform | `#444` Dark grey |

Hide the badge entirely on any shortcode with `show_source="no"` — but keep it visible for live Google/Yelp content, since both platforms require their attribution to stay visible per their display terms.

---

## ⚡ Built for Speed

Reviewfic never adds weight to pages that don't need it:

- CSS and JavaScript are **registered, not auto-enqueued** — the actual load only happens from inside the code path that renders a Reviewfic feature (the shortcode, the live-reviews renderer, or the form shortcode)
- JavaScript is **split by feature** — `reviewfic-slider.js` and `reviewfic-form.js` load independently, so a static grid never loads form code and a form never loads slider code
- A page with zero Reviewfic content loads **zero** Reviewfic assets

---

## 📁 File Structure

```
reviewfic/
├── admin/
│   ├── assets/img/              # Star rating SVG icons
│   ├── templates/
│   │   └── tourfic-reviews.php  # Tourfic review section replacement template
│   ├── admin-brand.php          # Admin branding (header bar, button styles)
│   ├── meta-boxes.php           # Review Details meta box (stars, source, reviewer)
│   ├── post-types-taxonomy.php  # CPT + taxonomies registration
│   ├── shortcode-config.php     # Shortcode Generator CPT + meta box + save
│   ├── shortcode.php            # [reviewfic] shortcode — all 10 templates
│   ├── review-form.php          # [reviewfic_form] shortcode + CF7 integration
│   ├── import-export.php        # Import / Export admin page
│   ├── form-integrations.php    # WPForms / Fluent Forms / Gravity Forms
│   ├── live-reviews.php         # Google, Yelp, WooCommerce, WordPress.org live reviews
│   ├── woocommerce.php          # WooCommerce email, tab replacement, auto-tag
│   ├── tourfic.php              # Tourfic email, section replacement, auto-tag
│   └── extra-pages.php          # Get Help + Our Plugins admin pages
├── assets/
│   ├── css/
│   │   ├── reviewfic.css        # Frontend styles (10 templates, slider, pagination, form)
│   │   └── reviewfic-admin.css  # Admin styles (meta box, shortcode generator)
│   └── js/
│       ├── reviewfic.js          # Admin shortcode generator JS
│       ├── reviewfic-slider.js   # Frontend carousel logic only
│       └── reviewfic-form.js     # Star picker, photo upload, connected-form styling
├── reviewfic.php                # Plugin bootstrap + on-demand asset loading
├── README.md                    # This file
└── readme.txt                   # WordPress.org readme
```

---

## 🛠️ Development & Deploy Workflow

This repo uses a lightweight deploy script. After cloning, move `deploy.sh` and `watch.sh` into your dev folder:

```bash
# One-time: make scripts executable
chmod +x deploy.sh watch.sh

# Start the background file watcher (auto-deploys new zips)
./watch.sh --bg

# Manual deploy from a downloaded zip
./deploy.sh ~/Downloads/reviewfic_X_X_X.zip
```

The deploy script syncs files to Local by Flywheel and pushes to the `new-updates` branch on GitHub. GitHub Actions automatically deploys to the live site via FTP.

---

## 📋 Changelog

See [readme.txt](readme.txt) for the full changelog.

**Latest — v1.2.47**
- Improvement: Frontend CSS/JS no longer load sitewide — only on pages where a review, slider, or form actually renders
- Improvement: Frontend JS split into `reviewfic-slider.js` and `reviewfic-form.js`, each loaded independently
- Fix: Removed a dead, unreferenced duplicate JS file

**v1.2.46**
- Fix: WordPress.org review cache could never be cleared
- New: Clear Live Review Cache button on the Live Reviews page

**v1.2.45**
- Fix: WordPress.org's review feed metadata ("Replies: 0 Rating: 5 stars") leaking into review content — now stripped
- New: Generated colored-initials avatar fallback for sources with no photo

**v1.2.44**
- New: `show_source` attribute to hide the platform/source badge on any shortcode

**v1.2.43**
- New: Full feature parity for every live review shortcode — all 10 templates, full slider sub-options, pagination, design colors via `id="X"`
- New: Saved Display Config selector on WooCommerce and Tourfic settings pages
- Fix: WooCommerce reviews tab silently ignored the saved Columns setting

**v1.2.42**
- Improvement: Detailed Google Places API error diagnostics for admins

**v1.2.39**
- New: `[reviewfic_woocommerce]` — native on-site WooCommerce reviews, no API key
- New: `[reviewfic_wporg]` — live WordPress.org plugin reviews, no API key
- New: Display Options added to WooCommerce and Tourfic settings pages

**v1.2.38**
- New: 5 additional display templates — Split, Glow, Score, Magazine, Neon Dark. 10 templates total.

**v1.2.37**
- New: Tourfic integration — post-booking email, review section replacement, auto-tag by service

**v1.2.28**
- New: WooCommerce integration — post-purchase review request email
- New: WooCommerce integration — replace WC reviews tab with Reviewfic templates
- New: WooCommerce integration — auto-tag reviews by product name

**v1.2.27**
- Fix: Our Plugins page uses real WP.org plugin icons with letter-badge fallback
- New: `[reviewfic_google place_id="..."]` — live Google Places reviews (up to 5)
- New: `[reviewfic_yelp business_id="..."]` — live Yelp Fusion reviews (up to 3)
- New: Reviewfic → Live Reviews page for API key management and shortcode docs
- Live reviews support all 5 templates, slider, columns, and design options

**v1.2.26**
- New: Reviewfic → Get Help page (Support, Feature Request, Docs)
- New: Reviewfic → Our Plugins page with one-click install from WordPress.org

**v1.2.25**
- New: WPForms, Fluent Forms, and Gravity Forms integrations
- New: Form Integrations admin page (Reviewfic → Form Integrations) with tabbed UI for all three plugins
- Per-form: enable toggle, review status, source dropdown, full field mapping
- Frontend: connected forms automatically styled to match Reviewfic form design

**v1.2.24**
- Improvement: CF7 Review Source is now a taxonomy dropdown, not a manual slug field
- New: CF7 photo upload with drag-and-drop (same design as built-in form)
- New: CF7 connected forms automatically styled to match Reviewfic form (scoped only to connected forms)

**v1.2.23**
- Fix: Columns work correctly in slider mode — N columns = N cards visible per page
- Improvement: Slider JS rewritten to support page-by-page multi-column navigation

**v1.2.22**
- Fix: Slider layout broken from slide 2 onward (legacy CSS margin rule causing overflow)
- Fix: Slider items now correctly constrained to 100% width with flex-shrink:0
- Improvement: Slider/pagination are now mutually exclusive in the Shortcode Generator UI
- Improvement: Column toggle greyed out when slider is active

**v1.2.21**
- New: Reviewer Name Color, Designation & Company Color, Card Border Color, and Box Shadow options in the Design panel

**v1.2.20**
- Fix: Import/Export CSS not loading (wrong admin hook name)
- Fix: Import/Export page redesigned — clean cards, Dashicons, proper format picker
- Fix: Shortcode Generator template picker now correctly restores selection when editing

**v1.2.19**
- New: Import / Export page — export reviews as CSV or JSON, import from either format

**v1.2.18**
- Improvement: Modern drag-and-drop photo uploader on review form with circular preview and remove button

**v1.2.17**
- New: Photo upload field on `[reviewfic_form]` — reviewers upload their own profile photo with a live circular preview

**v1.2.16**
- New: `[reviewfic_form]` shortcode — built-in customer review submission form
- New: Contact Form 7 Integration — map CF7 fields to Reviewfic via the CF7 editor
- Improvement: Cleaned up readme; removed legacy shortcode documentation

---

## 🤝 Support

- **WordPress.org forums** — for general support questions
- **[Themefic Support Portal](https://portal.themefic.com/support/)** — for priority support
- **[GitHub Issues](https://github.com/hasanet/reviewfic/issues)** — for bug reports and feature requests

---

## 📄 License

Reviewfic is open-source software licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

<div align="center">
Made with ❤️ by <a href="https://themefic.com">Themefic</a>
</div>
