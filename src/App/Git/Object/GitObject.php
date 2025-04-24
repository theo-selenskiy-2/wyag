<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

abstract class GitObject
{
    public function __construct(mixed $data)
    {
        if($data !== null) {
            $this->deserialize($data);
        } else {
            $this->init();
        }
    }

    public function init() {
        return;
    }

    abstract protected function serialize(GitRepository $repo);
    abstract protected function deserialize(mixed $data);
}