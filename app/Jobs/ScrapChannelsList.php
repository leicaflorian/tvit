<?php

namespace App\Jobs;

use App\Models\Channel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use Psr\Http\Client\ClientExceptionInterface;

class ScrapChannelsList implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  
  private string $baseUrl = 'https://www.sorrisi.com/guidatv/canali-tv/';
  private string $logoUrlColor = 'https://www.sorrisi.com/guidatv/bundles/tvscnewsite/css/images/loghi/%s_c.png';
  private string $logoUrlWhite = 'https://www.sorrisi.com/guidatv/bundles/tvscnewsite/css/images/loghi/%s.png';
  
  private string $freeChannelsId = "gtv-mod1-free";
  private string $premiumChannelsId = "gtv-mod1-premium";
  private string $skyChannelsId = "gtv-mod1-sky";
  
  /**
   * @var array["free" => Channel[], "premium" => Channel[], "sky" => Channel[]]
   */
  private array $channelsList = [
    "free"    => [],
    "premium" => [],
    "sky"     => []
  ];
  
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct() {
    //
  }
  
  /**
   * @param $type
   * @param $dom
   * @param $containerId
   *
   * @return void
   */
  public function parseChannelsGroup($type, $dom, $containerId): void {
    $channelsContainer   = $dom->find("#" . $containerId);
    $channelsDOMElements = $channelsContainer->find('.gtv-logo');
    
    dump("Scanning $type channels... Found " . count($channelsDOMElements) . " elements");
    
    foreach ($channelsDOMElements as $channel) {
      $slug = Str::of(Str::match("/canali-tv\/.*/", $channel->getAttribute('href')))->remove(['canali-tv/', '/'])->trim()->toString();
      
      $newChannel = (new Channel([
        'name'           => $channel->getAttribute("title"),
        'name_slug'      => $slug,
        'page_url'       => $channel->getAttribute('href'),
        'channel_number' => (int) $channel->getAttribute('data-channel-number'),
        'logo_url_color' => sprintf($this->logoUrlColor, $slug),
        'logo_url_light' => sprintf($this->logoUrlWhite, $slug),
        'type'           => $type
      ]));
      
      $this->channelsList[$type][] = $newChannel;
      
      try {
        Channel::updateOrCreate([
          'name_slug' => $newChannel->name_slug,
          'type'      => $type
        ], $newChannel->toArray());
      } catch (Exception $e) {
        dump($e->getMessage());
      }
    }
  }
  
  /**
   * Execute the job.
   *
   * @return void
   * @throws ClientExceptionInterface|Exception
   */
  public function handle(): void {
    $dom = new Dom;
    
    $dom->loadFromUrl($this->baseUrl);
    
    $this->parseChannelsGroup("free", $dom, $this->freeChannelsId);
    $this->parseChannelsGroup("premium", $dom, $this->premiumChannelsId);
    $this->parseChannelsGroup("sky", $dom, $this->skyChannelsId);
  }
}
