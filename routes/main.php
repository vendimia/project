<?php

use Vendimia\Routing\Rule;
use Vendimia\Http\{Request, Response};
use Vendimia\Core\AssetManager;

return [
    Rule::default()->view('::welcome'),
];