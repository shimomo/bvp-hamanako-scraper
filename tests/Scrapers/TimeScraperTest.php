<?php

declare(strict_types=1);

namespace BVP\HamanakoScraper\Tests\Scrapers;

use BVP\HamanakoScraper\Scrapers\TimeScraper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TimeScraperTest extends TestCase
{
    /**
     * @var \BVP\HamanakoScraper\Scrapers\TimeScraper
     */
    protected TimeScraper $scraper;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scraper = new TimeScraper();
    }

    /**
     * @param  array  $arguments
     * @param  array  $expected
     * @return void
     */
    #[DataProviderExternal(TimeScraperDataProvider::class, 'scrapeProvider')]
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
            "BVP\HamanakoScraper\Scrapers\TimeScraper::scrape() - " .
            "The specified key '.com-rname' is not found in the content of the URL: " .
            "'https://www.boatrace-hamanako.jp/modules/yosou/group-cyokuzen.php?day=20250104&race=1&kind=2'."
        );

        $this->scraper->scrape(1, '2025-01-04');
    }

    /**
     * @return void
     */
    public function testInvalidWithRaceCode1AndDate20250104(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            "BVP\HamanakoScraper\Scrapers\BaseScraper::__call() - " .
            "Call to undefined method 'BVP\HamanakoScraper\Scrapers\BaseScraper::ghost()'."
        );

        $this->scraper->ghost(1, '2025-01-04');
    }
}
