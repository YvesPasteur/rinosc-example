<?php
namespace VdmScraping;


class Post
{
    /**
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    private $node;
    /**
     * @var string
     */
    private $content;
    /**
     * @var DateTime
     */
    private $date;
    /**
     * @var string
     */
    private $author;

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $node
     */
    public function __construct(\Symfony\Component\DomCrawler\Crawler $node)
    {
        $this->node = $node;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function extractInfoFromNode()
    {
        $this->content = utf8_decode($this->node->filter('p')->text());
        $dateAndAuthor = utf8_decode($this->node->filter('div.date .right_part p:nth-child(2)')->text());
        $regexp = '#Le ([0-9]{2})/([0-9]{2})/([0-9]{4}) à ([0-9]{2}):([0-9]{2}) - .* - par ([^ ]*) #';

        if (!preg_match($regexp, $dateAndAuthor, $matches)) {
            throw new \Exception("Can't extract the date and author from the text : $dateAndAuthor");
        }
        list(, $day, $month, $year, $hour, $minute, $this->author) = $matches;

        $this->date = new \DateTime("$year-$month-$day $hour:$minute");

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
