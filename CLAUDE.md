# CLAUDE.md

TvIt: an M3U playlist of Italian (and a few Romanian) TV channels, an EPG scraped from SuperGuidaTV and a web player, at [tvit.leicaflorianrobert.dev](https://tvit.leicaflorianrobert.dev). Laravel 9 (PHP 8.1) + Inertia + Vue 3. `README.md` covers what the app serves and how to run it locally: read it first. This file adds the rules for working on it.

## Superpowers

**Superpowers comes before anything else** (owner's rule, 13/10/2026). At the start of every conversation, and before every answer or action (even a clarifying question), invoke the `superpowers:using-superpowers` skill and follow the Superpowers skills that apply to the task. The plugin is installed in Claude Code (`/plugin`) and enabled for this project in `.claude/settings.json`, which is versioned so a cloud session finds it too. How it is used here (ToDo items, worktrees, TDD) is in *Superpowers in this project*, below.

## Language

- **Everything in the repository is in English**: code, comments, `README.md`, this file, commit messages. When you touch a file that still has Italian comments, translate the ones you touch.
- **The documentation in the vault is in Italian.**
- Texts shown to users in the web app (labels, messages such as the geo-block error) stay in Italian: the audience is Italian.

## Documentation

The documentation lives in the Obsidian vault on Google Drive, not in this repository (apart from `README.md` and this file):

`/Users/florianleica/Library/CloudStorage/GoogleDrive-florian.leica@gmail.com/Il mio Drive/MyVault/Projects/TvIt`

- `_index.md` is the entry point: sections, and *Come sono scritte le note*.
- `01 - Documentazione funzionale`: what the user sees (playlists, channels page, TV guide).
- `02 - Documentazione tecnica`: how it works (architecture, channels and groups, streams, EPG scraping, frontend, local environment and tests, production).
- `ToDo`: the things to do, one folder per item.
- The infrastructure (Coolify, Droplet, shared MySQL, DNS) is documented for all projects in `MyVault/Infrastruttura`; the TvIt notes link to it instead of repeating it.

Rules for the notes (from the vault, re-read `_index.md` before writing): no `#` title at the top except in `_index` notes; at the top of the text only the `←` link back to the folder's `_index`; links to nearby notes go in the properties (`funzioni`, `tecnica`), quoted; every folder has an `_index` listing its notes, and a new note is added to it. An existing note is edited by replacement and re-read right before: other agents and the owner work in the vault too. Never put secrets in the notes.

To let Claude Code read the vault without prompts, add the `Projects/TvIt`, `Infrastruttura` and `Template` folders to `permissions.additionalDirectories` in `.claude/settings.local.json` (personal, not versioned: the path changes from one computer to another).

"Update the documentation" means the vault **and** `README.md`. Before a commit, check that the README and the vault notes still describe what the committed code does.

## Superpowers in this project

- **ToDo items**: new features and ideas are items of `ToDo/` in the vault folder. `brainstorming` writes `ToDo/_Opened/<Title>/Analisi.md` and `writing-plans` writes `ToDo/_Opened/<Title>/Piano.md`, from the templates `MyVault/Template/Template - Analisi.md` and `Template - Piano d'azione.md`, in Italian. In the templates the `←` line points to `[[Projects/TvIt/ToDo/_index|ToDo]]`, and the properties `aree`, `concetti`, `funzioni` are replaced by `progetti` (here `tvit`) and `tecnica`. Do not use `docs/superpowers/`. Product choices go in the *Da decidere* section of the analysis, each with a recommendation and a `**Risposta:**` line, all together rather than one question at a time; `writing-plans` starts only when every answer is there. During development tick the steps of `Piano.md` and update `stato` (`in corso`, then `da controllare`). A finished item moves to `_Done` only after the owner's check (rules in `ToDo/_index.md`). Vault notes are not in git and are never committed. If the vault can't be read (e.g. in a cloud session), stop and say so.
- **Worktrees**: `using-git-worktrees` creates the worktree in `.worktrees/` (git-ignored), on a new branch from `main`. `finishing-a-development-branch` merges it back into `main` locally. No push and no pull request unless asked: **a push to `main` deploys to production** (GitHub webhook → Coolify). The worktree lacks unversioned files: copy `.env` and run `composer install` and `npm install` before testing.
- **TDD**: test first for backend logic: stream controllers (relinker/redirect handling, playlist rewriting), scrapers (parse saved HTML fixtures, never the live site), EPG/M3U output, guide day calculation. Fake outbound calls with `Http::fake()` (see `tests/Feature/RaiPlayStreamTest.php`). There are no frontend tests: check UI changes in the browser and say what you checked. Whether a stream really plays is checked by the owner (some channels are geo-blocked outside Italy). This is what `verification-before-completion` means here.
- **Before a commit**: tests green, the documentation updated (see *Documentation*) and the change listed under *Unreleased* in `CHANGELOG.md` (user-visible changes only). A release moves *Unreleased* to a `YYYY.MM.DD` section, tags it and creates the GitHub release with that section as notes.

## Commands

```bash
./vendor/bin/sail up -d                 # app on http://localhost:8082
./vendor/bin/sail npm run dev           # Vite
./vendor/bin/sail test                  # PHPUnit (php artisan test works on the host too)
./vendor/bin/sail artisan db:seed       # channel groups and channels (reads superguidatv.it)
./vendor/bin/sail artisan job:dispatch ScrapChannelsContent
./vendor/bin/sail artisan queue:work --queue scrap-channels
./vendor/bin/sail artisan queue:work --queue scrap-program-detail
```

Production runs PHP 8.1: when updating dependencies run `composer` inside Sail (`./vendor/bin/sail composer …`) so the lock stays installable on 8.1. Tests run on an in-memory SQLite database (`phpunit.xml`); tests that touch the database use `RefreshDatabase`. The migrations must keep working on both MySQL and SQLite.

## Architecture in short

- **Channels** (`channels`) are seeded from `config/channels/*.php` (one file per group, with SuperGuidaTV id, stream code and DTT number) matched to the SuperGuidaTV channel list, plus `ExtraChannelsSeeder` and `RegionaliSeeder`. `tvg_slug` is the key: programs reference it (`channel_tvg_slug`) and stream URLs use it.
- **Streams**: `Channel::m3u8Link` is `APP_URL/{group}/{tvg_slug}/stream.m3u8`, served by the group's controller (`RaiPlayController`, `MediasetController`, …) using the `App\Traits\ChannelController` trait. Most controllers redirect to the broadcaster's playlist (proxying from the server would hit geo-blocks); a few rewrite the playlist and proxy the segments. Rai goes through the RaiPlay relinker, which answers only to Italian IPs: when `RAI_RELINKER_PROXY` is set (production) that call goes through an Italian proxy; a non-Italian caller gets 451 `geo_blocked`, an unreachable relinker or proxy 502.
- **EPG**: the scheduler dispatches `ScrapChannelsContent` daily at 03:00 Europe/Rome (production only), which queues `ScrapSingleChannel` per channel on `scrap-channels`; the group's `scrapper` picks the class in `app/Jobs/Scrappers` (default `SuperGuidaTv`). Each program with a detail link queues `ScrapProgramDetail` on `scrap-program-detail`. `CleanOldPrograms` deletes programs that ended more than two days ago, daily at 02:30 (production only). **Program times are stored in UTC**; the site's times are Europe/Rome.
- **Outputs**: `/iptv/list.m3u|json` (`IptvController`), `/epg/list.xml|json` (`EpgController`), pages `/channels` and `/guida` (Inertia, `resources/js/Pages`).
- **Production**: `Dockerfile` + `docker-compose.prod.yml` on Coolify: `app`, `scheduler` and one worker per queue, sharing one image; DB on the shared MySQL. Details in the vault.

## Code conventions

- Follow the surrounding style: most PHP files use 2-space indent and `function () {` on the same line; some (e.g. `MediasetController`) use 4 spaces. Match the file you edit.
- Short comments explaining *why* (as in `RaiPlayController`), in English.
- A scraper that finds nothing must throw (see `SuperGuidaTvScrapper`), so a markup change on the source site shows up as failed jobs instead of an empty guide.
