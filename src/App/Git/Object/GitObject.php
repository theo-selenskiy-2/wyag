<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

abstract class GitObject
{
    public function __construct(?string $data = null)
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

    abstract public function serialize(): string;
    abstract public function deserialize(string $data): void;
    abstract public function getFormat(): string;
}
