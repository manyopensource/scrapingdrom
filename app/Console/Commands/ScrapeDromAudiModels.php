<?php

namespace App\Console\Commands;

use App\Spiders\DromAudiModelSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class ScrapeDromAudiModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:drom-audi-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape models of Audi from drom.ru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Roach::startSpider(DromAudiModelSpider::class);

        $this->info('Scraping models of Audi completed.');

        return 0;
    }
}
