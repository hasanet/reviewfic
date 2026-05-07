<div align="center">

# ⭐ Reviewfic

### The clean, focused testimonial & review plugin for WordPress

[![Version](https://img.shields.io/badge/version-1.2.5-orange.svg)](https://github.com/hasanet/reviewfic)
[![WordPress](https://img.shields.io/badge/WordPress-5.4%2B-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WP%206.8-blue.svg)](https://wordpress.org)

Collect, manage, and display customer reviews on any WordPress site — with star ratings, reviewer avatars, source badges, and a visual shortcode builder. No coding required.

[WordPress.org Plugin Page](https://wordpress.org/plugins/reviewfic) · [Support](https://portal.themefic.com/support/) · [Report a Bug](https://github.com/hasanet/reviewfic/issues)

</div>

---

## ✨ Features

| Feature | Description |
|---|---|
| ⭐ **Star Ratings** | 1–5 stars with half-star support and a live visual preview in the admin |
| 🖼️ **Reviewer Avatars** | Upload circular profile photos via the WordPress media library |
| 🏷️ **Source Badges** | Color-coded badges for Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon + custom platforms |
| 📂 **Source Taxonomy** | Manage review platforms from **Reviews → Sources** — add or remove any time |
| 🗂️ **Categories** | Organise reviews into categories and filter per shortcode |
| 📐 **Responsive Grid** | 1–4 column layout that collapses automatically on mobile |
| 🎠 **Slider Mode** | Swipeable carousel with arrows, dots, touch and keyboard support |
| 🎨 **5 Templates** | Classic, Quote, Minimal, Dark, and Centered — choose per shortcode |
| ⚙️ **Shortcode Generator** | Visual builder with template picker, live output and one-click copy |
| 🔌 **Zero dependencies** | No jQuery plugins, no FontAwesome, no external CDN calls on the frontend |

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

Go to **Reviews → Add New**. Fill in:
- **Title** — the review headline or reviewer's quote
- **Body** — the full review text
- **Star Rating** — 1 to 5, increments of 0.5
- **Review Source** — pick a platform from the dropdown
- **Reviewer Name & Company**
- **Reviewer Photo** — upload via the media library

### 2. Manage sources

Go to **Reviews → Sources** to add, rename, or delete review platforms. Default sources (Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon) are seeded automatically on first activation.

### 3. Display reviews

Paste this shortcode into any page, post, or widget:

```
[reviewfic]
```

Or use the visual **Shortcode Generator** at **Reviews → Shortcode Generator** to configure and copy a customised shortcode.

---

## 🔧 Shortcode Reference

```
[reviewfic category="all" source="all" columns="3" max_items="6" show_avatar="yes"]
```

| Parameter | Values | Default | Description |
|---|---|---|---|
| `category` | Category slug or `all` | `all` | Filter by review category |
| `source` | Source slug or `all` | `all` | Filter by review platform |
| `columns` | `1` `2` `3` `4` | `1` | Number of grid columns |
| `max_items` | Any number or `-1` | `-1` (unlimited) | Maximum reviews to display |
| `show_avatar` | `yes` or `no` | `yes` | Show/hide reviewer profile photo |

### Examples

```
// All reviews, 3 columns
[reviewfic columns="3"]

// Google reviews only, 4 columns, no avatars
[reviewfic source="google" columns="4" show_avatar="no"]

// Trustpilot reviews from a specific category, max 6
[reviewfic source="trustpilot" category="saas-tools" columns="2" max_items="6"]

// Latest 3 reviews, single column
[reviewfic columns="1" max_items="3"]
```

---

## 🏷️ Source Badge Colors

Known platforms get automatic brand colors:

| Platform | Color |
|---|---|
| Google | ![#4285F4](https://via.placeholder.com/12/4285F4/4285F4.png) `#4285F4` Blue |
| Trustpilot | ![#00B67A](https://via.placeholder.com/12/00B67A/00B67A.png) `#00B67A` Green |
| G2 | ![#FF492C](https://via.placeholder.com/12/FF492C/FF492C.png) `#FF492C` Red |
| Capterra | ![#FF3E72](https://via.placeholder.com/12/FF3E72/FF3E72.png) `#FF3E72` Pink |
| Facebook | ![#1877F2](https://via.placeholder.com/12/1877F2/1877F2.png) `#1877F2` Blue |
| Yelp | ![#FF1A1A](https://via.placeholder.com/12/FF1A1A/FF1A1A.png) `#FF1A1A` Red |
| Amazon | ![#FF9900](https://via.placeholder.com/12/FF9900/FF9900.png) `#FF9900` Orange |
| Custom | ![#444444](https://via.placeholder.com/12/444444/444444.png) `#444` Dark grey |

Any platform you add via **Reviews → Sources** that isn't in this list automatically gets the neutral dark badge.

---

## 📁 File Structure

```
reviewfic/
├── admin/
│   ├── assets/img/          # Star SVG icons
│   ├── meta-boxes.php       # Review Details meta box
│   ├── post-types-taxonomy.php  # CPT + taxonomy registration
│   ├── shortcode-generator.php  # Visual shortcode builder page
│   └── shortcode.php        # [reviewfic] shortcode handler
├── assets/
│   ├── css/
│   │   ├── reviewfic.css        # Frontend styles
│   │   └── reviewfic-admin.css  # Admin styles
│   └── js/
│       └── reviewfic.js         # Shortcode generator JS
├── reviewfic.php            # Plugin bootstrap
└── readme.txt               # WordPress.org readme
```

---

## 🛠️ Development

### Deploy workflow

This repo uses a local deploy script. After cloning:

```bash
# One-time setup
chmod +x deploy.sh watch.sh

# Auto-deploy on new zip (background watcher)
./watch.sh --bg

# Manual deploy
./deploy.sh ~/Downloads/reviewfic_X_X_X.zip
```

The deploy script syncs files to Local by Flywheel and pushes to the `new-updates` branch on GitHub.

---

## 📋 Changelog

See [readme.txt](readme.txt) for the full changelog.

**Latest — v1.2.5**
- Brand color updated to review orange across all admin UI
- Emoji icons replaced with native WordPress Dashicons
- Meta box wrapper styled with branded accent
- SEO-focused readme.txt for WordPress.org discoverability

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
