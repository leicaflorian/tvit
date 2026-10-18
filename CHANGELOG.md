# Changelog

Notable changes to TvIt, newest first. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). TvIt has no version numbers: every push to `main` deploys to production, so entries are grouped by date. New changes go under *Unreleased* and get a date when they reach `main`.

## [Unreleased]

### Added

- The M3U playlist header carries `url-tvg` pointing to `/epg/list.xml`, so most players load the guide without the user adding it by hand.

### Fixed

- The XMLTV guide uses the playlist's `tvg-id` (e.g. `rai-1`) as channel id, so players that match by `tvg-id` show the guide; programme times carry an explicit `+0000` offset. Players where the guide was mapped by hand to the old ids (e.g. `Rai 1`) need to map it again.

## 2026-10-17

### Added

- Grid TV guide page (`/guida`).
- Player: channel zapping, keyboard shortcuts legend, IPTV links menu, unified controls and now-playing indicator.
- Channel list: compact layout with search and group filters; channel preview redesigned as a hero panel.
- Geo-block state for Rai channels (451 `geo_blocked` from outside Italy) and a generic message when a channel fails to load.
- App icons, web manifest and navbar logo; SEO meta tags, Open Graph image and sitemap.
- Production Docker setup for Coolify (`Dockerfile`, `docker-compose.prod.yml`).
- `CLAUDE.md` and the Superpowers plugin for Claude Code.

### Changed

- Rai channels are served through the official RaiPlay relinker.
- Discovery channels moved to the new simulcast source.
- Program list panel redesigned; now-on-air data refreshes.
- `APP_URL` comes from `SERVICE_URL_APP`, so links keep the https scheme; Traefik port pinned to 8080.
- Carbon updated to 2.73; tests run on in-memory SQLite.
- Code comments translated to English.

### Fixed

- SuperGuidaTV scraper and channel seeder for the new site markup.
- La7, TV8, Cielo, Antena 1, ProTV and TeleNorba streams.
- La7d and Warner TV guides, now seeded from their SuperGuidaTV ids.
- UI layout bugs and the empty state when a channel has no EPG.
- `CleanOldPrograms` is now scheduled (daily at 02:30); test suite fixed.

## 2025

### Added

- TeleNorba regional channel and a few other channels.

### Removed

- Pluto TV channels.

### Fixed

- A routing bug.

### Changed

- Guzzle HTTP client updated.

## 2024

### Added

- ProTV and Antena channels with their own scraper.
- Channel scraping runs as queued jobs.

### Changed

- Discovery and Sky channels updated.
- Mobile navigation optimized; `APP_URL` moved to the config.

### Fixed

- Scheduled scraping jobs (including Pluto TV).

## 2023

### Added

- A few Romanian channels, La7, a Pluto TV channel and its scraper.
- Links to the playlists and other useful links in the web app.

### Changed

- Sky channels and the Pluto TV scraper go through a proxy, with logs for proxied calls.
- WebServerOne channels updated; Antena 1 activated.

### Fixed

- Pages when no EPG is available, Docker config, EPG XML generator, channel seeder.

### Security

- Secret data no longer exposed.

## 2022

### Added

- First release: M3U playlist of Italian channels and the channels page.
- Stream links rewritten to go through the app (`/{group}/{tvg_slug}/stream.m3u8`).
- EPG scraped from SuperGuidaTV, with program details and images, served as XML.
- Web app on Inertia and Vue 3.
- Daily scheduler for scraping and a job that cleans old programs.

### Fixed

- Rai channels for the Italy region, Mediaset and DMAX EPG links, tvg ids and names, timezone bugs.
