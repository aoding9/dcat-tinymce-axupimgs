<?php

namespace Aoding9\Dcat\TinymceAxupimgs;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field\Editor;
use Illuminate\Support\ServiceProvider;

class TinymceServiceProvider extends ServiceProvider {
    /**
     * {@inheritdoc}
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [__DIR__ . '/../resources/assets/axupimgs' => public_path('vendor/dcat-admin/dcat/plugins/tinymce/plugins/axupimgs')],
                'dcat-tinymce-axupimgs'
            );
        }
        
        Admin::booting(function() {
            Editor::resolving(function(Editor $editor) {
                $editor->imageDirectory(config('admin.upload.directory.image_editor') ?? 'images/editor/' . today()->toDateString())
                       ->height(400);
                $method = (new \ReflectionClass(get_class($editor)))->getMethod('defaultImageUploadUrl');
                $method->setAccessible(true);
                $url = $method->invoke($editor);
                $editor->options([
                                     'images_upload_handler' =>
                                         <<<JS
                                  function (blobInfo, succFun, failFun) {
       var xhr, formData;
       var file = blobInfo.blob();
       xhr = new XMLHttpRequest();
       xhr.withCredentials = false;
       xhr.open('POST', "$url"); // 图片上传url
       xhr.onload = function() {
           var json;
           if (xhr.status != 200) {
               failFun('HTTP Error: ' + xhr.status);
               return;
           }
           json = JSON.parse(xhr.responseText);
           if (!json || typeof json.location != 'string') {
               failFun('Invalid JSON: ' + xhr.responseText);
               return;
           }
           succFun(json.location);
       };
       formData = new FormData();
       formData.append('file', file, file.name );
       xhr.send(formData);
   }
JS
                          
                                 ]);
            });
        });
    }
}