<?php
namespace Vdm\Model;

class Post
{
    private $db;
    private $id;
    private $content;
    private $author;
    private $date;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function isInBase()
    {
        return $this->id !== null;
    }

    public function loadById($id)
    {
        $sql = "SELECT * FROM posts WHERE id = ?";
        $post = $this->db->fetchAssoc($sql, array((int)$id));

        if ($post) {
            $this->completefromArray($post);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        if ($this->isInBase()) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * @return $this
     */
    public function insert()
    {
        $this->db->insert('posts', $this->toArray());
        $this->id = $this->db->lastInsertId();

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function update()
    {
        $nbUpdates = $this->db->update(
            'post',
            array(
                'content' => $this->content,
                'author'  => $this->author,
                'date'    => $this->date
            ),
            array(
                'id' => $this->id
            )
        );

        if ($nbUpdates === 0) {
            throw new \Exception('Invalid identifier : resource not found');
        }

        return $this;
    }

    public function delete()
    {
        $nbDelete = $this->db->delete('post', array('id' => $this->id));

        if ($nbDelete === 0) {
            throw new \Exception('Invalid identifier : resource not found');
        }

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function toArray()
    {
        return array(
            'id'      => $this->id,
            'content' => $this->content,
            'author'  => $this->author,
            'date'    => $this->date
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public function completefromArray(array $data)
    {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['content'])) {
            $this->content = $data['content'];
        }
        if (isset($data['author'])) {
            $this->author = $data['author'];
        }
        if (isset($data['date'])) {
            $this->date = $data['date'];
        }

        return $this;
    }
}
