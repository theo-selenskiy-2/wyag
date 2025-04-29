<?php

namespace Console\App\Commands;

require_once __DIR__ . '/../utils/helpers.php';

use Console\App\Git\GitRepository;
use Console\App\Git\Object\GitBlob;
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
        // $path = $input->getArgument('path') ?? '.';
        // $output->writeln($path);

        // $sha = "0e731391907116139cdc9b68b061c4be90ce9e70";
        // $this->read($path, $sha, $output);

        $data = "tree 29ff16c9c14e2652b22f8b78bb08a5a07930c147
parent 206941306e8a8af65b66eaaaea388a7ae24d49a0
author Thibault Polge <thibault@thb.lt> 1527025023 +0200
committer Thibault Polge <thibault@thb.lt> 1527025044 +0200
gpgsig -----BEGIN PGP SIGNATURE-----

 iQIzBAABCAAdFiEExwXquOM8bWb4Q2zVGxM2FxoLkGQFAlsEjZQACgkQGxM2FxoL
 kGQdcBAAqPP+ln4nGDd2gETXjvOpOxLzIMEw4A9gU6CzWzm+oB8mEIKyaH0UFIPh
 rNUZ1j7/ZGFNeBDtT55LPdPIQw4KKlcf6kC8MPWP3qSu3xHqx12C5zyai2duFZUU
 wqOt9iCFCscFQYqKs3xsHI+ncQb+PGjVZA8+jPw7nrPIkeSXQV2aZb1E68wa2YIL
 3eYgTUKz34cB6tAq9YwHnZpyPx8UJCZGkshpJmgtZ3mCbtQaO17LoihnqPn4UOMr
 V75R/7FjSuPLS8NaZF4wfi52btXMSxO/u7GuoJkzJscP3p4qtwe6Rl9dc1XC8P7k
 NIbGZ5Yg5cEPcfmhgXFOhQZkD0yxcJqBUcoFpnp2vu5XJl2E5I/quIyVxUXi6O6c
 /obspcvace4wy8uO0bdVhc4nJ+Rla4InVSJaUaBeiHTW8kReSFYyMmDCzLjGIu1q
 doU61OM3Zv1ptsLu3gUE6GU27iWYj2RWN3e3HE4Sbd89IFwLXNdSuM0ifDLZk7AQ
 WBhRhipCCgZhkj9g2NEk7jRVslti1NdN5zoQLaJNqSwO1MtxTmJ15Ksk3QP6kfLB
 Q52UWybBzpaP9HEd4XnR+HuQ4k2K0ns2KgNImsNvIyFwbpMUyUWLMPimaV1DWUXo
 5SBjDB/V/W2JBFR+XKHFJeFwYhj7DD/ocsGr4ZMx/lgc8rjIBkI=
 =lgTX
 -----END PGP SIGNATURE-----

Create first draft";

        $arr = kvlm_parse($data);
        // foreach($arr as $key => $value) {
        //     $output->writeln(sprintf("key: %s", $key));
        //     $output->writeln(sprintf("value: %s", $value));
        // }

        $poop = kvlm_serialize($arr);

        //$output->writeln($arr);
        $output->writeln($poop);

        return Command::SUCCESS;
    }
}
