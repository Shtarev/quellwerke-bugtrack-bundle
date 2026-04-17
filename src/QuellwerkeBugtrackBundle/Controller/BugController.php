<?php

namespace Quellwerke\QuellwerkeBugtrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Quellwerke\QuellwerkeBugtrackBundle\Service\EmailService;
use Pimcore\Config;

class BugController extends AbstractController
{

    #[Route('/admin/bugtrack/bugs', name: 'admin_bugs', methods:['post'])]
    public function index(Request $request, EmailService $emailService): JsonResponse
    {
        $message = $request->request->get('value');
        $time = date('d-m-Y_H-i-s');
        $fileName = 'bug_message__' . $time . '.json';

        $response = [
            'status' => 'ok',
            'message' => $message,
            'fileName' => $fileName,
            'time' => $time,
            'result' => 'The request has been received and processed.',
            'backLog' => $this->backLog(),
            'frontLog' => $this->frontLog($request)
        ];

        $emails = $this->debugEmails();

        // Send messages with a JSON file to all addresses from $emails
        $sendResult = false;
        if($emails) {
            $jsonFile = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $sendResult = $emailService->sendMail($emails, $jsonFile, $fileName, $message);
        }
        if($sendResult) {
            $response['result'] .= ' A notification has been sent to the support email address.';
        }
        else {
            $response['result'] .= ' Please forward the downloaded file to the support team.';
        }

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

    /**
     * From System Settings, retrieve Debug Email Addresses (CSV)
     * @return array
     */
    private function debugEmails(): array
    {
        $emails = [];
        $config = Config::getSystemConfiguration();
        $emailsCsv = $config['email']['debug']['email_addresses'] ?? null;
        if($emailsCsv) {
            $emails = array_filter(array_map('trim', explode(',', $emailsCsv)));
        }
        if($emails==[]) {
            $db = \Pimcore\Db::get();
            $data = $db->fetchOne("
                SELECT data 
                FROM settings_store 
                WHERE `id` = 'system_settings'
            ");
            $config = json_decode($data, true);
            $emailsCsv = $config['email']['debug']['email_addresses'] ?? null;
            $emails = array_filter(array_map('trim', explode(',', $emailsCsv)));
        }
        return $emails;
    }
}
