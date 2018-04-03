<?php

namespace App\Listeners;

use App\Events\BaseCacheEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Cache;
use Log;

class DataToCacheListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BaseCacheEvent  $event
     * @return void
     */
    public function handle(BaseCacheEvent $event)
    {
        $model = $event->model;
        // pr(get_class($model));exit;
        $key = md5(get_class($model)) . '_' . $model->id;
        Cache::put($key, json_encode($model->toArray()), 60*24*7);
        Log::useFiles(storage_path() . '/logs/cache.log');
        Log::info('保存数据到缓存成功！',['id' => $model->id, 'title' => $model->username]);
    }
}
