<?php

namespace App\Services\Contract;

interface UsersCsvExportContract
{
    public function generate(): string|false;
}
