<?php
// Example
namespace Quellwerke\QuellwerkeBugtrackBundle\Command;

use Quellwerke\QuellwerkeBugtrackBundle\Service\BugService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BugsCommand extends Command
{
    protected static $defaultName = 'quellwerke:bugs:command';

    public function __construct(private BugService $bugService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bug = $this->bugService->createBug('Test bug');

        $output->writeln('Bug created: ' . $bug['title']);

        return Command::SUCCESS;
    }
}