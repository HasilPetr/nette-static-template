<?php

namespace App\Macros;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Strings;

class ControlBlock extends MacroSet
{
    public static function install(Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('controlblock', array(__CLASS__, 'controlblock'), array($set, 'controlblock_end'));
    }

    static function controlblock(MacroNode $node, PhpWriter $writer)
    {
        return 'ob_start()';
    }

    function controlblock_end(MacroNode $node, PhpWriter $writer)
    {
        $words = $node->tokenizer->fetchWords();
        if (!$words) {
            throw new CompileException('Missing control name in {controlblock}');
        }
        $name = $writer->formatWord($words[0]);
        $method = isset($words[1]) ? ucfirst($words[1]) : '';
        $method = Strings::match($method, '#^\w*\z#') ? "render$method" : "{\"render$method\"}";
        $param = $writer->formatArray();
        if (!Strings::contains($node->args, '=>')) {
            $param = substr($param, $param[0] === '[' ? 1 : 6, -1); // removes array() or []
        }
        return '$content' . $writer->write(' = %modify(ob_get_clean());') . ($name[0] === '$' ? "if (is_object($name)) \$_l->tmp = $name; else " : '')
        . '$_l->tmp = $_control->getComponent(' . $name . '); '
        . 'if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); '
        . ($node->modifiers === '' ? "\$_l->tmp->$method(\$content, $param)" : $writer->write("ob_start(function () {}); \$_l->tmp->$method(\$content, $param); echo %modify(ob_get_clean())"));
    }

}