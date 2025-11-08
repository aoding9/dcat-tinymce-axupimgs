## 简介

dcat扩展：tinymce富文本多图上传

效果：

![Laravel](https://oss.tianwe.net/aoding9/0.png)

![Laravel](https://oss.tianwe.net/aoding9/1.png)

![Laravel](https://oss.tianwe.net/aoding9/2.png)

![Laravel](https://oss.tianwe.net/aoding9/3.png)

## 安装

第一步：`composer require aoding9/dcat-tinymce-axupimgs`

如果安装失败，切换官方源（阿里云镜像试了装不上）

`composer config repo.packagist composer https://packagist.org`

因为官方源下载比较慢，国内镜像又有各种问题可能导致安装失败，可以把以下代码添加到composer.json，直接从github安装
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/aoding9/dcat-tinymce-axupimgs"
    }
  ]
}
```




第二步：`php artisan vendor:publish --provider="Aoding9\Dcat\TinymceAxupimgs\TinymceServiceProvider`

发布静态资源到public目录


## 配置

修改config/admin.php，在directory中添加

image_editor：自定义上传目录，不填则传到Editor组件默认上传目录`'tinymce/images`

image_upload_url_editor：自定义上传接口，不填则使用Editor组件图片上传接口

```php

//...
'upload'=> [

        // Disk in `config/filesystem.php`.
        // 如果要上传oss，把disk改成qiniu或者别的驱动，然后配置好oss参数就行了
        'disk'      => 'admin',

        // Image and file upload path under the disk above.
        'directory' => [
            'image'        => 'images',
            'file'         => 'files',
            'image_editor' => 'images/editor/' . today()->toDateString(), // 全局修改多图上传目录
            'image_upload_url_editor' => null, // 全局修改多图上传接口
        ]

```




## 使用
在控制器form方法中使用editor即可
```php
// ...
      $form->editor('content');
      
      // ->imageDirectory('custom_directory') 局部修改图片上传目录
      // ->imageUrl('custom_api_url') 局部修改图片上传接口

```

## 更新

- 1.0.1 （2023-6-30）
  - 配置项新增image_upload_url_editor，用于修改图片上传接口
- 1.0.2（2025-10-22）
  - 支持php8
- 1.0.3（2025-11-08）
  - 调整图片默认上传目录，若未配置image_editor，则与dcat默认的富文本图片上传目录一致，即'tinymce/images`，可通过imageDirectory改变。
  - 修复控制器中使用imageDirectory修改自定义目录不生效的bug