=== Reviewfic – Testimonial Slider, Testimonial Grid & Customer Reviews ===
Contributors: hasanet, themefic
Tags: testimonials, reviews, star rating, customer reviews, social proof
Requires at least: 5.4
Tested up to: 6.9
Stable tag: 1.2.36
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Collect, manage, and display customer reviews on your WordPress site — with star ratings, reviewer avatars, source badges, and a shortcode builder.

== Description ==

👉 Official Demo Link: [Click Here](https://themefic.com/reviewfic/)
👉 Get Support: [Click Here](https://portal.themefic.com/support/)
👉 Our [Documentation](https://themefic.com/reviewfic/docs)
👉 Join Our [Facebook Group](https://www.facebook.com/groups/themefic)

**Reviewfic** is a lightweight yet powerful WordPress testimonial and review plugin. Whether you are collecting client feedback for a portfolio, showcasing product reviews on an eCommerce store, or aggregating ratings from Google, Trustpilot, G2, and Capterra onto one page — Reviewfic gives you everything you need with zero coding required.

Reviews are managed as a native WordPress custom post type, making them easy to add, edit, organise, and display anywhere on your site using a simple shortcode.

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
<li>Reviewer name color</li>
<li>Designation and company color</li>
<li>Star color</li>
<li>Accent color — controls Template 2 left border, Template 3 top bar, and source badge borders</li>
<li>Card border color</li>
<li>Box shadow (None / Subtle / Medium / Strong)</li>
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
<li>Color pickers for card background, text, name, designation/company, stars, accent, and border</li>
<li>Box shadow selector and border radius slider</li>
<li>One-click Copy Shortcode button</li>
</ul>

**Review Collection Form**
Collect reviews directly from your customers using a built-in submission form. Place `[reviewfic_form]` on any page or post to render a styled form with all the fields your review post type supports: name, designation, company, star rating, review title, review body, platform source, and a drag-and-drop photo upload with live circular preview. New submissions are held as pending by default so you can approve them before they go live — or set `require_approval="no"` to publish immediately.

Form shortcode attributes:
<ul>
<li><strong>require_approval</strong> — <code>yes</code> (default) or <code>no</code>. Controls whether submissions are saved as pending or published immediately.</li>
<li><strong>success_message</strong> — Custom thank-you message shown after a successful submission.</li>
<li><strong>show_source</strong> — <code>yes</code> (default) or <code>no</code>. Show or hide the platform/source dropdown.</li>
<li><strong>redirect</strong> — URL to redirect to after a successful submission (optional).</li>
</ul>

**Contact Form 7 Integration**
Connect an existing Contact Form 7 form to Reviewfic. After installing CF7, a "Reviewfic" tab appears in the CF7 form editor. Enable the integration, set the review status (pending or published), and map your CF7 field names to Reviewfic fields — Reviewer Name, Designation, Company, Star Rating, Review Title, Review Content, and Review Source. Every time that form is submitted, a review is created automatically in Reviewfic using the mapped field values. No coding required.

**Import / Export**
Bulk-manage your reviews without touching the database. Export all reviews as a CSV file (opens in Excel or Google Sheets) or as a JSON file for developer use or re-importing. Import a CSV or JSON file to bulk-create reviews on any site — new sources and categories are created automatically if they don't already exist. Access via **Reviewfic → Import / Export**.

**WPForms, Fluent Forms & Gravity Forms Integrations**
Connect any WPForms, Fluent Forms, or Gravity Forms form to Reviewfic from the dedicated **Reviewfic → Form Integrations** admin page. A tabbed interface lists all forms from each installed plugin. Per form: enable toggle, review status (pending or published), review source dropdown, and full field mapping (name, designation, company, star rating, title, content, photo). Connected forms are automatically styled to match the Reviewfic form design on the frontend.

**Live Reviews — Google Places**
Display live reviews directly from Google using `[reviewfic_google place_id="ChIJ..."]`. Reviews are fetched from the Google Places API, cached for 12 hours, and rendered using any of Reviewfic's 5 templates with full slider, column, and design option support. Requires a Google Places API key, configured at **Reviewfic → Live Reviews**.

**Live Reviews — Yelp**
Display live reviews directly from Yelp using `[reviewfic_yelp business_id="..."]`. Reviews are fetched from the Yelp Fusion API (up to 3 on the free tier), cached for 12 hours, and support all the same display options as Google. Requires a Yelp Fusion API key, configured at **Reviewfic → Live Reviews**.

**WooCommerce Integration**
A dedicated **Reviewfic → WooCommerce** admin page brings three powerful features:

<ul>
<li><strong>Post-Purchase Review Request Email</strong> — automatically emails customers after order completion with a link to your review form. Choose the delay: immediately, or 1, 2, 3, 5, 7, or 14 days after order completion. Select any published page as the review landing page (with your `[reviewfic_form]` shortcode on it). Customize the email subject and body with placeholders: `{customer_name}`, `{order_id}`, `{site_name}`.</li>
<li><strong>Replace WooCommerce Reviews Tab</strong> — replaces the default WooCommerce product reviews tab with Reviewfic's templates. Existing product reviews are rendered using Template 1 (Classic) with a full Reviewfic design. A "Write a Review" button links to your configured review landing page.</li>
<li><strong>Auto-Tag Reviews by Product</strong> — when a customer clicks the review link from the email and submits a review, it is automatically tagged with the product name as a Reviewfic category. Works for single-product orders and enables product-filtered shortcodes.</li>
</ul>

**Responsive Grid**
Display reviews in a 1, 2, 3, or 4 column grid. Collapses to a single column on tablets and mobile automatically.

=== Shortcode Reference ===

**Display reviews — ID-based (recommended):**
<pre>[reviewfic id="21"]</pre>
Create a shortcode configuration in **Reviewfic → Shortcode Generator**, copy the ID shortcode, and paste it anywhere. All options are managed in the generator — no shortcode attributes needed.

**Collect reviews — submission form:**
<pre>[reviewfic_form]</pre>
Renders a styled review submission form. Customers fill in their name, star rating, review, and optionally their designation, company, and platform source. Submissions are held as pending until you approve them.

=== Example Shortcodes ===

Display reviews from the Shortcode Generator:
<pre>[reviewfic id="21"]</pre>

Submission form with auto-approval and a redirect on success:
<pre>[reviewfic_form require_approval="no" redirect="https://example.com/thank-you"]</pre>

Submission form without the platform dropdown:
<pre>[reviewfic_form show_source="no"]</pre>

Live Google reviews, 3 columns, Classic template:
<pre>[reviewfic_google place_id="ChIJN1t_tDeuEmsRUsoyG83frY4" columns="3" template="1"]</pre>

Live Yelp reviews, slider mode:
<pre>[reviewfic_yelp business_id="gary-danko-san-francisco" slider="yes" template="2"]</pre>

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

= What image size is recommended for reviewer avatars? =

Any square image works. WordPress crops it to the thumbnail size automatically. For crisp results, upload a minimum of 150×150 pixels.

= Is Reviewfic compatible with Gutenberg and the block editor? =

Yes. The shortcode works in any block that accepts shortcodes, including the core Shortcode block and any Classic Editor block. It also works in widget areas and page builders that support shortcodes.

= How do I let customers submit reviews directly? =

Place `[reviewfic_form]` on any page or post. A styled submission form will appear with all the necessary fields. By default, new submissions are saved as pending so you can review and approve them from **Reviewfic → All Reviews**. Set `require_approval="no"` to publish submissions immediately.

= Can I use Contact Form 7 to collect reviews? =

Yes. Install Contact Form 7, then open any CF7 form in the editor. You will see a "Reviewfic" tab. Enable the integration, choose whether submissions go to pending or published, and map your CF7 field names to Reviewfic fields (name, rating, content, etc.). From that point on, every submission of that form automatically creates a review in Reviewfic.

= How does the WooCommerce post-purchase email work? =

When a customer's order is marked as completed, Reviewfic schedules a review request email using WP-Cron. The delay is configurable (immediately, or up to 14 days). The email includes a styled CTA button that links to the review page you select in **Reviewfic → WooCommerce**. If the order has a single product, the link includes the product ID so the review is automatically tagged with the product name.

= Will the WooCommerce tab replacement affect existing reviews? =

No. The replacement only changes how reviews are *displayed*. All existing WooCommerce product reviews (stored in wp_comments) are rendered using Reviewfic's Classic template. No data is lost or moved.

= Will my existing shortcodes break after upgrading? =

No. All existing `[reviewfic id="X"]` shortcodes continue to work exactly as before.

= How do I display live Google reviews? =

Go to **Reviewfic → Live Reviews**, enter your Google Places API key, then use `[reviewfic_google place_id="YOUR_PLACE_ID"]` on any page. Your Place ID can be found using the Google Place ID Finder tool. Reviews are cached for 12 hours automatically.

= How do I display live Yelp reviews? =

Go to **Reviewfic → Live Reviews**, enter your Yelp Fusion API key, then use `[reviewfic_yelp business_id="YOUR_BUSINESS_ID"]`. The Business ID is the slug in your Yelp business URL. Yelp's free tier returns up to 3 reviews.

= Can I connect WPForms, Fluent Forms, or Gravity Forms? =

Yes. Go to **Reviewfic → Form Integrations** and select the relevant tab. Enable any form, choose the review status and source, and map your form field IDs to Reviewfic fields. Submissions from connected forms automatically create reviews in Reviewfic.

== Screenshots ==
1. Reviewfic admin — Reviews list with branded interface
2. Add New Review — star rating, review source, reviewer details (name, designation, company), and avatar upload
3. Shortcode Generator — visual builder with 5-template picker, slider options, pagination, and design controls
4. Frontend — Classic and Quote template cards with avatars and source badges
5. Slider mode — swipeable carousel with dot indicators and navigation arrows
6. Dark and Centered templates

== Changelog ==

= 1.2.36 =
* Fix: CF7 photo upload now correctly handles CF7 5.x+ where uploaded_files() returns an array of paths instead of a single string — previously caused a PHP 8 TypeError that corrupted CF7's JSON response and left the form stuck on the loading spinner.
* Fix: Wrapped media_handle_sideload in an output buffer so any PHP notices from WP image helpers cannot bleed into CF7's AJAX / REST API JSON response.

= 1.2.28 =
* New: WooCommerce integration — post-purchase review request email with configurable delay (0–14 days).
* New: WooCommerce integration — configurable review landing page (any published page) linked in the email CTA.
* New: WooCommerce integration — replace the WooCommerce product reviews tab with Reviewfic templates.
* New: WooCommerce integration — auto-tag reviews by product name (Reviewfic category) when submitted via review request link.
* Improvement: [reviewfic_form] now accepts rwf_product URL parameter for WooCommerce product context.

= 1.2.27 =
* Fix: Our Plugins page now shows real plugin icons fetched from WordPress.org with letter-badge fallback.
* New: Live Reviews — [reviewfic_google] shortcode pulls live reviews from Google Places API.
* New: Live Reviews — [reviewfic_yelp] shortcode pulls live reviews from Yelp Fusion API.
* New: Reviewfic → Live Reviews admin page for managing API keys and shortcode docs.
* Improvement: Live reviews are cached for 12 hours and rendered using all existing templates and design options.

= 1.2.26 =
* New: Reviewfic → Get Help page — three cards linking to Support, Feature Request, and Documentation.
* New: Reviewfic → Our Plugins page — showcases 6 Themefic plugins with Install Now / Activate / Active status buttons that work directly from the WordPress admin.

= 1.2.25 =
* New: WPForms integration — connect any WPForms form to Reviewfic via Reviewfic → Form Integrations.
* New: Fluent Forms integration — same per-form field mapping and source selection.
* New: Gravity Forms integration — same per-form field mapping and source selection.
* New: Form Integrations admin page with tabbed UI for all three plugins, showing all forms with enable toggle, status, source dropdown, and field mapping.
* Improvement: Connected WPForms, Fluent Forms, and Gravity Forms forms are styled to match Reviewfic form design on the frontend.

= 1.2.24 =
* Improvement: CF7 integration — Review Source is now a dropdown populated from the Reviewfic taxonomy instead of a manual slug input.
* New: CF7 integration — Photo upload field mapping with drag-and-drop uploader on the frontend (same design as the built-in form).
* New: CF7 forms connected to Reviewfic are automatically styled to match the Reviewfic review form design. Style is scoped only to connected forms.

= 1.2.23 =
* Fix: Re-enabled column support in slider mode — columns control how many cards are visible per page.
* Improvement: Slider now supports multi-column page-by-page navigation. 3 columns = 3 cards per slide, dots represent pages.
* Fix: data-columns attribute now passed from shortcode to slider wrapper.

= 1.2.22 =
* Fix: Slider layout broken from slide 2 onwards — caused by a legacy CSS rule adding margin to track items, making each slide overflow its 100% width boundary.
* Fix: Slider items now have explicit flex-shrink:0 and max-width:100% so adjacent slides can never bleed into view.
* Improvement: Enabling slider in Shortcode Generator now automatically disables pagination, and vice versa.
* Improvement: Column toggle is greyed out when slider mode is active (columns have no effect in slider mode).

= 1.2.21 =
* New: Reviewer Name Color option in Design panel.
* New: Designation and Company Color option in Design panel.
* New: Card Border Color option in Design panel.
* New: Box Shadow option in Design panel (None / Subtle / Medium / Strong).
* Improvement: Accent Color hint updated to clarify it controls Template 2 left border, Template 3 top bar, and source badges.

= 1.2.20 =
* Fix: Import/Export admin CSS was not loading due to incorrect hook name.
* Fix: Import/Export page design overhauled — removed emoji icons, proper card layout with Dashicons, clean format picker.
* Fix: Shortcode Generator template picker now reliably restores the selected template on edit via JS init.

= 1.2.19 =
* New: Import / Export page under Reviewfic admin menu.
* New: Export all reviews as CSV (Excel/Sheets-compatible) or JSON.
* New: Import reviews from a CSV or JSON file. New sources and categories are created automatically. Rows with empty title and content are skipped.

= 1.2.18 =
* Improvement: Photo upload on review form replaced with a modern drag-and-drop zone — circular avatar preview, drag-over highlight, and remove button.

= 1.2.17 =
* New: Photo upload field on the [reviewfic_form] submission form — reviewers can attach their own profile photo directly. Circular preview shown before submission.

= 1.2.16 =
* New: Review Collection Form — add `[reviewfic_form]` to any page to let customers submit reviews directly. Submissions are held as pending by default for approval.
* New: Contact Form 7 Integration — connect any CF7 form to Reviewfic via a new "Reviewfic" tab in the CF7 editor. Map CF7 field names to reviewer name, star rating, review content, and more.
* Improvement: Removed "Why Choose Reviewfic?" section from readme for a cleaner plugin page.

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
