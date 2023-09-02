<?php

namespace App\libs;

use Clickfwd\Yoyo\Blade\Application;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Jenssegers\Blade\Blade;

/**
 * Helper class to use laravels blade engine outside of laravel
 * Massive credits to @lexdubyna for this solution
 * @link https://laracasts.com/discuss/channels/general-discussion/correct-way-to-use-illuminateview-outside-of-laravel?page=1&replyId=626768
 */
class ViewEngine
{
    private static Application $container;
    private static Blade $engine;

    /**
     * 
     * @param string $viewPath 
     * @param string $cachePath 
     * @return void 
     */
    public function __construct(public string $viewPath, public string $cachePath)
    {
    }

    /**
     * 
     * @return void 
     */
    public function boot()
    {
        self::$container = Application::getInstance();

        self::$container->bind(ApplicationContract::class, Application::class);

        self::$container->alias('view', ViewFactory::class);

        self::$engine = new Blade([$this->viewPath], $this->cachePath, self::$container);
    }

    /**
     * 
     * @return Blade
     */
    public function get()
    {
        if (!isset(self::$engine)) {
            $this->boot();
        }

        return self::$engine;
    }

    /**
     * 
     * @param string $name 
     * @param array $data 
     * @return string 
     */
    public function render(string $name, array $data)
    {
        echo self::$engine->render($name, $data);
    }
}
