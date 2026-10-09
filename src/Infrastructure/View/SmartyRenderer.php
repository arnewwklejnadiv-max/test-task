<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use App\Http\ViewInterface;
use Smarty\Smarty;

final class SmartyRenderer implements ViewInterface
{
    public function __construct(
        private string $templateDir,
        private string $compileDir,
        private bool $production,
    ) {}

    /** @param array<string, mixed> $data */
    public function render(string $view, array $data = []): string
    {
        if (!is_dir($this->compileDir) && !mkdir($this->compileDir, 0775, true) && !is_dir($this->compileDir)) {
            throw new \RuntimeException('Cannot create the Smarty compile directory');
        }

        $smarty = new Smarty();
        $smarty->setEscapeHtml(true);
        $smarty->setForceCompile(false);
        $smarty->setCompileCheck($this->production ? Smarty::COMPILECHECK_OFF : Smarty::COMPILECHECK_ON);
        $smarty->setTemplateDir($this->templateDir);
        $smarty->setCompileDir($this->compileDir);
        foreach ($data as $key => $value) {
            $smarty->assign($key, $value);
        }

        return $smarty->fetch($view . '.tpl');
    }
}
