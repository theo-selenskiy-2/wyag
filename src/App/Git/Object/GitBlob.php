<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

class GitBlob extends GitObject
{
    private mixed $data;
    
    public function serialize() {
        return $this->data;
    }

    public function deserialize(mixed $data) {
        $this->data = $data;
    }

    public function getFormat(): string
    {
        return "blob";
    }

    public function getData() {
        return $this->data;
    }
}
