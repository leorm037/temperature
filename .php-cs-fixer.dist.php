<?php

$header = <<<'EOF'
    This file is part of Temperature.

    (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>

    This source file is subject to the MIT license that is bundled
    with this source code in the file LICENSE.
    EOF;

$finder = (new PhpCsFixer\Finder())
        ->in(__DIR__)
        ->exclude('var')
;

return (new PhpCsFixer\Config())
                ->setRiskyAllowed(true)
                ->setUsingCache(false)
                ->setRules([
                    '@Symfony' => true,
                    'header_comment' => ['header' => $header,],
                    'protected_to_private' => false,
                    'modernize_strpos' => true, // needs PHP 8+ or polyfill
                ])
                ->setFinder($finder)
;
