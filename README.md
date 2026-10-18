# TvIt

![TvIt, TV italiana in diretta e guida programmi](public/og-image.png)

M3U playlist of Italian TV channels with an XMLTV EPG (electronic program guide) and a web player, at
[tvit.leicaflorianrobert.dev](https://tvit.leicaflorianrobert.dev).

- an **M3U playlist** with the main Italian free-to-air channels (and a few Romanian ones), playable with
  [VLC](https://www.videolan.org/vlc/) or any other IPTV player
- an **EPG** with the schedule of every channel in the playlist, refreshed daily
- a **web player** with the channel list and a TV guide

## Channels

| Group     | Channels                                                                                                                                                         |
|-----------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Rai       | Rai 1, Rai 2, Rai 3, Rai 4, Rai 5, Rai Movie, Rai Premium, Rai Gulp, Rai Yoyo, Rai News 24, Rai Storia, Rai Sport, Rai Sport +, Rai Scuola, Rai Radio 2          |
| Mediaset  | Rete 4, Canale 5, Italia 1, Italia 2, Mediaset 20, La 5, Iris, Focus, Mediaset Extra, Top Crime, Boing, Cartoonito, Tgcom 24, Cine34, Twenty Seven, Radio 101, Radio 105 |
| La7       | La7, La7d                                                                                                                                                        |
| Sky       | TV8, Cielo                                                                                                                                                       |
| Discovery | Nove, DMAX, Real Time, Warner TV, Giallo, K2, Frisbee                                                                                                            |
| Regional  | Tele Norba                                                                                                                                                       |
| Romanian  | Pro TV, Comedy Play, Antena 1, Antena 3, Antena Monden                                                                                                           |

The up-to-date list, with the DTT numbers, is the [JSON playlist](https://tvit.leicaflorianrobert.dev/iptv/list.json).

## M3U playlist

Each channel link returns an M3U8 stream. The playlist header (`url-tvg`) points to the EPG, so most players load
the guide on their own.

| Format | Link                                                                                                       |
|--------|------------------------------------------------------------------------------------------------------------|
| M3U    | [`https://tvit.leicaflorianrobert.dev/iptv/list.m3u`](https://tvit.leicaflorianrobert.dev/iptv/list.m3u)   |
| JSON   | [`https://tvit.leicaflorianrobert.dev/iptv/list.json`](https://tvit.leicaflorianrobert.dev/iptv/list.json) |

Add `?logoVersion=light` to the M3U link to get the light version of the channel logos.

> [!NOTE]
> The **Rai** channels can only be watched from Italy: from abroad their stream answers HTTP 451 (`geo_blocked`) and
> the player shows nothing. Use a VPN located in Italy. All the other channels can be watched from any country.

## EPG

The schedule of each channel and the details of each program are scraped from
[SuperGuidaTV](https://www.superguidatv.it) every day at 03:00 (Europe/Rome). The guide lists the programs from today
on; programs that ended more than two days ago are deleted.

| Format | Link                                                                                                     |
|--------|----------------------------------------------------------------------------------------------------------|
| XMLTV  | [`https://tvit.leicaflorianrobert.dev/epg/list.xml`](https://tvit.leicaflorianrobert.dev/epg/list.xml)   |
| JSON   | [`https://tvit.leicaflorianrobert.dev/epg/list.json`](https://tvit.leicaflorianrobert.dev/epg/list.json) |

> [!IMPORTANT]
> All program times are in **UTC**. In the XMLTV file they have no offset, which the format reads as UTC, so players
> show them in your local time. If you read the JSON yourself, convert them (Italy is UTC+1, UTC+2 in summer).

## How to use

- **Browser**: open the [channel list](https://tvit.leicaflorianrobert.dev/channels) to watch a channel, or the
  [TV guide](https://tvit.leicaflorianrobert.dev/guida) to see what's on.
- **VLC**: *Media → Open Network Stream* and paste the M3U link. VLC plays the channels but doesn't show the guide.
- **TiviMate, IPTV Smarters and similar apps**: add a playlist from URL with the M3U link. The guide is picked up from
  the playlist; if it isn't, add the XMLTV link as the EPG source.
- **Kodi**: install the *PVR IPTV Simple Client* add-on and set the M3U link as the playlist and the XMLTV link as the
  EPG.

## Stack

- **Backend**: [Laravel 9](https://laravel.com) on PHP 8.1, MySQL, queued jobs for the scraping
- **Frontend**: [Inertia](https://inertiajs.com) + [Vue 3](https://vuejs.org), built with Vite
- **Production**: Docker image deployed on [Coolify](https://coolify.io), with separate containers for the app, the
  scheduler and the queue workers

How channels, streams and scraping fit together is described in [CLAUDE.md](CLAUDE.md#architecture-in-short).

## Running locally

### Requirements

- Docker
- PHP >= 8.1 and Composer on the host, only for the first `composer install` (Sail, which runs everything else, is a
  Composer package)
- An internet connection: the seeders read the channel list from superguidatv.it

### Setup

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

The app is then available at http://localhost:8082.

### EPG scraping

```bash
# Dispatch the scraping jobs
./vendor/bin/sail artisan job:dispatch ScrapChannelsContent

# Scrape the schedule of each channel
./vendor/bin/sail artisan queue:work --queue scrap-channels

# Scrape the details of each program
./vendor/bin/sail artisan queue:work --queue scrap-program-detail
```

The daily scheduled scraping only runs when `APP_ENV=production`. So does the cleanup of programs that ended more than
two days ago (`CleanOldPrograms`, daily at 02:30).

### Tests

```bash
./vendor/bin/sail test   # or `php artisan test` on the host
```

Tests run on an in-memory SQLite database (`phpunit.xml`), so they need neither Docker nor MySQL.

## Changelog

What changed, and when, is in [CHANGELOG.md](CHANGELOG.md).

## Disclaimer

TvIt doesn't host or retransmit any content: the playlist links to the streams the broadcasters publish on their own
websites, and the rights to the channels and their programs belong to the broadcasters. The program guide comes from
SuperGuidaTV. If you own one of these channels and want it removed, open an issue.

## License

[MIT](LICENSE)
