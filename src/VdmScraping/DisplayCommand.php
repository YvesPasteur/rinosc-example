<?php
namespace VdmScraping;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DisplayCommand extends \Cilex\Command\Command
{
    const DEFAULT_POST_NUMBER = 5;

    protected function configure()
    {
        $this
            ->setName('scrap:display')
            ->setDescription('Scrap some posts on the vdm website to display in the console')
            ->addArgument('number', InputArgument::OPTIONAL, 'How many posts do you want ?', self::DEFAULT_POST_NUMBER);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');

        $output->writeln("<comment>You want $number posts.</comment>");

        $accumulator = $this->extractAllPosts($number, $output);

        foreach ($accumulator as $index => $post) {
            /** @var \Symfony\Component\DomCrawler\Crawler $node */
            $output->writeln('=================');
            $output->writeln("");
            $output->writeln("n°" . ($index + 1));
            $this->writePost($post, $output);
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
        /** @var \Goutte\Client $client */
        $client = $this->getService('scrap.client');
        $postsCollector = new PostsCollector($client);
        return $postsCollector->extractAllPosts($expectedNumberOfPosts);
    }

    /**
     * @param Post $post
     * @param OutputInterface $output
     */
    protected function writePost(Post $post, OutputInterface $output)
    {
        $output->writeln($post->getContent());
        $output->writeln("<info>Date : " . $post->getDate()->format('Y-m-d H:i:s') . "</info>");
        $output->writeln("<info>Author : " . $post->getAuthor() . "</info>");
    }
}
