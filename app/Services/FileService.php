<?php

namespace App\Services;

class FileService
{
    const FILE_TYPE = ['avatar', 'product', 'banner'];
    const FILE_ACCEPT = ['jpg', 'jpeg', 'png'];
    const FILE_COMPERSS = ['jpg', 'jpeg', 'png'];
    const MAX_OFFSET = 1200;

    public function upload($file, $cate = 'temp', $prev='', $ext = '')
    {
        if (!in_array($cate, self::FILE_TYPE)) return false;
        $tmpname = explode('.', $file['name']);
        $extension = $tmpname[1] ?? ''; //后缀
        $tmpFile = $file['tmp_name']; //上传文件路径

        if (!in_array($extension, self::constant('FILE_ACCEPT')))
            return false;

        $imageService = \App::make('App/Services/ImageService');

        if ($cate == 'file') {
            if (!empty($ext))
                $extension = $ext;

            $saveUrl = ROOT_PATH.'public/image/'.$prev.'.'.$extension;
            $result = move_uploaded_file($tmpFile, $saveUrl);
            if ($result) {
                //压缩icon
                if ($extension == 'ico') {
                    $imageService->thumbImage($saveUrl, $saveUrl, 32, 32);
                } else {
                    $imageService->compressImg($saveUrl);
                }
                $returnData = [
                    'url' => str_replace(ROOT_PATH.'public/', Env('APP_DOMAIN'), $saveUrl).'?v='.time(),
                ];
            }
        } else {

            $hash = hash_file('md5', $tmpFile); //生成文件hash值
            $attachmentService = \App::make('App\Services\AttachmentService');

            $returnData = [];
            if ($attachmentService->isExitsHash($hash)) { 
                //文件已存在
                $returnData = $attachmentService->getAttachmentByHash($hash);
            } else {

                $insert = [
                    'name' => $hash,
                    'type' => $extension,
                    'cate' => $cate,
                    'source_name' => $this->utf8_unicode(substr($tmpname[0], strrpos($tmpname[0], '/') + 1)),
                    'size' => $file['size'] ?? filesize($file['name']),
                ];

                //保存文件地址
                $saveUrl = $cate;

                if (!empty($prev))
                	$saveUrl .= '/'.$prev;

                //中间路径
                $insert['path'] = $saveUrl;

                $saveUrl .= '/'.$hash.'.'.$extension;

                $saveUrl = ROOT_PATH.'public/file_center/'.$saveUrl;

                $savePath = pathinfo($saveUrl, PATHINFO_DIRNAME);

                //创建目录
                if (!is_dir($savePath)) {
                    mkdir($savePath, 0777, true);
                }

                $result = move_uploaded_file($tmpFile, $saveUrl);

                if ($result) {
                    //压缩文件
                    if (in_array($cate, ['banner'])) {
                        $imageService->compressImg($saveUrl, $this->pathUrl($saveUrl, '_thumb'));
                        $saveUrl = $this->pathUrl($saveUrl, '_thumb');
                    } elseif (in_array($cate, ['avatar', 'product', 'article'])) {
                        $imageService->thumbImage($saveUrl, $this->pathUrl($saveUrl, '800x800'), 800, 800);
                        $imageService->thumbImage($saveUrl, $this->pathUrl($saveUrl, '600x600'), 600, 600);
                        $imageService->thumbImage($saveUrl, $this->pathUrl($saveUrl, '300x300'), 300, 300);
                        $saveUrl = $this->pathUrl($saveUrl, '800x800');
                    }
                    //新增文件记录
                    $attachmentId = $attachmentService->addAttactment($insert);
                    if (!$attachmentId) return false;
                    $insert['attach_id'] = $attachmentId;
                    $insert['url'] = str_replace(ROOT_PATH.'public/', Env('APP_DOMAIN'), $saveUrl);
                }
                $returnData = $insert;
            }
        }

        return $returnData;
    }

    public function uploadUrlImage($url, $cate)
    {
        if (!in_array($cate, self::FILE_TYPE)) return false;

        $name = md5_file($url);
        $attachmentService = make('App\Services\AttachmentService');
        $info = $attachmentService->getAttachmentByName($name);
        if (!empty($info)) {
            return $info;
        }

        $path = ROOT_PATH . env('FILE_CENTER') . DS . $cate . DS;
        //创建目录
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $file = $path . $name . '.' . $ext;
        //存入文件
        file_put_contents($file, file_get_contents($url));
        $data = [
            'name' => $name,
            'type' => $ext,
            'cate' => $cate,
            'size' => filesize($file),
        ];
        $attachId = $attachmentService->create($data);
        $data['attach_id'] = $attachId;

        //图片缩略
        $imageService = make('App\Services\ImageService');
        $thumb = ['600', '400', '200', '100'];
        foreach ($thumb as $value) {
            $to = $path . $value . DS . $name . '.' . $ext;
            $imageService->thumbImage($file, $to, $value, $value);
        }
        return $data;
    }
}
