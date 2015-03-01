<?php
namespace Vdm\Api\V1\Posts;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class CollectionController
{
    public function getAction(Application $app, Request $request)
    {
        $posts = $this->instanciatePostCollection($app);
        $posts->getAll();

        return $app->json($posts->toArray(), Response::HTTP_OK);
    }

    public function postAction(Application $app, Request $request)
    {
        $post = $this->instanciatePost($app);
        $post->completeFromArray(
          array(
            'email' => $request->get('email'),
            'name'  => $request->get('name')
          )
        );
        $post->save();

        $response = $app->json(
          array(
            "message" => "Post " . $post->getId() . " created"
          ),
          Response::HTTP_CREATED
        );
        $response->headers->set(
          "Location",
          $app['url_generator']->generate(
            'posts.instance.get',
            array('id' => $post->getId())
          )
        );

        return $response;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    private function instanciatePostCollection(Application $app)
    {
        $collectionFactory = $app['model.postCollection'];
        $posts = $collectionFactory($app);

        return $posts;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    private function instanciatePost(Application $app)
    {
        $postFactory = $app['model.post'];
        $post = $postFactory($app['db']);

        return $post;
    }
}
