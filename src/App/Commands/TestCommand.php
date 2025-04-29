<?php

namespace Console\App\Commands;

require_once __DIR__ . '/../utils/helpers.php';

use Console\App\Git\GitRepository;
use Console\App\Git\Object\GitBlob;
use Console\App\Git\Object\GitCommit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\is_dir_empty;
use function Console\App\utils\kvlm_parse;
use function Console\App\utils\kvlm_serialize;
use function Console\App\utils\object_read;
use function Console\App\utils\repo_create;
use function Console\App\utils\repo_path;
use function Console\App\utils\repo_file;
use function Console\App\utils\object_write;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('testing')
            ->addArgument('path', InputArgument::OPTIONAL);
    }

    private function read(string $path, string $sha, OutputInterface $output) {
        $repo = new GitRepository($path);
        //$output->writeln("got repo");

        $obj = object_read($repo, $sha);

        $output->writeln($obj->getFormat());
        $output->writeln($obj->serialize());
    }

    private function write(string $path, OutputInterface $output) {
        $repo = new GitRepository($path);
        $obj = new GitBlob('this is the contents of a file');
        $sha = object_write($obj, $repo);
        $output->writeln(sprintf("created obj in memory with sha: %s", $sha));
        return $sha;
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
         $path = $input->getArgument('path') ?? '.';

        return Command::SUCCESS;
    }
}
