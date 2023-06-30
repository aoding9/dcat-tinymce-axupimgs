### 简介

dcat扩展：tinymce富文本多图上传

效果：

![Laravel](https://cdn.learnku.com/uploads/images/202306/09/78338/hebjBHjITk.png!large)

![Laravel](https://cdn.learnku.com/uploads/images/202306/09/78338/gfJMOTqHAp.png!large)

![Laravel](https://cdn.learnku.com/uploads/images/202306/09/78338/au0JseBO00.png!large)

![Laravel](https://cdn.learnku.com/uploads/images/202306/09/78338/EIJa30Gfl1.png!large)

### 安装

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


### 配置

修改config/admin.php，在directory中添加image_editor配置项，不填则上传到`'images/editor/' . today()->toDateString()`

image_upload_url_editor不填则使用dcat默认图片上传接口，填写则使用自定义接口

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
            'image_editor' => 'images/editor/' . today()->toDateString(), // 多图上传的路径
            'image_upload_url_editor' => null, // 多图上传自定义接口
        ]

```




### 使用
在控制器form方法中使用editor即可
```php
// ...
      $form->editor('content');

```