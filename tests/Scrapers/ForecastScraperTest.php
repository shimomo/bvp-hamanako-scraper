<?php

declare(strict_types=1);

namespace BVP\HamanakoScraper\Tests\Scrapers;

use BVP\HamanakoScraper\Scrapers\ForecastScraper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class ForecastScraperTest extends TestCase
{
    /**
     * @var \BVP\HamanakoScraper\Scrapers\ForecastScraper
     */
    protected ForecastScraper $scraper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scraper = new ForecastScraper();
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(ForecastScraperDataProvider::class, 'scrapeProvider')]
    public function testScrape(array $arguments, array $expected): void
    {
        $this->assertSame($expected, $this->scraper->scrape(...$arguments));
    }

    /**
     * @return void
     */
    public function testScrapeWithRaceCode1AndDate20250104(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "BVP\HamanakoScraper\Scrapers\ForecastScraper::scrapeYesterday() - " .
            "The specified key '.z_comment' is not found in the content of the URL: " .
            "'https://www.boatrace-hamanako.jp/modules/yosou/group-yosou.php?day=20250104&race=1&kind=0'."
        );

        $this->scraper->scrape(1, '2025-01-04');
    }

    /**
     * @return void
     */
    public function testThrowsExceptionWhenMethodDoesNotExist(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            "BVP\HamanakoScraper\Scrapers\BaseScraper::__call() - " .
            "Call to undefined method 'BVP\HamanakoScraper\Scrapers\BaseScraper::ghost()'."
        );

        $this->scraper->ghost(1, '2025-01-04');
    }
}
