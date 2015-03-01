<?php
namespace VdmScraping;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

class SaveCommand extends \Cilex\Command\Command
{
    const DEFAULT_POST_NUMBER = 5;

    const API_POST_URL = 'http://localhost:80/api/v1/posts/';

    protected function configure()
    {
        $this
            ->setName('scrap:save')
            ->setDescription('Scrap some posts on the vdm website to save them in database')
            ->addArgument('number', InputArgument::OPTIONAL, 'How many posts do you want ?', self::DEFAULT_POST_NUMBER);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getArgument('number');

        $output->writeln("<comment>You want $number posts.</comment>");

        $accumulator = $this->extractAllPosts($number);

        foreach ($accumulator as $index => $post) {
            /** @var \Symfony\Component\DomCrawler\Crawler $node */
            try {
                $this->savePost($post);
                $output->writeln("<info>Post saved : " . ($index + 1) . "</info>");
            }
            catch (\Exception $e) {
                $output->writeln("<error>Post " . ($index + 1) . " not saved : " . $e->getMessage() . "</error>");
            }
        }
    }

    /**
     * @param int $expectedNumberOfPosts Number of posts to retrieve from the website
     * @return array Collection of the postss
     */
    protected function extractAllPosts($expectedNumberOfPosts)
    {
        /** @var \Goutte\Client $client */
        $client = $this->getService('scrap.client');
        $postsCollector = new PostsCollector($client);
        return $postsCollector->extractAllPosts($expectedNumberOfPosts);
    }

    /**
     * @param Post $post
     * @return int Status code
     * @throws \Exception
     */
    protected function savePost(Post $post)
    {
        /** @var \Goutte\Client $client */
        $client = $this->getService('scrap.client');
        $params = array(
            'content' => $post->getContent(),
            'author' => $post->getAuthor(),
            'date' => $post->getDate()->format('Y-m-d H:i:s')
        );
        $client->request('POST', self::API_POST_URL, $params);

        if ($client->getResponse()->getStatus() !== Response::HTTP_CREATED) {
            throw new \Exception('Post not created, return status code : ' . $client->getResponse()->getStatus());
        }
    }
}
