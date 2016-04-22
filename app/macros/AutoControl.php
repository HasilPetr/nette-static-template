<?php

namespace App\Macros;

use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Strings;

class AutoControl extends MacroSet
{

    const _NAMESPACE = '\\App\\Controls\\';

    public static function install(Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('m', array(__CLASS__, 'm'), array($set, 'm_end'));
    }

    static function m(MacroNode $node, PhpWriter $writer)
    {
        return 'ob_start()';
    }

    function m_end(MacroNode $node, PhpWriter $writer)
    {
        $words = $node->tokenizer->fetchWords();
        if (!$words) {
            throw new CompileException('Missing control name in {m}');
        }
        $name = $writer->formatWord($words[0]);
        $method = isset($words[1]) ? ucfirst($words[1]) : '';
        $method = Strings::match($method, '#^\w*\z#') ? "render$method" : "{\"render$method\"}";
        $param = $writer->formatArray();
        if (!Strings::contains($node->args, '=>')) {
            $param = substr($param, $param[0] === '[' ? 1 : 6, -1); // removes array() or []
        }
        return '$_content' . $writer->write(' = %modify(ob_get_clean());') . ($name[0] === '$' ? "if (is_object($name)) \$_l->tmp = $name; else " : '')
        . 'if (!isset($_control->components[' . $name . '])) { $_l->tmp = new ' . self::_NAMESPACE . ucfirst($words[0]) . '; $_control->addComponent($_l->tmp, ' . $name . '); } else $_l->tmp = $_control->components[' . $name . '];'
        . 'if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); '
        . ($node->modifiers === '' ? "\$_l->tmp->$method(\$_content, $param)" : $writer->write("ob_start(function () {}); \$_l->tmp->$method(\$_content, $param); echo %modify(ob_get_clean())"));
    }

}