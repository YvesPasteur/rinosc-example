<?php
namespace VdmScraping;


class Page
{
    const SITE_URL = 'http://www.viedemerde.fr/';

    /**
     * @var int
     */
    private $number;
    /**
     * @var \Goutte\Client
     */
    private $client;

    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    private $crawler;

    public function __construct($number, \Goutte\Client $client)
    {
        $this->number = $number;
        $this->client = $client;
    }

    public function request()
    {
        $param = "";
        if ($this->number > 0) {
            $param = "?page=" . $this->number;
        }
        $this->crawler = $this->client->request('GET', self::SITE_URL . $param);

        return $this;
    }

    /**
     * @param int $postNumberToRetrieve Max number of post to retrieve from the page
     * @return array Collection of nodes
     */
    public function extractPosts($postNumberToRetrieve)
    {
        $accumulator = array();
        $this->crawler->filter('.post.article')->each(
            function ($node, $i) use (&$accumulator, $postNumberToRetrieve) {
                if ($i < $postNumberToRetrieve) {
                    $post = new Post($node);
                    $post->extractInfoFromNode();
                    $accumulator[] = $post;
                }
            }
        );

        return $accumulator;
    }
}
