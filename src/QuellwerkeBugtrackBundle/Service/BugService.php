<?php
// Example
namespace Quellwerke\QuellwerkeBugtrackBundle\Service;

class BugService
{
    public function createBug(string $title): array
    {
        return [
            'title' => $title,
            'createdAt' => new \DateTime(),
        ];
    }
}