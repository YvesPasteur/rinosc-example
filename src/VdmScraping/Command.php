<?php
namespace VdmScraping;

use Goutte\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Cilex\Command\Command
{
    const DEFAULT_POST_NUMBER = 5;

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

        $crawler = $this->getPage($output);
        $accumulator = $this->extractPosts($crawler, $number);

        foreach ($accumulator as $node) {
            /** @var \Symfony\Component\DomCrawler\Crawler $node */
            list($content, $day, $month, $year, $hour, $minute, $author) = $this->extractInfoFromNode($node);
            $this->writePost($output, $content, $year, $month, $day, $hour, $minute, $author);
        }
    }

    /**
     * @param OutputInterface $output
     * @param $content
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     * @param $author
     */
    protected function writePost(OutputInterface $output, $content, $year, $month, $day, $hour, $minute, $author)
    {
        $output->writeln('=================');
        $output->writeln("");
        $output->writeln($content);
        $output->writeln("<info>Date : $year-$month-$day $hour:$minute</info>");
        $output->writeln("<info>Author : $author</info>");
        $output->writeln("");
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

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    protected function getPage(OutputInterface $output)
    {
        $client = $this->getService('scrap.client');
        $output->writeln("<comment>A moment please. I try to contact Mr VDM</comment>");
        $output->writeln("");
        $crawler = $client->request('GET', 'http://www.viedemerde.fr/');

        $output->writeln("<comment>Ok, I have the answer. Have a good reading !</comment>");
        $output->writeln("");

        return $crawler;
    }

    /**
     * @param $crawler
     * @param $number
     * @return array
     */
    protected function extractPosts($crawler, $number)
    {
        $accumulator = array();
        $crawler->filter('.post.article')->each(
            function ($node, $i) use (&$accumulator, $number) {
                if ($i < $number) {
                    $accumulator[] = $node;
                }
            }
        );

        return $accumulator;
    }
}
