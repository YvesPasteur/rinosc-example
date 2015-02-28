<?php
namespace VdmScraping;

use Goutte\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Cilex\Command\Command
{
    const DEFAULT_POST_NUMBER = 5;

    const SITE_URL = 'http://www.viedemerde.fr/';

    protected function configure()
    {
        $this
            ->setName('scrap')
            ->setDescription('Scrap some posts on the vdm website')
            ->addArgument('number', InputArgument::OPTIONAL, 'How many posts do you want ?', self::DEFAULT_POST_NUMBER);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');

        $output->writeln("<comment>You want $number posts.</comment>");

        $accumulator = $this->extractAllPosts($output, $number);

        foreach ($accumulator as $index => $node) {
            /** @var \Symfony\Component\DomCrawler\Crawler $node */
            list($content, $day, $month, $year, $hour, $minute, $author) = $this->extractInfoFromNode($node);
            $output->writeln('=================');
            $output->writeln("");
            $output->writeln("n°" . ($index + 1));
            $this->writePost($content, $year, $month, $day, $hour, $minute, $author, $output);
            $output->writeln("");
        }
    }

    /**
     * @param int $expectedNumberOfPosts Number of posts to retrieve from the website
     * @param OutputInterface $output
     * @return array Collection of the postss
     */
    protected function extractAllPosts($expectedNumberOfPosts, OutputInterface $output)
    {
        $accumulator = array();
        $i = 0;
        while (count($accumulator) < $expectedNumberOfPosts) {
            $crawler = $this->getPage($i, $output);
            $maxToRetrieve = $expectedNumberOfPosts - count($accumulator);
            $accumulator = array_merge($accumulator, $this->extractPostsFromPage($crawler, $maxToRetrieve));
            $i++;
        }

        return $accumulator;
    }

    /**
     * @param int $offset Number of the page to request
     * @param OutputInterface $output
     * @return \Symfony\Component\DomCrawler\Crawler Requested page
     */
    protected function getPage($offset, OutputInterface $output)
    {
        /** @var \Goutte\Client $client */
        $client = $this->getService('scrap.client');
        $output->writeln("<comment>A moment please. I try to contact Mr VDM for the page $offset</comment>");
        $output->writeln("");

        $param = "";
        if ($offset > 0) {
            $param = "?page=$offset";
        }
        $crawler = $client->request('GET', self::SITE_URL . $param);

        $output->writeln("<comment>Ok, I have the answer. Next !</comment>");
        $output->writeln("");

        return $crawler;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler Page within are the posts
     * @param int $postNumberToRetrieve Max number of post to retrieve from the page
     * @return array Collection of nodes
     */
    protected function extractPostsFromPage($crawler, $postNumberToRetrieve)
    {
        $accumulator = array();
        $crawler->filter('.post.article')->each(
            function ($node, $i) use (&$accumulator, $postNumberToRetrieve) {
                if ($i < $postNumberToRetrieve) {
                    $accumulator[] = $node;
                }
            }
        );

        return $accumulator;
    }

    /**
     * @param string $content Content of a post
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param string $author Author of the post
     * @param OutputInterface $output
     */
    protected function writePost($content, $year, $month, $day, $hour, $minute, $author, OutputInterface $output)
    {
        $output->writeln($content);
        $output->writeln("<info>Date : $year-$month-$day $hour:$minute</info>");
        $output->writeln("<info>Author : $author</info>");
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $node
     * @return array
     * @throws \Exception
     */
    protected function extractInfoFromNode(\Symfony\Component\DomCrawler\Crawler $node)
    {
        $content = utf8_decode($node->filter('p')->text());
        $dateAndAuthor = utf8_decode($node->filter('div.date .right_part p:nth-child(2)')->text());
        $regexp = '#Le ([0-9]{2})/([0-9]{2})/([0-9]{4}) à ([0-9]{2}):([0-9]{2}) - .* - par ([^ ]*) #';

        if (!preg_match($regexp, $dateAndAuthor, $matches)) {
            throw new \Exception("Can't extract the date and author from the text : $dateAndAuthor");
        }
        list(, $day, $month, $year, $hour, $minute, $author) = $matches;

        return array($content, $day, $month, $year, $hour, $minute, $author);
    }
}
