<?php

namespace App\Services;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\MarkdownConverter;

class GuideService
{
    protected string $guidesPath;

    protected ?MarkdownConverter $markdownConverter = null;

    public function __construct(
        protected GuideDataLoaderService $dataLoader,
    ) {
        $this->guidesPath = resource_path('views/guides');
    }

    public function getGuide(string $category, string $slug): ?array
    {
        $filePath = $this->resolveGuideFile($category, $slug);

        if (! $filePath) {
            return null;
        }

        $source = file_get_contents($filePath);
        $parsed = $this->parseFrontMatter($source);
        $frontmatter = $parsed['frontmatter'];

        $data = [];
        if (isset($frontmatter['data']) && is_array($frontmatter['data'])) {
            $data = $this->dataLoader->load($frontmatter['data']);
        }

        try {
            $fullMarkdown = $this->renderGuideContent($filePath, $data, $parsed['content']);
            $html = $this->renderMarkdown($fullMarkdown);
        } catch (\Exception $e) {
            \Log::error('Guide rendering error: '.$e->getMessage(), [
                'filePath' => $filePath,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }

        return array_merge(
            $frontmatter,
            [
                'slug' => $slug,
                'html' => $html,
            ]
        );
    }

    /**
     * List all guides in a category
     */
    public function listByCategory(string $category): array
    {
        $categoryPath = $this->guidesPath.'/'.$category;

        if (! is_dir($categoryPath)) {
            return [];
        }

        $guides = [];
        $files = glob($categoryPath.'/*.{blade.php,md,php}', GLOB_BRACE);

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $slug = str_replace('.blade', '', $filename);
            $guide = $this->getGuide($category, $slug);
            if ($guide) {
                $guides[] = $guide;
            }
        }

        return $guides;
    }

    /**
     * Get all available categories
     */
    public function getCategories(): array
    {
        $categories = [];
        $items = scandir($this->guidesPath);

        foreach ($items as $item) {
            $path = $this->guidesPath.'/'.$item;
            if (is_dir($path) && $item !== '.' && $item !== '..') {
                $categories[] = $item;
            }
        }

        return $categories;
    }

    /**
     * Render guide source with Blade data applied
     */
    protected function renderGuideContent(string $filePath, array $data, string $source): string
    {
        if (Str::endsWith($filePath, '.md')) {
            return Blade::render($source, $data);
        }

        return View::file($filePath, $data)->render();
    }

    protected function renderMarkdown(string $markdown): string
    {
        $converter = $this->getMarkdownConverter();

        return (string) $converter->convert($markdown);
    }

    protected function getMarkdownConverter(): MarkdownConverter
    {
        if ($this->markdownConverter) {
            return $this->markdownConverter;
        }

        $config = [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
            'heading_permalink' => [
                'html_class' => 'guide-heading-anchor',
                'id_prefix' => 'guide',
                'fragment_prefix' => 'guide',
                'insert' => 'before',
                'min_heading_level' => 2,
                'max_heading_level' => 4,
                'title' => 'Permalink',
                'symbol' => '',
            ],
            'table_of_contents' => [
                'html_class' => 'guide-toc',
                'position' => 'top',
                'style' => 'bullet',
                'min_heading_level' => 2,
                'max_heading_level' => 4,
                'normalize' => 'relative',
                'placeholder' => null,
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new FrontMatterExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new HeadingPermalinkExtension);
        $environment->addExtension(new TableOfContentsExtension);

        $this->markdownConverter = new MarkdownConverter($environment);

        return $this->markdownConverter;
    }

    protected function resolveGuideFile(string $category, string $slug): ?string
    {
        $base = $this->guidesPath.'/'.$category.'/'.$slug.'.md';

        if (file_exists($base)) {
            return $base;
        }

        return null;
    }

    /**
     * @return array{frontmatter: array, content: string}
     */
    protected function parseFrontMatter(string $markdown): array
    {
        $extension = new FrontMatterExtension;
        $result = $extension->getFrontMatterParser()->parse($markdown);
        $frontmatter = $result->getFrontMatter();

        return [
            'frontmatter' => is_array($frontmatter) ? $frontmatter : [],
            'content' => $result->getContent(),
        ];
    }
}
