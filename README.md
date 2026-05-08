<div align="center">

# ⭐ Reviewfic

### Testimonial Slider, Testimonial Grid & Customer Reviews for WordPress

[![Version](https://img.shields.io/badge/version-1.2.27-brightgreen.svg)](https://github.com/hasanet/reviewfic)
[![WordPress](https://img.shields.io/badge/WordPress-5.4%2B-blue.svg)](https://wordpress.org)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WP%206.9-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Collect, manage, and display customer reviews on any WordPress site — star ratings, reviewer avatars, source badges, sliders, pagination, 5 templates, and a visual shortcode builder. Zero coding required.

👉 **[Official Demo](https://themefic.com/reviewfic/)** &nbsp;|&nbsp; 👉 **[Get Support](https://portal.themefic.com/support/)** &nbsp;|&nbsp; 👉 **[Documentation](https://themefic.com/reviewfic/docs)** &nbsp;|&nbsp; 👉 **[Facebook Group](https://www.facebook.com/groups/themefic)**

[WordPress.org Plugin Page](https://wordpress.org/plugins/reviewfic) · [Support](https://portal.themefic.com/support/) · [Report a Bug](https://github.com/hasanet/reviewfic/issues)

</div>

---

## ✨ Features

| Feature | Description |
|---|---|
| ⭐ **Star Ratings** | 1–5 stars with half-star precision and a live visual preview in the admin |
| 👤 **Reviewer Details** | Name, Designation (job title), and Company — shown as "CEO · Acme Corp" |
| 🖼️ **Reviewer Avatars** | Circular profile photos uploaded via the WordPress media library |
| 🏷️ **Review Source Badges** | Color-coded badges for Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon + any custom platform |
| 📂 **Review Sources Taxonomy** | Manage platforms from **Reviewfic → Review Sources** — add, rename, or delete anytime |
| 🗂️ **Review Categories** | Organise reviews into categories and filter per shortcode |
| 🎨 **5 Display Templates** | Classic, Quote, Minimal, Dark, Centered — chosen per shortcode config |
| 🎠 **Slider Mode** | Swipeable carousel with arrows, dots, autoplay, speed, loop, and pause-on-hover |
| 📄 **Pagination** | Numbered pagination with configurable per-page count and smart ellipsis |
| 🎨 **Design Options** | Per-shortcode color pickers (background, text, stars, accent) and border radius |
| ⚙️ **Shortcode Generator** | Visual builder — create named configs, get a permanent `[reviewfic id="X"]` shortcode |
| 📐 **Responsive Grid** | 1–4 column layout, collapses to single column on mobile automatically |
| 📝 **Review Collection Form** | `[reviewfic_form]` — let customers submit reviews directly from any page. Pending approval by default. |
| 🔗 **Contact Form 7 Integration** | Map CF7 fields to Reviewfic fields via a built-in tab in the CF7 editor. No coding needed. |
| 🔌 **WPForms / Fluent Forms / Gravity Forms** | Connect any form from these plugins via Reviewfic → Form Integrations. Per-form field mapping, source, and status. |
| 🔴 **Live Google Reviews** | `[reviewfic_google place_id="..."]` — fetch and display live Google Places reviews. Cached 12h. |
| 🟡 **Live Yelp Reviews** | `[reviewfic_yelp business_id="..."]` — fetch and display live Yelp Fusion reviews. Cached 12h. |
| 📤 **Import / Export** | Export all reviews as CSV or JSON. Import from either format — new sources/categories created automatically. |
| ❓ **Get Help** | Dedicated admin page linking to support, feature requests, and documentation. |
| 🔌 **Our Plugins** | One-click install for other Themefic plugins directly from the WordPress admin. |
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

Paste it into any page, post, or widget. If you later change any option in the generator, every instance of that shortcode updates automatically — no need to edit your pages.

---

## 🎨 Templates

| # | Name | Layout |
|---|---|---|
| 1 | **Classic** | Source badge → stars → title → content → client row at bottom |
| 2 | **Quote** | Avatar + name at top → large italic quote → stars → source badge. Orange left border on warm background. |
| 3 | **Minimal** | No card border — thin accent top line only. Pure typography, editorial feel. |
| 4 | **Dark** | Dark (`#111827`) card, white text, gold stars, accent-colored avatar ring, subtle corner glow. |
| 5 | **Centered** | Everything center-aligned — large avatar at top, centered stars, centered italic quote. Ideal for a single featured testimonial. |

---

## 🎠 Slider Options

When slider mode is enabled, the following options can be configured per shortcode:

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

Create a config in the Shortcode Generator and use the ID. Edit options anytime without touching the shortcode.

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

### Examples

```
[reviewfic id="21"]

[reviewfic_form]

[reviewfic_form require_approval="no" redirect="https://example.com/thank-you"]

[reviewfic_form show_source="no" success_message="Thanks! We'll review your submission shortly."]
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

All values are applied as CSS custom properties on the shortcode wrapper, so multiple shortcodes on the same page are fully independent.

---

## 📤 Import / Export

Go to **Reviewfic → Import / Export** to bulk-manage reviews.

**Export** — Download all reviews as:
- **CSV** — opens in Excel or Google Sheets
- **JSON** — structured data for developers or cross-site migration

**Import** — Upload a `.csv` or `.json` file to bulk-create reviews. New sources and categories are created automatically if they don't exist. Rows with both title and content empty are skipped. New reviews default to **pending** unless the file specifies otherwise.

---

## 🔴 Live Reviews (Google & Yelp)

Pull reviews directly from external platforms and display them using Reviewfic's templates.

### Setup

Go to **Reviewfic → Live Reviews** and enter your API keys:

| Platform | API | Free Tier |
|---|---|---|
| Google | [Places API](https://developers.google.com/maps/documentation/places/web-service/get-api-key) | $200/month credit (~unlimited for most sites) |
| Yelp | [Fusion API](https://www.yelp.com/developers/documentation/v3/authentication) | 3 reviews per request |

### Shortcodes

```
[reviewfic_google place_id="ChIJN1t_tDeuEmsRUsoyG83frY4" columns="3" template="1" max="5"]

[reviewfic_yelp business_id="gary-danko-san-francisco" columns="3" template="1" max="3"]
```

Both shortcodes support all 5 templates, slider mode, columns, `show_avatar`, and design options. Results are **cached for 12 hours** to avoid excessive API calls.

**Finding your IDs:**
- Google Place ID → [Place ID Finder](https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder)
- Yelp Business ID → the slug in your Yelp URL (e.g. `yelp.com/biz/gary-danko-san-francisco`)

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

---

## 📁 File Structure

```
reviewfic/
├── admin/
│   ├── assets/img/          # Star rating SVG icons
│   ├── admin-brand.php      # Admin branding (header bar, button styles)
│   ├── meta-boxes.php       # Review Details meta box (stars, source, reviewer)
│   ├── post-types-taxonomy.php  # CPT + taxonomies registration
│   ├── shortcode-config.php     # Shortcode Generator CPT + meta box + save
│   ├── review-form.php      # [reviewfic_form] shortcode + CF7 integration
│   ├── import-export.php    # Import / Export admin page
│   ├── form-integrations.php # WPForms / Fluent Forms / Gravity Forms
│   ├── live-reviews.php     # Google Places + Yelp Fusion live reviews
│   └── extra-pages.php      # Get Help + Our Plugins admin pages
├── assets/
│   ├── css/
│   │   ├── reviewfic.css        # Frontend styles (templates, slider, pagination, form)
│   │   └── reviewfic-admin.css  # Admin styles (meta box, shortcode generator)
│   └── js/
│       ├── reviewfic.js         # Admin shortcode generator JS
│       └── reviewfic-public.js  # Frontend slider + form star picker JS
├── reviewfic.php            # Plugin bootstrap + enqueue
├── README.md                # This file
└── readme.txt               # WordPress.org readme
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

**Latest — v1.2.27**
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
