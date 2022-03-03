<?php declare(strict_types=1);

namespace Soyhuce\Testing\Commands;

use Illuminate\Console\Command;

class TestingCommand extends Command
{
    public $signature = 'laravel-testing';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
