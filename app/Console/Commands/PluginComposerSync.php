<?php

namespace App\Console\Commands;

use Xpressengine\Plugin\Composer\ComposerFileWriter;

class PluginComposerSync extends PluginCommand
{
    /**
     * The console command name.
     * php artisan plugin:sync-composer
     *
     * @var string
     */
    protected $signature = 'plugin:sync-composer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sync plugins' composer.json with real plugin list";

    /**
     * Create a new controller creator command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param ComposerFileWriter $writer
     *
     * @return bool|null
     * @throws \Exception
     */
    public function fire(ComposerFileWriter $writer)
    {
        // php artisan plugin:sync-composer

        // sync
        $writer->resolvePlugins()->setFixMode()->write();

        $this->output->success("플러그인 설치 정보를 composer 파일[".$writer->getPath()."]과 동기화했습니다.");
    }
}
