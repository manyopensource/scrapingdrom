<?php

namespace App\Console\Commands;

use App\Spiders\DromAudiGenerationSpider;
use Illuminate\Console\Command;
use RoachPHP\Roach;

class ScrapeDromAudiGenerations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:drom-audi-generations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape generations of Audi from drom.ru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Roach::startSpider(DromAudiGenerationSpider::class);

        $this->info('Scraping generations of Audi completed.');

        return 0;
    }
}
