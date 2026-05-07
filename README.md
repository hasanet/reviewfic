<div align="center">

# ⭐ Reviewfic

### Testimonial Slider, Testimonial Grid & Customer Reviews for WordPress

[![Version](https://img.shields.io/badge/version-1.2.15-brightgreen.svg)](https://github.com/hasanet/reviewfic)
[![WordPress](https://img.shields.io/badge/WordPress-5.4%2B-blue.svg)](https://wordpress.org)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WP%206.9-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Collect, manage, and display customer reviews on any WordPress site — star ratings, reviewer avatars, source badges, sliders, pagination, 5 templates, and a visual shortcode builder. Zero coding required.

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

### Legacy — parameter-based (fully supported)

```
[reviewfic
  template="1"
  columns="3"
  slider="no"
  slider_nav="yes"
  slider_dots="yes"
  slider_auto="no"
  slider_speed="4000"
  slider_loop="yes"
  slider_pause="yes"
  source="all"
  category="all"
  max_items="-1"
  show_avatar="yes"
  pagination="no"
  per_page="6"
]
```

| Parameter | Values | Default | Description |
|---|---|---|---|
| `template` | `1` `2` `3` `4` `5` | `1` | Display template |
| `columns` | `1` `2` `3` `4` | `1` | Grid columns (ignored in slider mode) |
| `slider` | `yes` `no` | `no` | Enable slider mode |
| `slider_nav` | `yes` `no` | `yes` | Show prev/next arrows |
| `slider_dots` | `yes` `no` | `yes` | Show dot indicators |
| `slider_auto` | `yes` `no` | `no` | Autoplay |
| `slider_speed` | number (ms) | `4000` | Autoplay interval |
| `slider_loop` | `yes` `no` | `yes` | Infinite loop |
| `slider_pause` | `yes` `no` | `yes` | Pause autoplay on hover |
| `source` | slug or `all` | `all` | Filter by Review Source |
| `category` | slug or `all` | `all` | Filter by Review Category |
| `max_items` | number or `-1` | `-1` | Max reviews (unlimited) |
| `show_avatar` | `yes` `no` | `yes` | Show reviewer photo |
| `pagination` | `yes` `no` | `no` | Enable pagination (grid only) |
| `per_page` | number | `6` | Reviews per page |

### Examples

```bash
# All reviews, 3 columns, Classic template
[reviewfic columns="3" template="1"]

# Google reviews only, autoplay slider, Quote template
[reviewfic source="google" slider="yes" slider_auto="yes" template="2"]

# Trustpilot reviews, paginated, 6 per page, Dark template
[reviewfic source="trustpilot" pagination="yes" per_page="6" template="4"]

# Single featured testimonial, Centered template
[reviewfic template="5" columns="1" max_items="1"]

# Minimal grid, no avatars, filtered by category
[reviewfic template="3" columns="2" category="saas-tools" show_avatar="no"]
```

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
│   └── shortcode.php        # [reviewfic] shortcode handler
├── assets/
│   ├── css/
│   │   ├── reviewfic.css        # Frontend styles (templates, slider, pagination)
│   │   └── reviewfic-admin.css  # Admin styles (meta box, shortcode generator)
│   └── js/
│       ├── reviewfic.js         # Admin shortcode generator JS
│       └── reviewfic-public.js  # Frontend slider JS (dependency-free)
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

**Latest — v1.2.15**
- Plugin name updated to better reflect the full feature set
- Tested up to WordPress 6.9

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
