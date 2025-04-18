<?php

declare(strict_types=1);

namespace BVP\HamanakoScraper\Scrapers;

use BVP\ScraperCore\Normalizer;
use BVP\ScraperCore\Scraper;
use Carbon\CarbonImmutable as Carbon;
use Carbon\CarbonInterface;

/**
 * @author shimomo
 */
class ForecastScraper extends BaseScraper
{
    /**
     * @param  string|int                           $raceNumber
     * @param  \Carbon\CarbonInterface|string|null  $raceDate
     * @return array
     */
    public function scrape(string|int $raceNumber, CarbonInterface|string|null $raceDate = null): array
    {
        return array_merge(...[
            $this->scrapeYesterday($raceNumber, $raceDate),
            $this->scrapeToday($raceNumber, $raceDate),
        ]);
    }

    /**
     * @param  string|int                           $raceNumber
     * @param  \Carbon\CarbonInterface|string|null  $raceDate
     * @return array
     *
     * @throws \RuntimeException
     */
    private function scrapeYesterday(string|int $raceNumber, CarbonInterface|string|null $raceDate = null): array
    {
        $raceDate = Carbon::parse($raceDate ?? 'today')->format('Ymd');
        $crawlerUrl = sprintf($this->baseUrl, 'yosou', $raceDate, $raceNumber, '&kind=0');
        $crawler = Scraper::getInstance()->request('GET', $crawlerUrl);
        $forecasts = Scraper::filterByKeys($crawler, [
            '.z_comment',
            '.z_sinnyu > .sinnyu_zlist',
            '.z_focus > .focus_zlist > li',
            '.j_sinnyu > .sinnyu_jlist',
            '.j_focus > .focus_jlist > li',
            '.j_reliability',
        ]);

        foreach ($forecasts as $key => $value) {
            if (empty($value)) {
                throw new \RuntimeException(
                    __METHOD__ . "() - The specified key '{$key}' is not found " .
                    "in the content of the URL: '{$crawlerUrl}'."
                );
            }
        }

        $reporterYesterdayCommentLabel = '記者予想 前日コメント';
        $reporterYesterdayCourseLabel = '記者予想 前日コース';
        $reporterYesterdayFocusLabel = '記者予想 前日フォーカス';
        $reporterYesterdayFocusExactaLabel = '記者予想 前日フォーカス 2連単';
        $reporterYesterdayFocusTrifectaLabel = '記者予想 前日フォーカス 3連単';

        $reporterYesterdayComment = Normalizer::normalize($forecasts['.z_comment'][0]);
        $reporterYesterdayCourse = Normalizer::normalize($forecasts['.z_sinnyu > .sinnyu_zlist'][0], ['shouldRemoveAllSpaces' => true]);
        $reporterYesterdayFocus = Normalizer::normalize($forecasts['.z_focus > .focus_zlist > li']);
        $reporterYesterdayFocusExacta = array_values(array_filter($reporterYesterdayFocus, function ($focus) {
            return (substr_count($focus, '-') + substr_count($focus, '=')) === 1;
        }));
        $reporterYesterdayFocusTrifecta = array_values(array_filter($reporterYesterdayFocus, function ($focus) {
            return (substr_count($focus, '-') + substr_count($focus, '=')) === 2;
        }));

        $jlcYesterdayCourseLabel = 'JLC予想 前日コース';
        $jlcYesterdayFocusLabel = 'JLC予想 前日フォーカス';
        $jlcYesterdayFocusExactaLabel = 'JLC予想 前日フォーカス 2連単';
        $jlcYesterdayFocusTrifectaLabel = 'JLC予想 前日フォーカス 3連単';
        $jlcYesterdayReliabilityLabel = 'JLC予想 前日信頼度';

        $jlcYesterdayCourse = Normalizer::normalize($forecasts['.j_sinnyu > .sinnyu_jlist'][0]);
        $jlcYesterdayFocus = Normalizer::normalize($forecasts['.j_focus > .focus_jlist > li']);
        $jlcYesterdayFocusExacta = array_values(array_filter($jlcYesterdayFocus, function ($focus) {
            return (substr_count($focus, '-') + substr_count($focus, '=')) === 1;
        }));
        $jlcYesterdayFocusTrifecta = array_values(array_filter($jlcYesterdayFocus, function ($focus) {
            return (substr_count($focus, '-') + substr_count($focus, '=')) === 2;
        }));
        $jlcYesterdayReliability = Normalizer::normalize($forecasts['.j_reliability'][0]);

        return [
            'reporter_yesterday_comment_label' => $reporterYesterdayCommentLabel,
            'reporter_yesterday_comment' => $reporterYesterdayComment,
            'reporter_yesterday_course_label' => $reporterYesterdayCourseLabel,
            'reporter_yesterday_course' => $reporterYesterdayCourse,
            'reporter_yesterday_focus_label' => $reporterYesterdayFocusLabel,
            'reporter_yesterday_focus' => $reporterYesterdayFocus,
            'reporter_yesterday_focus_exacta_label' => $reporterYesterdayFocusExactaLabel,
            'reporter_yesterday_focus_exacta' => $reporterYesterdayFocusExacta,
            'reporter_yesterday_focus_trifecta_label' => $reporterYesterdayFocusTrifectaLabel,
            'reporter_yesterday_focus_trifecta' => $reporterYesterdayFocusTrifecta,
            'jlc_yesterday_course_label' => $jlcYesterdayCourseLabel,
            'jlc_yesterday_course' => $jlcYesterdayCourse,
            'jlc_yesterday_focus_label' => $jlcYesterdayFocusLabel,
            'jlc_yesterday_focus' => $jlcYesterdayFocus,
            'jlc_yesterday_focus_exacta_label' => $jlcYesterdayFocusExactaLabel,
            'jlc_yesterday_focus_exacta' => $jlcYesterdayFocusExacta,
            'jlc_yesterday_focus_trifecta_label' => $jlcYesterdayFocusTrifectaLabel,
            'jlc_yesterday_focus_trifecta' => $jlcYesterdayFocusTrifecta,
            'jlc_yesterday_reliability_label' => $jlcYesterdayReliabilityLabel,
            'jlc_yesterday_reliability' => $jlcYesterdayReliability,
        ];
    }

    /**
     * @param  string|int                           $raceNumber
     * @param  \Carbon\CarbonInterface|string|null  $raceDate
     * @return array
     *
     * @throws \RuntimeException
     */
    private function scrapeToday(string|int $raceNumber, CarbonInterface|string|null $raceDate = null): array
    {
        $raceDate = Carbon::parse($raceDate ?? 'today')->format('Ymd');
        $crawlerUrl = sprintf($this->baseUrl, 'yosou', $raceDate, $raceNumber, '&kind=1');
        $crawler = Scraper::getInstance()->request('GET', $crawlerUrl);
        $forecasts = Scraper::filterByKeys($crawler, [
            '.writer_txt',
            '.focus_list > li',
        ]);

        foreach ($forecasts as $key => $value) {
            if (empty($value)) {
                throw new \RuntimeException(
                    __METHOD__ . "() - The specified key '{$key}' is not found " .
                    "in the content of the URL: '{$crawlerUrl}'."
                );
            }
        }

        $reporterTodayCommentLabel = '記者予想 当日コメント';
        $reporterTodayFocusLabel = '記者予想 当日フォーカス';
        $reporterTodayFocusExactaLabel = '記者予想 当日フォーカス 2連単';
        $reporterTodayFocusTrifectaLabel = '記者予想 当日フォーカス 3連単';

        $reporterTodayComment = Normalizer::normalize($forecasts['.writer_txt'][0]);
        $reporterTodayFocus = Normalizer::normalize($forecasts['.focus_list > li']);
        $reporterTodayFocus = array_map(fn($focus) => str_replace('―', '-', $focus), $reporterTodayFocus);
        $reporterTodayFocusExacta = array_values(array_filter($reporterTodayFocus, function ($focus) {
            return (substr_count($focus, '-') + substr_count($focus, '=')) === 1;
        }));
        $reporterTodayFocusTrifecta = array_values(array_filter($reporterTodayFocus, function ($focus) {
            return (substr_count($focus, '-') + substr_count($focus, '=')) === 2 || str_contains($focus, 'BOX');
        }));

        return [
            'reporter_today_comment_label' => $reporterTodayCommentLabel,
            'reporter_today_comment' => $reporterTodayComment,
            'reporter_today_focus_label' => $reporterTodayFocusLabel,
            'reporter_today_focus' => $reporterTodayFocus,
            'reporter_today_focus_exacta_label' => $reporterTodayFocusExactaLabel,
            'reporter_today_focus_exacta' => $reporterTodayFocusExacta,
            'reporter_today_focus_trifecta_label' => $reporterTodayFocusTrifectaLabel,
            'reporter_today_focus_trifecta' => $reporterTodayFocusTrifecta,
        ];
    }
}
