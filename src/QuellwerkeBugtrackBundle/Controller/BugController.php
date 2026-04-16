<?php

namespace Quellwerke\QuellwerkeBugtrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BugController extends AbstractController
{

    #[Route('/admin/bugtrack/bugs', name: 'admin_bugs', methods:['post'])]
    public function index(Request $request): JsonResponse
    {
        $message = $request->request->get('value');
        $fileName = 'bug_message__' . date('d-m-Y_H-i-s') . '.json';

        $response = [
            'status' => 'ok',
            'message' => $message,
            'fileName' => $fileName,
            'result' => 'The request has been received and processed',
            'backLog' => $this->backLog(),
            'frontLog' => $this->frontLog($request)
        ];


        /* If it is necessary to save the file on the server. */
        // $projectRoot = dirname(__DIR__, 6);
        // $jsonFile = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // file_put_contents($projectRoot . DIRECTORY_SEPARATOR . $fileName, $jsonFile, FILE_APPEND | LOCK_EX);

        return new JsonResponse($response);
    }

    /**
     * Search for logs and errors in the system
     * @return array
     */
    private function backLog(): array
    {
        return [
            'bugLog_1' => 'text example 1',
            'bugLog_2' => 'text example 2',
            'bugLog_3' => 'text example 3'
        ];
    }

    /**
     * Search for logs and errors in the data received from the client
     * @return array
     */
    private function frontLog($request): array
    {
        return [
            'bugLog_1' => 'text example 1',
            'bugLog_2' => 'text example 2',
            'bugLog_3' => 'text example 3'
        ];
    }
}
