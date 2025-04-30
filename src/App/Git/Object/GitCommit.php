<?php

namespace Console\App\Git\Object;

use function Console\App\utils\kvlm_parse;
use function Console\App\utils\kvlm_serialize;

class GitCommit extends GitObject
{
    private array $data;

    public function __construct(?string $data = null)
    {
        parent::__construct($data);
    }
    
    public function serialize(): string 
    {
        return kvlm_serialize($this->data);
    }

    public function deserialize(string $data): void
    {
        $this->data = kvlm_parse($data);
    }

    public function getFormat(): string
    {
        return "commit";
    }

    public function getKvlm(): array
    {
        return $this->data;
    }
}
