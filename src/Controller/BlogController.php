<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BlogRepository;
use App\Entity\Blog;
use Symfony\Component\HttpFoundation\Request;
use App\Service\RouteUrlGenerate;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Form\BlogType;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(
        BlogRepository $blogRepository
    ): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll()
        ]);
    }
    
    #[Route('/blog/add', name: 'app_blog_add', priority: 2)]
    public function add(
        Request $request,
        BlogRepository $blogRepository
    ): Response
    {
        $blog = new Blog();
        
        $form = $this->createForm(BlogType::class, $blog);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //Do something to save the blog
            
            $blog = $blogRepository->save($blog, true);
            
            $this->addFlash('success', 'Article "' . $blog->getName() . '" Added successfully');
            
            return $this->redirectToRoute('app_blog');
        }
        
        return $this->render('blog/add.html.twig', [
            "form" => $form
        ]);
    }
    
    
    #[Route('/blog/{id}', name: 'app_blog_details')]
    public function blogDetails(Blog $blog, Request $request, RouteUrlGenerate $routeUrlGenerate): Response
    {
        //Do the code to get blog detail form db based on matching slug
        //$blog = $blogRepository->findOneBy(['slug'=>$slug]);
        
        return $this->render('blog/index.html.twig', [
            "message" => "This is the detail page, My slug is : " . $blog->getName()
        ]);
    }
    
    
    #[Route('/blog-detail/{id}', name: 'app_blog_details_v1')]
    public function getBlogDetails(
        int $id, 
        Request $request, 
        RouteUrlGenerate $routeUrlGenerate,
        BlogRepository $blogRepository
    ): Response
    {
        //Do the code to get blog detail form db based on matching slug
        $blog = $blogRepository->find($id);
        if(!$blog instanceof Blog) {
            //throw $this->createNotFoundException('Blog Not Found');
            
            //throw new AccessDeniedException();
            
            throw new \Exception('Blog Not Found');
        }
        
        $xml = '<body>This is sample xml</body>';
        $response = new Response($xml);
        $response->headers->set('Content-Type', 'xml');
        
        return $response;
        
        return $this->json([
            'id' => $blog->getId(),
            'name' => $blog->getName(),
            'slug' => $blog->getSlug()
        ]);
        
        /*return $this->render('blog/index.html.twig', [
            "message" => "This is the detail page, My slug is : " . $blog->getName()
        ]);*/
    }
    
    #[Route('/blog/about-us', name: 'app_blog_about_us', priority: 2)]
    public function aboutUs(): Response
    {
        //Do the code to get blog detail form db based on matching slug
        
        return $this->render('blog/index.html.twig', [
            "message" => "This is about us page"
        ]);
    }
        
    #[Route('/render', name: 'app_blog_render')]
    public function recentArticles(): Response
    {
        return $this->render('blog/index.html.twig', [
        ]);
    }
}
