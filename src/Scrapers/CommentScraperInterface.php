<?php

declare(strict_types=1);

namespace BVP\HamanakoScraper\Scrapers;

use BVP\HamanakoScraper\ScraperContractInterface;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
interface CommentScraperInterface extends ScraperContractInterface
{
    /**
     * @param  string|int                           $raceCode
     * @param  \Carbon\CarbonInterface|string|null  $date
     * @return array
     */
    public function scrape(string|int $raceCode, CarbonInterface|string|null $date = null): array;
}
