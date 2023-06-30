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
                $url = $this->getUrl($editor);
                $editor->options([
                                     'plugins'               => [
                                         'advlist',
                                         'autolink',
                                         'link',
                                         'image',
                                         'axupimgs',
                                         'media',
                                         'lists',
                                         'preview',
                                         'code',
                                         'help',
                                         'fullscreen',
                                         'table',
                                         'autoresize',
                                         'codesample',
                                     ],
                                     'toolbar'               => [
                                         'undo redo | preview fullscreen | styleselect | fontsizeselect bold italic underline strikethrough forecolor backcolor | link image axupimgs media blockquote removeformat codesample',
                                         'alignleft aligncenter alignright  alignjustify| indent outdent bullist numlist table subscript superscript | code',
                                     ],// plugins和toolbar添加axupimgs
                                     'images_upload_handler' => \Dcat\Admin\Support\JavaScript::make(
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
                                     ), // 直接写会被当成字符串，而不是js函数
                
                                 ]);
            });
        });
    }
    
    /**
     * @Desc 获取图片上传接口url，如果admin.php没配置image_upload_url_editor则使用dcat的默认接口
     * @param $editor
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     * @throws \ReflectionException
     * @Date 2023/6/30 17:37
     */
    public function getUrl($editor) {
        if($url = config('admin.upload.directory.image_upload_url_editor')){
            return $url;
        }
        $method = (new \ReflectionClass(get_class($editor)))->getMethod('defaultImageUploadUrl');
        $method->setAccessible(true);
        return $url = $method->invoke($editor);
    }
}