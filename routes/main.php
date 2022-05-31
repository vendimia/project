<?php

use Vendimia\Routing\Rule;
use Vendimia\Http\{Request, Response};
use Vendimia\Core\AssetManager;

return [

    // Change this rule for your default view or controller
    Rule::default()->view('welcome'),

    // CSS and JS assets processors
    Rule::path('assets')->includeFromClass(AssetManager\Controller::class),

];