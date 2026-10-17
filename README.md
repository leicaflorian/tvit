# Tvit

Italian TV EPG scraper and IPTV list generator.

The project is made of two parts:

- an **M3U playlist** with the main Italian TV channels, playable with [VLC](https://www.videolan.org/vlc/) or any
  other IPTV player
- an **EPG** (electronic program guide) with the schedule of each channel in the playlist

## M3U playlist

The playlist is generated automatically and each channel link returns an M3U8 stream.
Almost all channels can be watched from any country; a few are geo-blocked and only available from Italy. To get around
this, use a VPN located in Italy.

| Format | Link                                                                                                    |
|--------|---------------------------------------------------------------------------------------------------------|
| M3U    | [`https://tvit.leicaflorianrobert.dev/iptv/list.m3u`](https://tvit.leicaflorianrobert.dev/iptv/list.m3u)   |
| JSON   | [`https://tvit.leicaflorianrobert.dev/iptv/list.json`](https://tvit.leicaflorianrobert.dev/iptv/list.json) |

## EPG

The EPG is built by scraping web pages that list the schedule of each channel and the details of each program.
It is refreshed once a day, at 03:00 (Europe/Rome).

| Format | Link                                                                                                  |
|--------|-------------------------------------------------------------------------------------------------------|
| XMLTV  | [`https://tvit.leicaflorianrobert.dev/epg/list.xml`](https://tvit.leicaflorianrobert.dev/epg/list.xml)   |
| JSON   | [`https://tvit.leicaflorianrobert.dev/epg/list.json`](https://tvit.leicaflorianrobert.dev/epg/list.json) |

> [!IMPORTANT]
> All program times are in UTC.

## How to use

- **IPTV player**: add the M3U link as the playlist and the XML link as the EPG source.
- **Browser**: open [https://tvit.leicaflorianrobert.dev/channels](https://tvit.leicaflorianrobert.dev/channels).

## Running locally

### Requirements

- PHP >= 8.1
- Composer
- Docker

### Setup

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

The app is then available at http://localhost:8082.

### Frontend

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### EPG scraping

```bash
# Dispatch the scraping jobs
./vendor/bin/sail artisan job:dispatch ScrapChannelsContent

# Scrape the schedule of each channel
./vendor/bin/sail artisan queue:work --queue scrap-channels

# Scrape the details of each program
./vendor/bin/sail artisan queue:work --queue scrap-program-detail
```

The daily scheduled scraping only runs when `APP_ENV=production`.

## UI references

- https://www.domestika.org/it/projects/293989-smart-tv-ui-ux
- https://dribbble.com/shots/20043473-IPTV-Live-TV-s-Page
- https://cdn.dribbble.com/users/1472775/screenshots/5978437/media/126437c33d857249089942c869be65b1.jpg
- https://cdn.dribbble.com/users/954552/screenshots/4618265/media/92c156345449eddcf79cb935b4056231.png
