=== Reviewfic – Testimonial Slider, Testimonial Grid & Customer Reviews ===
Contributors: hasanet, themefic
Tags: testimonials, reviews, star rating, customer reviews, social proof
Requires at least: 5.4
Tested up to: 6.9
Stable tag: 1.2.15
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Collect, manage, and display customer reviews on your WordPress site — with star ratings, reviewer avatars, source badges, and a shortcode builder.

== Description ==

**Reviewfic** is a lightweight yet powerful WordPress testimonial and review plugin. Whether you are collecting client feedback for a portfolio, showcasing product reviews on an eCommerce store, or aggregating ratings from Google, Trustpilot, G2, and Capterra onto one page — Reviewfic gives you everything you need with zero coding required.

Reviews are managed as a native WordPress custom post type, making them easy to add, edit, organise, and display anywhere on your site using a simple shortcode.

=== Why Choose Reviewfic? ===

Most testimonial plugins are either too bloated or too basic. Reviewfic hits the sweet spot — a focused feature set covering what real WordPress sites actually need, without page-builder lock-in or premium paywalls.

=== Core Features ===

**Star Ratings**
Add a 1–5 star rating to every review with half-star precision (e.g. 4.5). A live visual star preview updates as you type so you always know exactly what will render on the frontend.

**Reviewer Details**
Each review stores the reviewer's Name, Designation (job title), and Company. These display as "CEO · Acme Corp" beneath the name — gracefully adapting if either field is left empty.

**Reviewer Avatars**
Upload a circular profile photo for each reviewer directly from the WordPress media library. Show or hide them per shortcode using the `show_avatar` option in the Shortcode Generator.

**Review Source Badges**
Tag each review with its origin platform — Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon, or any custom platform you add. A color-coded badge renders automatically on each card. Known platforms display their brand colors; custom platforms get a neutral dark badge. Review Sources are managed as a WordPress taxonomy under **Reviewfic → Review Sources**.

**Review Categories**
Organise reviews into categories using a built-in taxonomy. Show only a specific category on any page using the Shortcode Generator.

**5 Display Templates**
Choose from five distinct card layouts per shortcode:

<ul>
<li><strong>Classic</strong> — Stars, title, content, and client row at the bottom. Clean bordered card.</li>
<li><strong>Quote</strong> — Reviewer avatar and name at the top, large italic content as the featured quote, decorative quotation mark, orange-left border on a warm background.</li>
<li><strong>Minimal</strong> — No card border; a thin accent top line is the only visual element. Pure typography focus, ideal for editorial sites.</li>
<li><strong>Dark</strong> — Dark card (#111827), white text, gold stars, accent-colored avatar ring, and a subtle radial glow. Drop into any dark-background section.</li>
<li><strong>Centered</strong> — Everything center-aligned: large circular avatar at the top, centered name and designation, centered stars, centered italic quote. The featured testimonial format.</li>
</ul>

**Slider Mode**
Turn any shortcode into a swipeable carousel with full control over every slider option:

<ul>
<li>Navigation Arrows — show or hide the prev/next buttons</li>
<li>Dot Indicators — show or hide progress dots</li>
<li>Autoplay — auto-advance slides on a configurable interval</li>
<li>Autoplay Speed — set the interval in milliseconds</li>
<li>Infinite Loop — wrap around at the end</li>
<li>Pause on Hover — stop autoplay when visitors mouse over</li>
<li>Touch &amp; swipe support on mobile</li>
<li>Keyboard arrow key navigation</li>
</ul>

**Pagination**
Enable numbered pagination on any grid shortcode. Set how many reviews appear per page. Previous/next arrows, numbered page links, and smart ellipsis for long page ranges are all included. Slider mode is automatically excluded from pagination.

**Design Options**
Customise the visual style of each shortcode independently — no CSS required:

<ul>
<li>Card background color</li>
<li>Content text color</li>
<li>Star color</li>
<li>Accent/border color (used on Templates 2 and 3)</li>
<li>Border radius (0–24px slider)</li>
</ul>

Values are applied as CSS custom properties on the wrapper element, so multiple shortcodes on the same page each maintain their own visual style without conflict.

**Shortcode Generator**
Create and save named shortcode configurations from **Reviewfic → Shortcode Generator**. Each configuration gives you a permanent shortcode like `[reviewfic id="21"]`. Paste it once into any page or post — then edit the configuration anytime and every instance updates automatically. No need to touch the shortcode again.

The Shortcode Generator includes:
<ul>
<li>Visual template picker with layout preview thumbnails for all 5 templates</li>
<li>Column toggle (1–4)</li>
<li>Category and Review Source filters</li>
<li>All slider options with toggle switches</li>
<li>Pagination toggle and per-page count</li>
<li>Color pickers and border radius for design options</li>
<li>One-click Copy Shortcode button</li>
</ul>

**Responsive Grid**
Display reviews in a 1, 2, 3, or 4 column grid. Collapses to a single column on tablets and mobile automatically.

=== Shortcode Reference ===

**Recommended — ID-based (use the Shortcode Generator):**
<pre>[reviewfic id="21"]</pre>
Create a shortcode configuration in **Reviewfic → Shortcode Generator**, copy the ID shortcode, and paste it anywhere. All options are managed in the generator — no shortcode attributes needed.

**Legacy — parameter-based (still fully supported):**
<pre>[reviewfic template="1" columns="3" slider="yes" source="google" category="all" max_items="6" show_avatar="yes" pagination="no" per_page="6"]</pre>

<ul>
<li><strong>template</strong> — Card layout: <code>1</code> Classic, <code>2</code> Quote, <code>3</code> Minimal, <code>4</code> Dark, <code>5</code> Centered. Default: <code>1</code></li>
<li><strong>columns</strong> — Grid columns: <code>1</code>, <code>2</code>, <code>3</code>, or <code>4</code>. Default: <code>1</code></li>
<li><strong>slider</strong> — Enable slider mode: <code>yes</code> or <code>no</code>. Default: <code>no</code></li>
<li><strong>slider_nav</strong> — Show navigation arrows: <code>yes</code> or <code>no</code>. Default: <code>yes</code></li>
<li><strong>slider_dots</strong> — Show dot indicators: <code>yes</code> or <code>no</code>. Default: <code>yes</code></li>
<li><strong>slider_auto</strong> — Autoplay: <code>yes</code> or <code>no</code>. Default: <code>no</code></li>
<li><strong>slider_speed</strong> — Autoplay speed in milliseconds. Default: <code>4000</code></li>
<li><strong>slider_loop</strong> — Infinite loop: <code>yes</code> or <code>no</code>. Default: <code>yes</code></li>
<li><strong>slider_pause</strong> — Pause on hover: <code>yes</code> or <code>no</code>. Default: <code>yes</code></li>
<li><strong>source</strong> — Filter by Review Source slug, or <code>all</code>. Default: <code>all</code></li>
<li><strong>category</strong> — Filter by category slug, or <code>all</code>. Default: <code>all</code></li>
<li><strong>max_items</strong> — Max reviews to display. Use <code>-1</code> for unlimited. Default: unlimited</li>
<li><strong>show_avatar</strong> — Show reviewer photo: <code>yes</code> or <code>no</code>. Default: <code>yes</code></li>
<li><strong>pagination</strong> — Enable pagination: <code>yes</code> or <code>no</code>. Default: <code>no</code></li>
<li><strong>per_page</strong> — Reviews per page when pagination is on. Default: <code>6</code></li>
</ul>

=== Example Shortcodes ===

All reviews, 3 columns, Classic template:
<pre>[reviewfic columns="3" template="1"]</pre>

Google reviews only, slider with autoplay, Quote template:
<pre>[reviewfic source="google" slider="yes" slider_auto="yes" template="2"]</pre>

Trustpilot reviews, paginated, 6 per page, Dark template:
<pre>[reviewfic source="trustpilot" pagination="yes" per_page="6" template="4"]</pre>

Featured testimonial, centered layout, no pagination:
<pre>[reviewfic template="5" columns="1" max_items="1"]</pre>

=== Who Is This For? ===

<ul>
<li>Freelancers and agencies showcasing client testimonials</li>
<li>SaaS and software products aggregating reviews from G2, Capterra, and Trustpilot</li>
<li>eCommerce stores displaying product reviews with star ratings</li>
<li>Local businesses pulling in Google and Yelp reviews</li>
<li>Any WordPress site that needs a clean, fast testimonial section</li>
</ul>

=== Bug Reports / Feature Requests / Support ===

We provide full support on the WordPress.org forums and on [Our Website](https://portal.themefic.com/support/). Check our [Support Policy](https://themefic.com/support-policy/) for details.

== Installation ==

1. In your WordPress admin, go to **Plugins → Add New** and search for "Reviewfic". Click **Install Now**, then **Activate**.
2. Alternatively, download the zip, go to **Plugins → Add New → Upload Plugin**, upload the zip, and activate.
3. Once activated, go to **Reviewfic → Add New Review** to create your first review. Fill in the title (review headline), body text, star rating, reviewer name, designation, company, photo, and review source.
4. Go to **Reviewfic → Review Sources** to manage review platforms. Google, Trustpilot, G2, Capterra, Facebook, Yelp, and Amazon are added automatically on first activation.
5. Go to **Reviewfic → Shortcode Generator** and click **Create New Shortcode**. Configure your options, save, and copy the generated shortcode into any page, post, or widget.

== Frequently Asked Questions ==

= What is the recommended way to add a shortcode? =

Use the **Shortcode Generator** (Reviewfic → Shortcode Generator → Create New Shortcode). You get a permanent `[reviewfic id="21"]` shortcode. Configure all options in the generator — if you change something later, every page using that shortcode updates automatically.

= Does this plugin work with any WordPress theme? =

Yes. Reviewfic uses standard CSS grid and does not require any page builder. It works with any well-coded WordPress theme.

= Can I display reviews from multiple platforms on the same page? =

Yes. Use one shortcode per platform, or leave the source filter set to "All Sources" to mix everything together.

= Can I add platforms beyond the defaults? =

Yes. Go to **Reviewfic → Review Sources** and add any platform name. It appears immediately in the review editor dropdown and the Shortcode Generator. Known platforms (Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon) get automatic brand colors; anything custom gets a neutral dark badge.

= How do the 5 templates differ? =

Template 1 (Classic): Standard card — stars, title, content, client row at bottom. Template 2 (Quote): Avatar and name at the top, large italic quote, orange left border. Template 3 (Minimal): No card border, just a thin accent top line — pure typography. Template 4 (Dark): Dark background card with white text and gold stars. Template 5 (Centered): All content center-aligned with a large avatar at the top — ideal for a single featured testimonial.

= Can I use the slider and pagination at the same time? =

No. Slider mode and pagination are mutually exclusive. When slider is enabled, pagination is automatically disabled. Use one or the other depending on whether you want a carousel or a paged grid.

= Will my existing shortcodes break after upgrading? =

No. The legacy parameter-based shortcode format (`[reviewfic template="1" columns="3"]`) is fully supported and will continue to work exactly as before.

= What image size is recommended for reviewer avatars? =

Any square image works. WordPress crops it to the thumbnail size automatically. For crisp results, upload a minimum of 150×150 pixels.

= Is Reviewfic compatible with Gutenberg and the block editor? =

Yes. The shortcode works in any block that accepts shortcodes, including the core Shortcode block and any Classic Editor block. It also works in widget areas and page builders that support shortcodes.

== Screenshots ==
1. Reviewfic admin — Reviews list with branded interface
2. Add New Review — star rating, review source, reviewer details (name, designation, company), and avatar upload
3. Shortcode Generator — visual builder with 5-template picker, slider options, pagination, and design controls
4. Frontend — Classic and Quote template cards with avatars and source badges
5. Slider mode — swipeable carousel with dot indicators and navigation arrows
6. Dark and Centered templates

== Changelog ==

= 1.2.15 =
* Improvement: Plugin name updated to better reflect the full feature set.
* Improvement: Tested up to WordPress 6.9.

= 1.2.13 =
* Fix: Reduced wp.org tags to 5 to comply with repository guidelines.
* Fix: Shortened plugin description to under 150 characters.

= 1.2.12 =
* Improvement: Header bar CSS refined — cleaner margin approach.
* Improvement: Save Draft and Preview buttons styled with light brand green background.
* Improvement: Top-level admin menu renamed from "Reviews" to "Reviewfic".

= 1.2.11 =
* Improvement: Screen Options panel hidden on all Reviewfic admin screens except Add New Review.
* Improvement: Branded header bar removed from Add New Review screen for a cleaner editing experience.

= 1.2.10 =
* Fix: Branded header bar now inserts before .wrap for true full-width rendering.
* Fix: Removed green color overrides from list table row titles, hover backgrounds, current filter links, sortable column headers, and row action links — all reverted to WordPress defaults.

= 1.2.9 =
* Improvement: Brand color updated to professional green (#0E9F6E). Distinct from Trustpilot green, applied across all admin and frontend elements.
* New: Branded admin header bar with Reviewfic logo, current page context, and version number on all Reviews and Shortcode Generator screens.
* Improvement: Admin menu parent item renamed to "Reviewfic".
* Improvement: "Add Post" renamed to "Add New Review" via proper CPT labels.
* Improvement: Primary buttons, title input focus ring, and "Your Shortcode" sidebar box all branded consistently.

= 1.2.8 =
* New: Design Options — per-shortcode color pickers (card background, text, stars, accent) and border radius slider. Applied as CSS custom properties — no conflicts between multiple shortcodes on the same page.
* New: Pagination — numbered pagination with configurable per-page count, prev/next arrows, and smart ellipsis.
* New: Designation field — add a job title or role to each reviewer. Displayed as "Designation · Company" beneath the name.
* Improvement: "Sources" taxonomy renamed to "Review Sources" throughout the admin.

= 1.2.7 =
* Improvement: Template picker in Shortcode Generator shows visual mini layout preview thumbnails for all 5 templates.
* Improvement: Legacy Shortcode Generator page removed. The ID-based Shortcode Generator is now the only entry point.
* Improvement: "Shortcodes" admin menu item renamed to "Shortcode Generator".

= 1.2.6 =
* New: ID-based shortcode system — create a named config in Reviewfic → Shortcode Generator, get a permanent [reviewfic id="X"] shortcode. Edit the config anytime; all pages update automatically.
* New: Slider options — Navigation Arrows, Dot Indicators, Autoplay, Autoplay Speed, Infinite Loop, and Pause on Hover, all configurable per shortcode.
* Improvement: Template picker redesigned as a clean single-column list.
* Backward compatible: legacy parameter-based shortcodes continue to work unchanged.

= 1.2.5 =
* New: Template 3 — Minimal: borderless, accent top line, editorial layout.
* New: Template 4 — Dark: dark card, white text, gold stars.
* New: Template 5 — Centered: center-aligned, large avatar at top.

= 1.2.4 =
* New: Slider mode with swipe, keyboard navigation, dot indicators, and prev/next arrows.
* New: Template 2 — Quote style: avatar at top, large italic quote, decorative quotation mark.

= 1.2.3 =
* New: README.md added for GitHub.
* Improvement: Admin UI updated with Dashicons and branded styling.

= 1.2.2 =
* New: Default Review Sources seeded automatically on first activation.
* Improvement: Meta box redesigned with live star preview and live source badge preview.
* Improvement: Shortcode Generator redesigned with dark code output block and animated copy button.

= 1.2.1 =
* Improvement: Review Source is now a proper WordPress taxonomy.
* Improvement: Shortcode supports combining category and source filters simultaneously.

= 1.1.0 =
* New: Reviewer Avatar — circular profile photo via WordPress media library.
* New: Review Source Badges — color-coded platform badges with per-shortcode source filter.

= 1.0.1 =
* Compatibility check with WordPress 6.8.1.

= 1.0 =
* Initial stable release.
