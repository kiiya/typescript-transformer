<?php

namespace Spatie\TypeScriptTransformer\Actions;

use Spatie\TypeScriptTransformer\Support\TypeScriptTransformerLog;
use Spatie\TypeScriptTransformer\Support\WrittenFile;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;

class FormatFilesAction
{
    public function __construct(
        public TypeScriptTransformerConfig $config,
    ) {
    }

    /**
     * @param array<WrittenFile> $writtenFiles
     */
    public function execute(array $writtenFiles): void
    {
        if ($this->config->formatter === null) {
            return;
        }

        $this->config->formatter->format(
            array_map(fn (WrittenFile $writtenFile) => $writtenFile->path, $writtenFiles)
        );
    }
}
