<?php

declare(strict_types=1);

namespace SymfonyDemo\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use SymfonyDemo\Entity\Post;
use SymfonyDemo\Entity\Tag;
use SymfonyDemo\Repository\PostRepository;
use SymfonyDemo\Repository\TagRepository;
use Exception;

class BlogController extends AbstractActionController
{
    /**
     * @var PostRepository
     */
    private $posts;

    /**
     * @var TagRepository
     */
    private $tags;

    /**
     * @param  EntityManagerInterface  $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->posts = $entityManager->getRepository(Post::class);
        $this->tags = $entityManager->getRepository(Tag::class);
    }

    /**
     * @return ViewModel
     * @throws Exception
     */
    public function indexAction(): ViewModel
    {
        $request = $this->getRequest();
        $tag = $request->getQuery('tag', null);
        $page = (int) $request->getQuery('page', 1);
        if ($tag) {
            $tag = $this->tags->findOneBy(['name' => $tag]);
        }
        $latestPosts = $this->posts->findLatest($page, $tag);

        return new ViewModel([
            'paginator' => $latestPosts,
            'tag' => $tag
        ]);
    }

    /**
     * @return ViewModel
     */
    public function postShowAction(): ViewModel
    {
        $slug = $this->params()->fromRoute('slug');
        $post = $this->posts->findOneBy(
            ['slug' => htmlentities($slug, ENT_QUOTES, 'UTF-8')]
        );
        if (! $post) {
            $this->getResponse()->setStatusCode(404);

            return new ViewModel();
        }

        return new ViewModel([
            'post' => $post,
        ]);
    }
}
