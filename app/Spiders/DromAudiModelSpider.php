<?php

namespace App\Spiders;

use App\ItemPipelines\SaveModelsPipeline;
use Generator;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

class DromAudiModelSpider extends BasicSpider
{
    public array $startUrls = [
        'https://www.drom.ru/catalog/audi/',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        SaveModelsPipeline::class,
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
        $models = $response
            ->filter('div[data-ftid=component_cars-list] > div')
            ->children('a')
            ->extract(['_text', 'href']);

        foreach ($models as $model) {
            yield $this->item([
                'name' => $model[0],
                'url' => $model[1],
            ]);
        }
    }
}
