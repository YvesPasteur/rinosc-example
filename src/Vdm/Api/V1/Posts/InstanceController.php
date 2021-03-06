<?php
namespace Vdm\Api\V1\Posts;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class InstanceController
{
    public function getAction(Application $app, Request $request, $id)
    {
        $post = $this->instanciatePost($app);
        $post->loadById((int) $id);

        if (! $post->isInBase()) {
            return $app->json(
              array(
                "error" => 'post_not_found',
                "error_description" => "Post $id not found"
              ),
              Response::HTTP_NOT_FOUND
            );
        }

        $result = array(
            "post" => $post->toArray()
        );

        return $app->json($result, Response::HTTP_OK);
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
