<?php

namespace App\Spiders;

use App\Models\Model;
use App\ItemPipelines\SaveGenerationsPipeline;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use Symfony\Component\DomCrawler\Crawler;

class DromAudiGenerationSpider extends BasicSpider
{
    public array $startUrls = [
        //
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        SaveGenerationsPipeline::class,
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $generations = $response->filter('div[data-ga-stats-name=generations_outlet_item]')
            ->children('a[data-ftid=component_article]');

        foreach ($generations as $value) {
            $generation = new Crawler($value);
            $marketText = $generation->ancestors('.e1ei9t6a4')->children('.e1ei9t6a3')->text();
            $market = substr($marketText, strpos($marketText, 'для'));
            $name = $generation->filter('[data-ftid=component_article_caption]')->text();
            $genPeriod = explode(', ', $generation->filter('[data-ftid=component_article_extended-info] > div')->eq(0)->text());
            $period = $genPeriod[1] ?? '';
            $gen = str_replace(' поколение', '', $genPeriod[0]) ?? '';
            $image = $generation->filter('.evrha4s0')->attr('src');
            $url = $response->getUri() . $generation->attr('href');
            yield $this->item([
                'market' => $market,
                'name' => $name,
                'period' => $period,
                'gen' => $gen,
                'image' => $image,
                'url' => $url,
            ]);
        }
    }

    /** @return Request[] */
    protected function initialRequests(): array
    {
        $modelUrls = Model::pluck('url')->toArray();

        $requests = [];

        foreach ($modelUrls as $modelUrl) {
            $requests[] = new Request(
                'GET',
                $modelUrl,
                [$this, 'parse']
            );
        }

        return $requests;
    }

}
