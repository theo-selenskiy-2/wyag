<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

class GitBlob extends GitObject
{
    private string $data;

    public function __construct(?string $data = null)
    {
        parent::__construct($data);
    }
    
    public function serialize(): string 
    {
        return $this->data;
    }

    public function deserialize(string $data): void
    {
        $this->data = $data;
    }

    public function getFormat(): string
    {
        return "blob";
    }
}
