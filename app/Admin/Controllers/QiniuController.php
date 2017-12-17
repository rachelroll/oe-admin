<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Qiniu\Auth;

class QiniuController extends Controller
{
    use ModelForm;

    public function token()
    {
        $accessKey = env('QINIU_AK');
        $secretKey = env('QINIU_SK');
        // 初始化签权对象
        $auth = new Auth($accessKey, $secretKey);
        $bucket = env('QINIU_BT');
        // 生成上传Token
        $token = $auth->uploadToken($bucket);
        $data = [
            'uptoken' => $token,
        ];
        return response()->json($data);
    }
}
