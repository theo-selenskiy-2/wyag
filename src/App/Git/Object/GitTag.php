<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

class GitTag extends GitObject
{
    protected function serialize(GitRepository $repo) {}

    protected function deserialize(mixed $data) {}
}
