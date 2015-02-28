<?php
namespace VdmScraping;


class PostsCollector
{
    /**
     * @var \Goutte\Client
     */
    private $client;

    /**
     * @param \Goutte\Client $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param int $expectedNumberOfPosts Number of posts to retrieve from the website
     * @return array Collection of the postss
     */
    public function extractAllPosts($expectedNumberOfPosts)
    {
        $accumulator = array();
        $i = 0;
        while (count($accumulator) < $expectedNumberOfPosts) {
            $page = new Page($i, $this->client);
            $page->request();
            $maxToRetrieve = $expectedNumberOfPosts - count($accumulator);
            $accumulator = array_merge($accumulator, $page->extractPosts($maxToRetrieve));
            $i++;
        }

        return $accumulator;
    }
}
