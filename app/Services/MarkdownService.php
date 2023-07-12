<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;

class MarkdownService
{
    /**
     * Convert a markdown file to HTML considering app locale.
     *
     * @param string $filename The name of the markdown file without the extension.
     * 
     * @return string Translated markdown file content or the filename if the file does not exist.
     */
    public function trans(string $filename): string
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');
        $filePaths = [
            lang_path("{$locale}/md/{$filename}.md"),
            lang_path("{$fallbackLocale}/md/{$filename}.md"),
        ];

        foreach ($filePaths as $path) {
            if (File::exists($path)) {
                $content = File::get($path);

                return $this->parseMarkdown($content);
            }
        }

        return $filename;
    }

    /**
     * Parse markdown content to HTML with custom styles.
     *
     * @param string $content The markdown content to be converted.
     * 
     * @return string Converted HTML content.
     */
    public function parseMarkdown(string $content): string
    {
        $config = [
            'default_attributes' => [
                Heading::class => ['class' => fn (Heading $node) => $this->getHeadingClasses($node)],
                Code::class => ['class' => 'border p-2 m-2 rounded-lg'],
                Table::class => ['class' => 'table'],
                Paragraph::class => ['class' => 'mb-4 font-light'],
                Link::class => ['class' => 'text-link', 'target' => '_blank'],
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        $converter = new MarkdownConverter($environment);

        return $converter->convert($content);
    }

    /**
     * Return classes based on heading level.
     *
     * @param Heading $node The heading node.
     * 
     * @return string|null The CSS classes to apply.
     */
    private function getHeadingClasses(Heading $node): ?string
    {
        switch ($node->getLevel()) {
            case 1:
                return 'mb-4 text-4xl tracking-tight font-bold text-gray-900 dark:text-white';
            case 2:
                return 'mb-2 text-2xl tracking-tight font-bold text-gray-900 dark:text-white';
            default:
                return null;
        }
    }
}
