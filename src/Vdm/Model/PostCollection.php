<?php
namespace Vdm\Model;

use Silex\Application;

class PostCollection
{
    private $db;
    private $modelFactory;
    private $posts = array();

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
        $sql = "SELECT * FROM posts ORDER BY id";

        return $this->db->fetchAll($sql);
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
