<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.wang-editor';

    protected static $css = [
        '/vendor/wangEditor-3.0.15/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/qiniu/plupload/moxie.js',
        '/vendor/qiniu/plupload/plupload.dev.js',
        '/vendor/qiniu/qiniu.js',
        '/vendor/wangEditor-3.0.15/release/wangEditor.min.js',
    ];

    /**
     * 上传到服务器 和下面的 render 二选一
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render2()
    {
        $name = $this->formatName($this->column);
        $this->script = <<<EOT

var E = window.wangEditor
var editor = new E('#{$this->id}');
editor.customConfig.zIndex = 0
editor.customConfig.debug = true
editor.customConfig.uploadFileName = 'wang-editor-file'
editor.customConfig.uploadImgServer = '/upload'
editor.customConfig.onchange = function (html) {
    $('input[name=$name]').val(html);
}


editor.customConfig.uploadImgHooks = {
    before: function (xhr, editor, files) {
    },
    success: function (xhr, editor, result) {
    },
    fail: function (xhr, editor, result) {
    },
    error: function (xhr, editor) {
    },
    timeout: function (xhr, editor) {
    },

    customInsert: function (insertImg, result, editor) {
        var url = result.url
        console.log(url)
        insertImg(url)
    }

}

editor.create()

EOT;
        return parent::render();
    }


    public function render()
    {
        $name = $this->formatName($this->column);
        $this->script = <<<EOT

var E = window.wangEditor
var editor = new E('#{$this->id}');
// 允许上传到七牛云存储
editor.customConfig.qiniu = true
editor.customConfig.linkImgCallback = function (url) {
    console.log(url)
}

editor.create()

// 初始化七牛上传
uploadInit()




// 初始化七牛上传的方法
function uploadInit() {
    // 获取相关 DOM 节点的 ID
    var btnId = editor.imgMenuId;
    var containerId = editor.toolbarElemId;
    var textElemId = editor.textElemId;
console.log(234);
    // 创建上传对象
    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',    //上传模式,依次退化
        browse_button: btnId,       //上传选择的点选按钮，**必需**
        uptoken_url: '/qiniu-token',
            //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
         //uptoken : 'QmVOjLPdaEujy9AT3crbZNkcgz5QjVZH3Bl5b-nE:coeIPtzrsMg1GpKbevpXmCEQt8g=:eyJzY29wZSI6Im9lMzYwIiwiZGVhZGxpbmUiOjE1MTI3MzA0MjJ9',
            //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
        unique_names: true,
            // 默认 false，key为文件名。若开启该选项，SDK会为每个文件自动生成key（文件名）
        // save_key: true,
            // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK在前端将不对key进行任何处理
        domain: 'http://ozgti7vh2.bkt.clouddn.com/',
            //bucket 域名，下载资源时用到，**必需**
        container: containerId,           //上传区域DOM ID，默认是browser_button的父元素，
        max_file_size: '100mb',           //最大文件体积限制
        flash_swf_url: '/vendor/qiniu/plupload/Moxie.swf',  //引入flash,相对路径
        filters: {
                mime_types: [
                  //只允许上传图片文件 （注意，extensions中，逗号后面不要加空格）
                  { title: "图片文件", extensions: "jpg,gif,png,bmp" }
                ]
        },
        max_retries: 3,                   //上传失败最大重试次数
        dragdrop: true,                   //开启可拖曳上传
        drop_element: textElemId,        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
        chunk_size: '4mb',                //分块上传时，每片的体积
        auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
        init: {
            'FilesAdded': function(up, files) {
                plupload.each(files, function(file) {
                    // 文件添加进队列后,处理相关的事情
                    printLog('on FilesAdded');
                });
            },
            'BeforeUpload': function(up, file) {
                // 每个文件上传前,处理相关的事情
                printLog('on BeforeUpload');
            },
            'UploadProgress': function(up, file) {
                // 显示进度
                printLog('进度 ' + file.percent)
            },
            'FileUploaded': function(up, file, info) {
                // 每个文件上传成功后,处理相关的事情
                // 其中 info 是文件上传成功后，服务端返回的json，形式如
                // {
                //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                //    "key": "gogopher.jpg"
                //  }
                printLog(info);
                // 参考http://developer.qiniu.com/docs/v6/api/overview/up/response/simple-response.html
                
                var domain = up.getOption('domain');
                var res = $.parseJSON(info);
                var sourceLink = domain + res.key; //获取上传成功后的文件的Url

                printLog(sourceLink);

                // 插入图片到editor
                editor.cmd.do('insertHtml', '<img src="' + sourceLink + '" style="max-width:100%;"/>')
            },
            'Error': function(up, err, errTip) {
                //上传出错时,处理相关的事情
                printLog('on Error');
            },
            'UploadComplete': function() {
                //队列文件处理完毕后,处理相关的事情
                printLog('on UploadComplete');
            }
            // Key 函数如果有需要自行配置，无特殊需要请注释
            //,
            // 'Key': function(up, file) {
            //     // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
            //     // 该配置必须要在 unique_names: false , save_key: false 时才生效
            //     var key = "";
            //     // do something with key here
            //     return key
            // }
        }
        // domain 为七牛空间（bucket)对应的域名，选择某个空间后，可通过"空间设置->基本设置->域名设置"查看获取
        // uploader 为一个plupload对象，继承了所有plupload的方法，参考http://plupload.com/docs
    });
}

// 封装 console.log 函数
function printLog(title, info) {
    window.console && console.log(title, info);
}


EOT;
        return parent::render();

    }
}
