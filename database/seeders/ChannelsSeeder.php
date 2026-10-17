<?php

namespace Database\Seeders;

use App\Classes\ChannelsConfigCollection;
use App\Classes\ChannelsListCollection;
use App\Classes\ChannelsNumberCollection;
use App\Models\Channel;
use App\Models\ChannelGroup;
use Illuminate\Database\Seeder;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;
use Psr\Http\Client\ClientExceptionInterface;


class ChannelsSeeder extends Seeder {

    /**
     * @return ChannelsNumberCollection
     */
    private function getChannelsDttNumbers(): ChannelsNumberCollection {
        $toReturn = new ChannelsNumberCollection();
        $dom      = new Dom();

        try {
            $dom->loadFromUrl("https://www.punto-informatico.it/digitale-terrestre-numerazione-canali/");
            $channelElements = $dom->find(".post-content-single > ul > li");

            dump("Scanning channels dtt numbers... Found " . count($channelElements) . " elements");

            foreach ($channelElements as $channel) {
                $name   = trim($channel->text());
                $number = trim($channel->find("span")->text());

                if (!$name || !$number) {
                    continue;
                }

                $toReturn->push([
                    'number' => (int)$number,
                    'name'   => $name
                ]);
            }
        } catch (ChildNotFoundException|CircularException|ContentLengthException|LogicalException|StrictException|ClientExceptionInterface|NotLoadedException $e) {
            dump($e);
        }


        return $toReturn;
    }

    /**
     * @return ChannelsListCollection
     */
    private function getChannelsList(): ChannelsListCollection {
        $channelsList = new ChannelsListCollection([]);
        $dom          = new Dom();

        try {
            $dom->loadFromUrl("https://www.superguidatv.it/canali/");

            // Each channel is a link to its guide (".../guida-programmi-tv-rai-1/217/") wrapping the logo
            foreach ($dom->find('a') as $domLink) {
                $guideLink = $domLink->getAttribute('href') ?? '';

                if ( !preg_match("/programmi-tv-(.*)\/(\d+)/", $guideLink, $match)) {
                    continue;
                }

                $domImg = $domLink->find('img');

                if ( !count($domImg) || $channelsList->contains("tvg_code", (int)$match[2])) {
                    continue;
                }

                $channelsList->push([
                    'name'     => trim($domImg[0]->getAttribute("alt")),
                    'slug'     => trim($match[1]),
                    "tvg_code" => (int)$match[2]
                ]);
            }

            dump("Scanning channels... Found " . $channelsList->count() . " elements");
        } catch (ChildNotFoundException|CircularException|ContentLengthException|LogicalException|StrictException|ClientExceptionInterface|NotLoadedException $e) {
            dump($e);
        }

        return $channelsList;

    }


    /**
     * @return ChannelsConfigCollection
     */
    private function getChannelsConfig(): ChannelsConfigCollection {
        dump("Getting internal channels config");

        $data = array_merge(...array_values(config('channels')));

        usort($data, function ($item1, $item2) {
            return $item1['dvbNum'] <=> $item2['dvbNum'];
        });

        return new ChannelsConfigCollection($data);
    }

    /**
     * @param mixed $channel
     * @param string $version dark|light
     *
     * @return string
     */
    private function getLogoUrl(mixed $channel, string $version = 'dark'): string {
        if (!$channel) {
            return "";
        }

        $logo = "https://api.superguidatv.it/v1/channels/#id/logo?width=120&theme={$version}";

        if (str_starts_with($channel["tvg_code"], "http")) {
            return $channel["tvg_code"];
        }

        return str_replace("#id", $channel["tvg_code"], $logo);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void {
        $channelsConfig = $this->getChannelsConfig();
        $channelsList   = $this->getChannelsList();
        // Outdated numbering: the manual one in config/channels is better
        //    $channelsNumbers = $this->getChannelsDttNumbers();

        $finalList = collect([]);
        foreach ($channelsConfig as $channelConfig) {
            // if ($channelConfig["id"] !== "warner-tv") {
            //     continue;
            // }

            $channelData = $channelsList->filter(function ($value) use ($channelConfig) {
                $name = $channelConfig['tvgName'] ? $channelConfig['tvgName'] : $channelConfig['name'];

                $a = str_replace(" ", "", strtolower($value['name']));
                $b = str_replace(" ", "", strtolower($name));

                return $a == $b;
            })->first();

            // Not found by name: fall back to the SuperGuidaTV id stored in the config
            if ( !$channelData && is_numeric($channelConfig['tvgLogo'] ?? null)) {
                $channelData = [
                    'name'     => $channelConfig['name'],
                    'slug'     => $channelConfig['id'],
                    'tvg_code' => (int)$channelConfig['tvgLogo'],
                ];
            }


            $newChannel = [
                'name'           => $channelConfig["name"] ?? '',
                'tvg_slug'       => $channelData['slug'] ?? $channelConfig["id"] ?? '',
                'tvg_name'       => $channelData['name'] ?? '',
                'tvg_code'       => $channelData['tvg_code'] ?? '',
                'iptv_code'      => $channelConfig["code"] ?? '',
                'logo_url_color' => $this->getLogoUrl($channelData, 'dark') ?? '',
                'logo_url_light' => $this->getLogoUrl($channelData, 'light') ?? '',
                'dtt_num'        => $channelConfig["dvbNum"] ?? '',
                'group'          => $channelConfig["groupTitle"] ?? '',
                'group_id'       => ChannelGroup::where("slug", $channelConfig["groupTitle"])->first()->id
            ];

            // dump($channelConfig, $channelData, $newChannel);
            dump($newChannel);

            $finalList->push(Channel::updateOrCreate([
                "tvg_slug" => $newChannel['tvg_slug']
            ], $newChannel));
        }

        //    dump($finalList->toArray());
    }
}
