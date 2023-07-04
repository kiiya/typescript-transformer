<?php

namespace Spatie\TypeScriptTransformer\Actions;

use Spatie\TypeScriptTransformer\Support\TypeScriptTransformerLog;
use Spatie\TypeScriptTransformer\Support\WrittenFile;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;

class FormatFilesAction
{
    public function __construct(
        public TypeScriptTransformerConfig $config,
        public TypeScriptTransformerLog $log,
    ) {
    }

    /**
     * @param  array<WrittenFile>  $writtenFiles
     */
    public function execute(array $writtenFiles): void
    {

    }
}