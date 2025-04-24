<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

class GitBlob extends GitObject
{
    protected function serialize(GitRepository $repo) {}

    protected function deserialize(mixed $data) {}
}
