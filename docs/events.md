# Events

Other extensions can add code or HTML to the news page through these events, without editing NV Newspage.

The events from the original extension keep their `nickvergessen` names, so listeners and style templates written for `nickvergessen/newspage` keep working. Events added since use `phpbbmodders_newspage`.

## PHP events

### nickvergessen.newspage.newspage
* Location: `controller/main.php`
* Variables:
    + `forum_id` - Forum ID of the category to display
    + `year` - Limit the news to a certain year
    + `month` - Limit the news to a certain month
    + `page` - Page to display
* Since: 1.2
* Purpose: Runs before the news list is built. Use it to load settings or language for the news page, or to change which news is shown.

### nickvergessen.newspage.single_news
* Location: `controller/main.php`
* Variables:
    + `topic_id` - Topic ID of the news to display
* Since: 1.2
* Purpose: Runs before a single news item is shown. Use it to load settings or language for that page.

## Template events

A style or extension uses a template event by adding `styles/<style>/template/event/<event name>.html`.

### News page

All in `styles/prosilver/template/newspage_body.html`.

| Event | Since | Where |
|---|---|---|
| `nickvergessen_newspage_before` | 1.2 | Top of the news page, before the heading |
| `phpbbmodders_newspage_single_before` | 1.4.0 | Single news item only: above the "All news" link and the heading |
| `nickvergessen_newspage_title_after` | 1.2 | Right after the heading |
| `nickvergessen_newspage_filters_before` | 1.2 | Before the filter bar |
| `phpbbmodders_newspage_active_filters_append` | 1.4.0 | At the end of the "Showing" line, after the filter chips; only shown while a filter is active |
| `nickvergessen_newspage_filters_after` | 1.2 | After the filter bar and the "Showing" line |
| `phpbbmodders_newspage_single_after` | 1.4.0 | Single news item only: after the news item |
| `nickvergessen_newspage_after` | 1.2 | Bottom of the news page |

The filter bar and the "Showing" line aren't shown on a single news item, so the filter events don't fire there.

### Each news item

All in `styles/prosilver/template/viewtopic_postrow.html`, inside the `postrow` loop, so `postrow.*` variables are available. The `viewtopic_body_*` events have the same names and roughly the same places as in phpBB's `viewtopic_body.html`, so an extension that adds something to forum posts usually shows up on the news page too.

| Event | Since | Where |
|---|---|---|
| `viewtopic_body_postrow_post_before` | 1.2 | Before the news item |
| `viewtopic_body_post_buttons_list_before` | 1.4.0 | Before the post buttons (whether or not they're shown) |
| `viewtopic_body_post_buttons_before` | 1.2 | Inside the post buttons list, before the first button |
| `viewtopic_body_post_buttons_after` | 1.2 | Inside the post buttons list, after the last button |
| `viewtopic_body_post_buttons_list_after` | 1.4.0 | After the post buttons (whether or not they're shown) |
| `viewtopic_body_post_subject_before` | 1.4.0 | Before the title; not on a single news item, where the page heading is the title |
| `viewtopic_body_postrow_post_details_before` | 1.2 | Before the byline |
| `viewtopic_body_avatar_before` | 1.2 | Before the avatar in the byline; only with "Show user information" on and an avatar |
| `viewtopic_body_avatar_after` | 1.2 | After the avatar in the byline; same condition |
| `viewtopic_body_post_author_before` | 1.2 | Before the author's name in the byline |
| `viewtopic_body_post_author_after` | 1.2 | After the author's name in the byline |
| `phpbbmodders_newspage_byline_append` | 1.4.0 | At the end of the byline, after the date and forum |
| `viewtopic_body_postrow_rank_before` | 1.4.0 | Before the rank in the poster line; only with "Show user information" on |
| `viewtopic_body_postrow_rank_after` | 1.4.0 | After the rank in the poster line; same condition |
| `viewtopic_body_postrow_custom_fields_before` | 1.2 | Before the profile fields in the poster line; same condition |
| `viewtopic_body_postrow_custom_fields_after` | 1.2 | After the profile fields in the poster line; same condition |
| `viewtopic_body_postrow_post_details_after` | 1.2 | After the byline and poster line |
| `phpbbmodders_newspage_lead_image_before` | 1.4.0 | Before the lead image; only when one is shown |
| `phpbbmodders_newspage_lead_image_after` | 1.4.0 | After the lead image; same condition |
| `viewtopic_body_postrow_content_before` | 1.4.0 | Before the news text |
| `viewtopic_body_postrow_content_after` | 1.4.0 | After the news text |
| `viewtopic_body_postrow_post_notices_before` | 1.2 | Before the notices (poll, download notice, edited or deleted message) |
| `viewtopic_body_postrow_post_notices_after` | 1.2 | After the notices |
| `viewtopic_body_postrow_signature_before` | 1.4.0 | Before the signature (whether or not one is shown) |
| `viewtopic_body_postrow_signature_after` | 1.4.0 | After the signature (whether or not one is shown) |
| `viewtopic_body_postrow_post_content_footer` | 1.2 | Before the footer line |
| `phpbbmodders_newspage_footer_prepend` | 1.4.0 | At the start of the footer line, before "Read more" and the comment count |
| `phpbbmodders_newspage_footer_append` | 1.4.0 | In the footer line, after "View topic" and before the back-to-top arrow; a good place for share or like links |
| `viewtopic_body_postrow_back2top_before` | 1.4.0 | In the footer line, before the back-to-top arrow's block |
| `viewtopic_body_postrow_back2top_prepend` | 1.4.0 | Inside the back-to-top block, before the arrow |
| `viewtopic_body_postrow_back2top_append` | 1.4.0 | Inside the back-to-top block, after the arrow |
| `viewtopic_body_postrow_back2top_after` | 1.4.0 | In the footer line, after the back-to-top arrow's block |
| `viewtopic_body_postrow_post_after` | 1.2 | After the news item |

phpBB's contact-menu events (`viewtopic_body_contact_fields_*`, `viewtopic_body_contact_icon_*`) and Who is online events (`viewtopic_body_online_list_*`) aren't used: the news page has no contact menu or online list.
