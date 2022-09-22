<?php

namespace Cockpit\Php\Common;

class DotEnv
{
    protected $path;

    public function __construct(string $path)
    {
        if (file_exists($path)) {
            $this->path = $path;
        }
    }

    public function load(): void
    {
        if (!is_readable($this->path)) {
            return;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name               = trim($name);
            $value              = trim($value);

            if (!getenv($name)) {
                putenv(sprintf('%s=%s', $name, $value));
            }
        }
    }
}
