##### 图片验证码生成器
> 适用于thinkphp、laravel、lumen、hyperf、easyswoole等框架

##### 使用

1. 使用加载文件配置参数（参考config.php文件）, 可搭配各个框架的config配置使用
    
        $config = [
            'charset' => '123467890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', // 字符
            'length' => 4, // 位数
            'width' => 150, // 宽度
            'height' => 40, // 高度
            'fontSize' => null, // 字体大小
            'bgColor' => '#F3FBFE', // 背景颜色 [243, 251, 254]
            'fontColor' => null, // 字体颜色 [0, 0, 0]
            'codeFont' => '', // 字体路径,如：/fonts/xxx.ttf, isRandomFont => true 则该字体设置无效
            'borderColor' => null, // 边框颜色 如 #ccc 或 [243, 251, 254]
            'isBorder' => false, // 是否画边框
            'isRandomFont' => false, // 是否使用随机字体，开启后优先使用 fonts 配置的字体，如 fonts 未设置则使用包内自带的随机字体
            'isRandomLightBgColor' => false, // 是否使用随机浅色背景，开启后背景颜色 bgColor 无效
            'isDrawCurve' => true, // 是否画干扰曲线
            'isDrawNoise' => true, // 是否画干扰噪点
            'isDrawLine' => true, // 是否画干扰线
            'fonts' => [], // 其他字体
            'noiseLevel' => 10, // 噪点级别，值越大越密集
        ];
        // 实例化对象
        $captchaGenerator = new \RealTruths\Captcha\Generator\CaptchaGenerator($config);
        
2. 使用CaptchaConfig对象配置参数

        $captchaConfig = new \RealTruths\Captcha\Generator\CaptchaConfig();
        $captchaConfig->setWidth(150)->setHeight(); // 可设置如1所示的所有参数
        // 实例化对象
        $captchaGenerator = new \RealTruths\Captcha\Generator\CaptchaGenerator($captchaConfig);

3. 生成及获取验证码
   
        $captchaGenerator->generate(); // 生成随机验证码
        // $captchaGenerator->generate('abcd'); // 生成指定验证码
        $code = $captchaGenerator->getCode(); // 获取验证码内容
        $img = $captchaGenerator->getBase64(); // 获取验证码base64图片



        

