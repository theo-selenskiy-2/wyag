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

    abstract public function serialize();
    abstract public function deserialize(mixed $data);
    abstract public function getFormat(): string;
}
