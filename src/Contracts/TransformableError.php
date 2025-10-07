<?php

namespace O21\RestfulForLaravel\Contracts;

interface TransformableError
{
    public function code(): string;

    public function message(): string;

    public function meta(): array;
}
