<?php

namespace Console\App\Commands;

use Console\App\Git\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\log_graphviz;

require_once __DIR__ . '/../utils/helpers.php';

class LogCommand extends Command
{
    protected function configure()
    {
        $this->setName('log')
            ->setDescription('Display history of a given commit.')
            ->setHelp('Display history of a given commit.')
            ->addArgument('commit', InputArgument::OPTIONAL, 'commit to start at', 'HEAD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commit = $input->getArgument("commit");
        $output->writeln($commit);

        $repo = new GitRepository(".");

        $poop = log_graphviz($repo, $commit, new \Ds\Set());

        return Command::SUCCESS;
    }
}