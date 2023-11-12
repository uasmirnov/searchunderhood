<?php

namespace Test\Import;

include_once 'ImporterInterface.php';
include_once 'ConsoleNotificateEvent.php';

use Exception;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Test\Import\ConsoleNotificateEvent as Event;

class Importer implements ImporterInterface
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var WriterInterface
     */
    private WriterInterface $writer;

    /**
     * @var Crawler
     */
    private Crawler $crawler;

    /**
     * @var array
     */
    private array $buffer;

    /**
     * @var int
     */
    private int $offset;

    /**
     * @var int
     */
    public static int $countResults;

    /**
     * @var string
     */
    private string $uri;

    /**
     * @param string $word
     */
    public function __construct(
        protected string $word,
        protected string $writerType,
    ) {
        $this->client = new Client();
        $this->writer = WriterSelector::select($this, $writerType);
        $this->uri = 'https://search.ipaustralia.gov.au/trademarks/search/advanced/';
    }

    /**
     * @return $this
     */
    public function prepare(): self
    {
        Event::listen(Event::START_SEARCH, function() {
            echo "\n Searching... \n";
        });

        $this->buffer = [];
        $this->offset = 0;
        $this->writer->prepare();
        self::$countResults = 0;

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function validate(): self
    {
        $crawler = $this->client->request('GET', $this->uri);

        $form = $crawler->selectButton('Search')->form();
        $form->setValues([
            'wv' => [
                $this->word,
            ],
        ]);

        $crawler = $this->client->submit($form);
        $countResults = $crawler->filter('.js-mark-record')->count();

        if ($countResults === 0) {
            throw new \Exception(sprintf("\n Not found (%s) \n", \strlen($this->word) > 0
                ? $this->word
                : 'Search context missing') . "\n"
            );
        }

        $this->crawler = $crawler;
        self::$countResults = intval(preg_replace('/[^\d.]/',
            '',
            $crawler->filter('h2[class="number qa-count"]')->text())
        );
        Event::trigger(Event::START_SEARCH);

        return $this;
    }

    /**
     * @return $this
     */
    public function run(): self
    {
        $offset = $this->offset;

        $this->crawler->filter('.js-mark-record')->each(function (Crawler $node, $i) use ($offset) {
            $index = ++$i + $offset;
            $buffer = [
                'number' => $node->filter('td.number a')->text(),
                'url_logo' => \count($node->filter('td.trademark.image img')) > 0
                    ? $node->filter('td.trademark.image img')->eq(0)->attr('src')
                    : '',
                'name' => $node->filter('td.trademark.words')->text(),
                'class' => $node->filter('td.classes')->text(),
                'status' => preg_replace('/[^\p{L}\p{N}\s]/u', '', $node->filter('td.status')->text()),
                'url_details_page' => $node->filter('td.number a')->eq(0)->attr('href'),
            ];

            $this->offset = $index;
            $this->buffer[$index] = $buffer;
        });

        $nodeNextUri = $this->crawler->filter('a.js-nav-next-page');
        $this->writer->save();
        $this->buffer = [];

        if ($nodeNextUri->count() > 0) {
            $this->crawler = $this->client->click($nodeNextUri->first()->link());
            $this->run();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * @return int
     */
    public static function getCountResults(): int
    {
        return self::$countResults;
    }
}