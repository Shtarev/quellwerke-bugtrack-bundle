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
        $projectRoot = dirname(__DIR__, 6);
        $logFile = $projectRoot . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'dev-error.log';
        $errors = $this->getLogErrorsLastHours($logFile, 2);

        $message = $request->request->get('value');
        $frontLog = $this->deepJsonDecode($request->request->get('frontLog'));
        $time = date('d-m-Y_H-i-s');
        $fileName = 'bug_message__' . $time . '.json';

        $response = [
            'status' => 'ok',
            'message' => $message,
            'fileName' => $fileName,
            'time' => $time,
            'result' => 'The request has been received and processed.',
            'backErrorLog' => $errors,
            'frontLog' => $frontLog
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
     * Get the error log for a specified number of hours
     * @param string $logFile number of hours
     * @param int $hours 
     * @return string[]
     */
    private function getLogErrorsLastHours(string $logFile, int $hours = 2): array
    {
        $result = [];
        $threshold = new \DateTimeImmutable("-{$hours} hours");

        if (!is_readable($logFile)) {
            return $result;
        }

        $file = new \SplFileObject($logFile, 'r');

        while (!$file->eof()) {
            $line = $file->fgets();

            if ($line === false || $line === '') {
                continue;
            }

            if (!preg_match('/^\[(.*?)\]/', $line, $matches)) {
                continue;
            }

            try {
                $logTime = new \DateTimeImmutable($matches[1]);
            } catch (\Exception $e) {
                continue;
            }

            if ($logTime >= $threshold) {
                $result[] = $line;
            }
        }

        return $result;
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

    private function deepJsonDecode($value) {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return $decoded === null ? $value : $this->deepJsonDecode($decoded);
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->deepJsonDecode($v);
            }
        }

        return $value;
    }
}
