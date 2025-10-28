# WP Minimal Consent (GTM + Consent Mode v2)

Minimal WordPress CMP plugin integrating **Google Consent Mode v2** with a lightweight **cookie banner**, **GTM compatibility** and **code-only configuration** (no admin UI). Advanced Mode defaults, regional denied, wait_for_update, and persistent preferences.

## Features

- Consent Mode v2 (ad_storage, analytics_storage, ad_user_data, ad_personalization)
- Advanced Mode defaults before GTM
- Regional defaults (EEE/UK/CH)
- Banner + granular panel (Analytics/Marketing)
- Persistent preferences + floating “Preferences” button
- Optional debug logs (`WPMC_DEBUG`)

## Requirements

- WordPress 5.9+ / PHP 7.4+
- Google Tag Manager container (Web)
- (Optional) GA4 test property

## Install

1. Copy the folder `wp-minimal-consent` into `wp-content/plugins/`.
2. Activate the plugin in **WP → Plugins**.
3. Edit constants at the top of `wp-minimal-consent.php`:
   - `WPMC_GTM_ID`: your GTM container ID
   - texts, position, `WPMC_DEBUG`, etc.

## How it works

- Sets `consent default` (denied + region + wait_for_update) **before** GTM.
- Restores user choice from `localStorage` and sends `consent update`.
- Banner: Accept / Reject shortcuts.
- Preferences panel: toggles map to:
  - Analytics → `analytics_storage`
  - Marketing → `ad_storage`, `ad_user_data`, `ad_personalization`

## Dev / Debug

- Enable `WPMC_DEBUG` to print every `consent default/update` on console.
- Test with Local (WP Engine) **Live Links** + GTM Preview.

## Roadmap

- i18n, A11y enhancements, categories fine-grain, basic-mode option.
