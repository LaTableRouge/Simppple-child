<?php

$config = new TwigCsFixer\Config\Config();

$ruleset = new TwigCsFixer\Ruleset\Ruleset();
$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());
$ruleset->overrideRule(new TwigCsFixer\Rules\Whitespace\IndentRule(
    2,
    true
));

$finder = new TwigCsFixer\File\Finder();
$finder->in(dirname(__DIR__));

return $config
->setRuleset($ruleset)
->setFinder($finder);
