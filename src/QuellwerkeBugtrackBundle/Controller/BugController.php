<?php
// Example
namespace Quellwerke\QuellwerkeBugtrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BugController extends AbstractController
{
    #[Route('/admin/bugtrack/bugs', name: 'admin_bugs')]
    public function index(): Response
    {
        return new Response('HALLO BugtrackBundle!!!');
    }
}
