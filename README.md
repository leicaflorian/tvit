# Tvit

Italian tv EPG scrapper and ITPV list generator

This project is made by two parts:

- **m3u8** list containing main italian tv channels that can be viewed with [vlc](https://www.vlc.org/) or any other
  IPTV player
- **epg** list containing the replative EPG for each channel provided by the list mentioned above

## m3u8 list

The list is generated automatically and each channel link returns a m3u8 stream.
Almost all channel are available from any country. Some are only accessible from Italy. To avoid thi block use a VPN
located in Italy.

Link M3U: [`https://tvit.leicaflorianrobert.dev/iptv/list.m3u`](https://tvit.leicaflorianrobert.dev/iptv/list.m3u)

Link JSON: [`https://tvit.leicaflorianrobert.dev/iptv/list.json`](https://tvit.leicaflorianrobert.dev/iptv/list.json)

## Epg list

This list is generated by scraping webpages that contain the EPG for each channel and for each program.

The list is generated daily and is updated every day at midnight.

Link XML: [`https://tvit.leicaflorianrobert.dev/epg/list.xml`](https://tvit.leicaflorianrobert.dev/epg/list.xml)

Link JSON: [`https://tvit.leicaflorianrobert.dev/epg/list.json`](https://tvit.leicaflorianrobert.dev/epg/list.json)

> ❗️ Attention
>
> All program times are in UTC format.

# How to use

If you're using a dedicated software, you have to configure the m3uList and the epgList.

If you're using a browser, just
visit [https://tvit.leicaflorianrobert.dev/channels](https://tvit.leicaflorianrobert.dev/channels)

# How to run locally

### Requirements

- php >= 8.1
- composer
- docker

### Steps

- run `composer install`
- run `./vendor/bin/sail up -d`
- run `cp .env.example .env`
- run `./vendor/bin/sail artisan key:generate`
- run `./vendor/bin/sail artisan db:migrate`
- run `./vendor/bin/sail artisan db:seed`
- the app should be reachable on http://localhost:8082

### For the frontend part

- run `./vendor/bin/sail npm install`
- run `./vendor/bin/sail npm run dev`

### Epg scrapping
- trigger scrapping jobs by `./vendor/bin/sail artisan job:dispatch ScrapChannelsContent`
- execute queued works for scrapping each channel epg by `./vendor/bin/sail artisan queue:work --queue scrap-channels`
- execute queued works for scrapping each program details by `./vendor/bin/sail artisan queue:work --queue scrap-program-detail`

# UI ideas

- https://www.domestika.org/it/projects/293989-smart-tv-ui-ux
- https://dribbble.com/shots/20043473-IPTV-Live-TV-s-Page
- https://cdn.dribbble.com/users/1472775/screenshots/5978437/media/126437c33d857249089942c869be65b1.jpg?compress=1&resize=1200x900&vertical=top
- https://cdn.dribbble.com/users/954552/screenshots/4618265/media/92c156345449eddcf79cb935b4056231.png
