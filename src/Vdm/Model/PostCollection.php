<?php
namespace Vdm\Model;

use Silex\Application;

class PostCollection
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;
    private $modelFactory;
    private $posts = array();
    private $authorFilter;
    private $dateFilter = array('from' => null, 'to' => null);

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
        $this->modelFactory = $app['model.post'];
    }

    /**
     * @return $this
     */
    public function getAll()
    {
        $posts = $this->getRawAll();
        $this->posts = $this->getInstancedCollection($posts, $this->modelFactory);

        return $this;
    }

    /**
     * @param string $author
     * @return $this
     */
    public function setAuthorFilter($author)
    {
        $this->authorFilter = $author;
        return $this;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setFromDateFilter($date)
    {
        $this->dateFilter['from'] = $date;
        return $this;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setToDateFilter($date)
    {
        $this->dateFilter['to'] = $date;
        return $this;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->posts as $post) {
            $result[] = $post->toArray();
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getRawAll()
    {
        $filters = array();
        $params = array();
        if (! empty($this->authorFilter)) {
            $filters[] = "author = :author";
            $params['author'] = $this->authorFilter;
        }

        if (! empty($this->dateFilter['from'])) {
            $filters[] = "date > :date_from";
            $params['date_from'] = $this->dateFilter['from'];
        }

        if (! empty($this->dateFilter['to'])) {
            $filters[] = "date < :date_to";
            $params['date_to'] = $this->dateFilter['to'];
        }

        $filter = "";
        if (! empty($filters)) {
            $filter = 'WHERE ' . implode(' AND ', $filters);
        }

        $sql = "SELECT * FROM posts $filter ORDER BY id";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * @param array $posts
     * @param \Closure $modelFactory
     * @return array
     */
    private function getInstancedCollection(array $posts, \Closure $modelFactory)
    {
        $collection = array();
        foreach ($posts as $post) {
            $object = $modelFactory($this->db);
            $object->completefromArray($post);

            $collection[] = $object;
        }

        return $collection;
    }
}
