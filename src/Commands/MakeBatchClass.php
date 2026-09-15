<?php

namespace RobinThijsen\NotificationBatcher\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RobinThijsen\NotificationBatcher\Concerns\CommandHelper;
use RobinThijsen\NotificationBatcher\Batcher;
use function Laravel\Prompts\text;

class MakeBatchClass extends Command
{
    use CommandHelper;

    public $signature = 'make:batch {className?}';

    public $description = 'Create a batch class.';

    public function handle(): int
    {
        $className = $this->argument('className') ?? null;

        if (empty($className)) {
            $className = text(
                label: "What is the name of the batch you want to create?",
                required: true,
                hint: "e.g. NewMessageBatch",
            );
        }

        if (empty($className)) {
            $this->displayError("Invalid class name.");
            return Command::FAILURE;
        }

        $className = Str::pascal($className);

        $batches = $this->listOfBatches();

        $path = $this->resolvePath();
        $filename = $className . '.php';
        $fullpath = $path . '/' . $filename;

        if (in_array($className, $batches)) {
            $this->displayError("Batch [$fullpath] already exists!");
            return Command::FAILURE;
        }

        $stubPath = $this->resolveStub();

        if (empty($stubPath) || !File::exists($stubPath)) {
            $this->displayError("Stub [$stubPath] does not exists!");
            return Command::FAILURE;
        }

        $rewroteContent = str_replace(
            [
                '{{ namespace }}',
                '{{ className }}',
            ],
            [
                $this->resolveNamespace(),
                $className,
            ],
            file_get_contents($stubPath),
        );

        File::ensureDirectoryExists($path);
        File::put($fullpath, $rewroteContent);

        if (!File::exists($fullpath)) {
            return Command::FAILURE;
        }

        $this->displayInfo("Batch [$fullpath] created successfully.");
        return Command::SUCCESS;
    }
}