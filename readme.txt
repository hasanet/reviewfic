=== Reviewfic – Testimonial & Review Plugin for WordPress ===
Contributors: hasanet, themefic
Tags: testimonials, reviews, star rating, customer reviews, social proof
Requires at least: 5.4
Tested up to: 6.8
Stable tag: 1.2.13
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Collect, manage, and display customer reviews on your WordPress site — with star ratings, reviewer avatars, source badges, and a shortcode builder.

== Description ==

**Reviewfic** is a lightweight but powerful WordPress testimonial and review plugin. Whether you are collecting client feedback for a portfolio, showcasing product reviews on an eCommerce store, or aggregating ratings from Google, Trustpilot, G2, and Capterra onto one page — Reviewfic gives you everything you need with zero coding required.

Reviews are managed as a native WordPress custom post type, making them easy to add, edit, organise, and display anywhere on your site using a simple shortcode.

=== Why Choose Reviewfic? ===

Most testimonial plugins are either too bloated or too basic. Reviewfic sits in the sweet spot: a clean, focused feature set that covers what real WordPress sites actually need — star ratings, reviewer photos, platform badges, responsive grid layouts, and a visual shortcode builder — without the page-builder lock-in or premium paywalls.

=== Core Features ===

**Star Ratings**
Add a 1–5 star rating to every review with half-star precision. A live visual star preview updates as you type so you always know exactly what will render on the frontend.

**Reviewer Avatars**
Upload a circular profile photo for each reviewer directly from the WordPress media library. Avatars display next to the reviewer's name and company on each card. Show or hide them per shortcode using `show_avatar="yes"` or `show_avatar="no"`.

**Review Source Badges**
Tag each review with the platform it came from — Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon, or any custom platform you add. A color-coded badge renders automatically on each card so visitors instantly know where the review originated. The source list is managed as a WordPress taxonomy under **Reviews → Sources**, so you can add, rename, or remove platforms any time without touching code.

**Responsive Grid Layout**
Display reviews in a 1, 2, 3, or 4 column grid. The layout collapses gracefully to a single column on tablets and mobile devices automatically.

**Visual Shortcode Generator**
Build your shortcode visually from the **Reviews → Shortcode Generator** page. Filter by category, source, and number of columns, toggle avatars on or off, set a max item count, and copy the generated shortcode to your clipboard in one click.

**Review Categories**
Organise reviews into categories using a built-in taxonomy. Display only a specific category on any page using the `category` parameter.

**Source Filtering**
Filter any shortcode output to show only reviews from a specific platform. Create dedicated pages like "Our Google Reviews" or embed a Trustpilot-only widget in your sidebar — all with a single shortcode attribute.

=== Shortcode Reference ===

<pre>[reviewfic category="all" source="all" columns="3" max_items="6" show_avatar="yes"]</pre>

<ul>
<li><strong>category</strong> — Category slug to filter by, or <code>all</code> for every category. Default: <code>all</code></li>
<li><strong>source</strong> — Source slug to filter by (e.g. <code>google</code>, <code>trustpilot</code>), or <code>all</code>. Default: <code>all</code></li>
<li><strong>columns</strong> — Grid columns: <code>1</code>, <code>2</code>, <code>3</code>, or <code>4</code>. Default: <code>1</code></li>
<li><strong>max_items</strong> — Maximum reviews to show. Use <code>-1</code> for unlimited. Default: unlimited</li>
<li><strong>show_avatar</strong> — Show reviewer photo: <code>yes</code> or <code>no</code>. Default: <code>yes</code></li>
</ul>

=== Example Shortcodes ===

Display all reviews in a 3-column grid:
<pre>[reviewfic columns="3"]</pre>

Show only Google reviews, 4 per row, no avatars:
<pre>[reviewfic source="google" columns="4" show_avatar="no"]</pre>

Show Trustpilot reviews from a specific category, max 6:
<pre>[reviewfic source="trustpilot" category="saas-tools" columns="2" max_items="6"]</pre>

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
3. Once activated, go to **Reviews → Add New** to create your first review. Fill in the title, review body, star rating, client name, company, photo, and source platform.
4. Go to **Reviews → Sources** to manage review platforms. Default sources (Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon) are added automatically.
5. Go to **Reviews → Shortcode Generator**, configure your display options, and copy the generated shortcode into any page, post, or widget.

== Frequently Asked Questions ==

= Does this plugin work with any WordPress theme? =

Yes. Reviewfic uses a CSS grid layout that works with any well-coded WordPress theme. No page builder is required.

= Can I display reviews from multiple platforms on the same page? =

Yes. Use one shortcode per platform, or use `source="all"` (the default) to mix all sources together on one page.

= Can I add custom review platforms beyond the defaults? =

Yes. Go to **Reviews → Sources** and add any platform name you like. It will immediately appear in the meta box dropdown and the Shortcode Generator.

= What happens if I don't assign a source to a review? =

No badge is displayed on that card. The source field is completely optional.

= What image size is recommended for reviewer avatars? =

Any square image works best. WordPress crops it to the thumbnail size automatically. For crisp results upload a minimum of 150×150 pixels.

= Will upgrading break my existing reviews? =

No. All new fields are optional and stored separately from the review content. Existing shortcodes continue to work without any changes.

= Is Reviewfic compatible with Gutenberg and the block editor? =

Yes. The shortcode works in any block that accepts shortcodes, including the Shortcode block and any Classic Editor block.

== Screenshots ==
1. Backend — Reviews list
2. Add New Review — star rating, source badge, avatar upload, and reviewer details
3. Shortcode Generator — visual builder with live shortcode output
4. Frontend — review cards with circular avatars and color-coded source badges

== Changelog ==

= 1.2.9 =
* Improvement: Brand color changed from orange to a professional green (#0E9F6E). Distinct from Trustpilot green, applied across all admin UI and frontend elements.
* Improvement: Admin screens rebranded with a green gradient header bar showing the Reviewfic logo, current page context, and plugin version on all Reviews and Shortcode Generator screens.
* Improvement: "Add Post" renamed to "Add New Review" on the review edit screen via proper CPT labels.
* Improvement: List table buttons, row title links, sortable column headers, hover states, subsubsub filters, and row actions all updated to Reviewfic green.
* Improvement: "Your Shortcode" sidebar box styled with green accent border, green header, and dark code block with green monospace text.
* Improvement: Publish button, title input focus ring, and postbox corners all branded consistently.

= 1.2.8 =
* New: Design Options — per-shortcode color controls (card background, text color, star color, accent color) and border radius slider. Set via the Shortcode Generator config. Applied as CSS custom properties so each shortcode instance can have its own visual style.
* New: Pagination — enable numbered pagination on any grid shortcode with a configurable "Reviews Per Page" count. Prev/next arrows, numbered pages, ellipsis for long ranges. Slider mode is automatically excluded from pagination.
* New: Designation field — add a job title or role (e.g. "CEO", "Marketing Manager") to each reviewer. Displayed as "Designation · Company" below the name, or just one if the other is empty.
* Improvement: "Sources" taxonomy renamed to "Review Sources" throughout the admin for clarity.

= 1.2.7 =
* Improvement: Template picker in Shortcode Generator now shows visual mini card previews for all 5 templates — same layout-accurate thumbnails as the legacy generator had.
* Improvement: Legacy Shortcode Generator page removed. The new ID-based system (Reviews → Shortcode Generator) is now the single entry point.
* Improvement: "Shortcodes" admin menu renamed to "Shortcode Generator" for clarity.
* Readme updated to reflect the new shortcode workflow.

= 1.2.6 =
* New: ID-based shortcode system — create named shortcode configs (Reviews → Shortcodes), get a permanent [reviewfic id="123"] shortcode. Edit options anytime and all pages update automatically without touching the shortcode.
* New: reviewfic_config custom post type with full options UI: template, columns, max items, avatar, category, source filter, and all slider settings.
* New: Slider — Navigation Arrows toggle (show/hide), Dot Indicators toggle, Autoplay (on/off), Autoplay Speed, Infinite Loop (on/off), Pause on Hover (on/off).
* Improvement: Template picker redesigned as a single-column list with number badge, name, description, and checkmark — no more overflow on smaller screens.
* Backward compatible: existing [reviewfic template="1" columns="3" ...] shortcodes continue to work unchanged.

= 1.2.5 =
* New: Template 3 — Minimal: no border, orange top accent line, clean editorial layout with no title field.
* New: Template 4 — Dark: dark card (#111827), white text, gold stars, orange avatar border, subtle orange corner glow.
* New: Template 5 — Centered: center-aligned everything, large avatar at top, centered italic quote, centered stars and badge. Ideal for featured testimonials.
* Improvement: Shortcode Generator template picker updated to show all 5 templates with mini visual previews.

= 1.2.4 =
* New Feature: Testimonials Slider — toggle slider mode on any shortcode with slider="yes". Includes prev/next arrows, animated dot indicators, touch swipe, and keyboard navigation. Works with both templates.
* New Feature: Two display templates — Template 1 (Classic card: stars, title, content, client row) and Template 2 (Quote style: avatar at top, large italic quote, decorative quotation mark, orange left border). Chosen via template="1" or template="2".
* Improvement: Shortcode Generator updated with visual template picker cards and a slider toggle switch.

= 1.2.3 =
* Improvement: Brand color updated to review orange across all admin UI elements.
* Improvement: Emoji icons in the admin replaced with native WordPress Dashicons for a polished, consistent look.
* Improvement: Meta box wrapper and header styled with branded accent.
* New: README.md added for GitHub with full documentation, badges, and usage examples.
* Improvement: readme.txt description expanded with SEO-focused content for better discoverability on WordPress.org and Google.

= 1.2.2 =
* New: Default sources (Google, Trustpilot, G2, Capterra, Facebook, Yelp, Amazon) seeded automatically on first admin load.
* Improvement: Meta box redesigned with sectioned layout, live star preview, live source badge preview, and improved avatar upload UI.
* Improvement: Shortcode Generator redesigned with two-column layout, column toggle buttons, avatar toggle switch, dark code output block, and animated copy button.

= 1.2.1 =
* Improvement: Review Source is now a proper WordPress taxonomy (Reviews → Sources).
* Improvement: Shortcode Generator source dropdown pulls live from the taxonomy.
* Improvement: Shortcode supports combining category and source filters simultaneously.

= 1.2.0 =
* New Feature: Custom Review Sources with free-text input and autocomplete suggestions.
* Improvement: Source filtering is now case-insensitive.

= 1.1.0 =
* New Feature: Reviewer Avatar — circular profile photo via WordPress media library.
* New Feature: Review Source Badge — color-coded platform badges with shortcode source filter.
* Improvement: Shortcode Generator updated with avatar and source options plus Copy to Clipboard.

= 1.0.1 =
* Checked compatibility with WordPress 6.8.1.

= 1.0 =
* Initial stable release.
