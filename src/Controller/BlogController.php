<?php

declare(strict_types=1);

namespace SymfonyDemo\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
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

    /**
     * @return ViewModel
     */
    public function searchAction(): ViewModel
    {
        if (! $this->getRequest()->isXmlHttpRequest()) {

            return new ViewModel();
        }
        $request = $this->getRequest();
        $query = $request->getQuery('q', '');
        $limit = $request->getQuery('l', 10);
        $foundPosts = $this->posts->findBySearchQuery($query, $limit);

        $results = [];
        foreach (new \ArrayObject($foundPosts) as $post) {
            $results[] = [
                'title' => htmlspecialchars(
                    $post->getTitle(),
                    ENT_COMPAT | ENT_HTML5
                ),
                'date' => $post->getPublishedAt()->format('M d, Y'),
                'author' => htmlspecialchars(
                    $post->getAuthor()->getFullName(),
                    ENT_COMPAT | ENT_HTML5
                ),
                'summary' => htmlspecialchars(
                    $post->getSummary(),
                    ENT_COMPAT | ENT_HTML5
                ),
                'url' => $this->url()->fromRoute(
                    'blog/detail',
                    ['slug' => $post->getSlug()]
                ),
            ];
        }

        return new JsonModel($results);
    }
}
