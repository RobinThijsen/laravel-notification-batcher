<?php

namespace OANNA\NotificationBatcher\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Termwind\render;

trait CommandHelper
{
    public function resolvePath()
    {
        return app('batcher')->resolveDirectoryPath();
    }

    public function resolveNamespace()
    {
        $relative = ltrim(app('batcher')->resolveRelativePath(), '/');
        return implode('\\', array_map(fn($p) => Str::pascal($p), explode('/', $relative)));
    }

    public function resolveStub()
    {
        return app('batcher')->resolveStubPath();
    }

    public function listOfBatches()
    {
        $path = $this->resolvePath();
        if (! File::isDirectory($path) || File::isEmptyDirectory($path)) {
            return [];
        }

        return collect(File::allFiles($path))
            ->filter(fn ($f) => $f->getExtension() === 'php') // Only search for PHP files
            ->map(fn ($f) => $f->getBasename('.php')) // Get the file name without extension
            ->values()
            ->all();
    }

    public function displayInfo($message)
    {
        render($this->generateHTML($this->prefixMessage('bg-blue', 'Info', $message)));
    }

    public function displayError($message)
    {
        render($this->generateHTML($this->prefixMessage('bg-red', 'Error', $message)));
    }

    private function pregMessage($message)
    {
        $message = preg_replace('/\[([^\]]+)\]/', '<span class="font-bold text-white">[$1]</span>', $message);
        return "<div class=\"text-gray-400\">$message</div>";
    }

    private function prefixMessage($color, $content, $message)
    {
        return "<div class=\"px-1 $color text-black uppercase\">$content</div> $message";
    }

    private function generateHTML($message)
    {
        return <<<HTML
<div class="px-2 py-1">{$this->pregMessage($message)}</div>
HTML;
    }
}