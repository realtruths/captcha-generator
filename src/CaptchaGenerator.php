<?php
/**
 * CaptchaGenerator.php
 *
 * author: RealTruths
 * DateTime: 2021/8/9 17:59
 */

namespace RealTruths\Captcha\Generator;

/**
 * Class CaptchaGenerator
 * @package RealTruths\Captcha\Generator
 */
class CaptchaGenerator
{
    /**
     * 图片画布
     * @var resource
     */
    private $imgCanvas;
    /**
     * 是否开启随机浅色
     * @var bool
     */
    public $isRandomLightBgColor = false;
    /**
     * 长度
     * @var int
     */
    private $width = 500;
    /**
     * 高度
     * @var int
     */
    private $height = 200;
    /**
     * 字体大小
     * @var int
     */
    private $fontSize = 20;
    /**
     * 验证码位数
     * @var int
     */
    private $length = 4;
    /**
     * 背景颜色
     * @var string
     */
    private $backgroundColor = [243, 251, 254];
    /**
     * 字体颜色
     * @var array [255,255,255]
     */
    private $fontColor = [];
    /**
     * 是否添加干扰线
     * @var bool
     */
    private $isDrawLine = false;
    /**
     * 是否启用曲线
     * @var bool
     */
    private $isDrawCurve = true;
    /**
     * 是否启用背景噪音
     * @var bool
     */
    private $isDrawNoise = true;
    /**
     * 字符串
     * @var string
     */
    private $charset = '123467890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    /**
     * 其他字体路径
     * @var array
     */
    private $fonts = [];
    /**
     * 验证码字体
     * @var string
     */
    private $codeFont;
    private $code;
    private $byte;

    public function __construct()
    {
        $fontSize = intval($this->width / $this->length * 1.5);
        if ($fontSize < $this->fontSize) $this->fontSize = $fontSize;
        // 加载字体
        $fonts = $this->loadFonts(__DIR__ . '/fonts/');
        $this->fonts && $fonts = $this->fonts;
        if (!$this->codeFont) $this->codeFont = $fonts[array_rand($fonts)];
    }

    public function generate(): self
    {
        // 创建空白画布
        $this->imgCanvas = imagecreate($this->width, $this->height);
        list($red, $blue, $green) = $this->isRandomLightBgColor ? $this->getRandomLightBgColor() : ($this->backgroundColor ?: $this->getRandomLightBgColor());
        // 设置背景颜色
        $this->backgroundColor = imagecolorallocate($this->imgCanvas, $red, $blue, $green);
        // 画干扰噪点
        $this->isDrawNoise && $this->drawNoise();
        // 画干扰曲线
        $this->isDrawCurve && $this->drawCurve();

        // 画验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        $code = [];
        for ($i = 0; $i < $this->length; $i++) {
            $code[$i] = $this->charset[mt_rand(0, strlen($this->charset) - 1)];
            $codeNX += mt_rand($this->fontSize * 1, $this->fontSize * 1.3);
            $color = mt_rand(50, 200);
            imagettftext($this->imgCanvas, $this->fontSize, mt_rand(-40, 40), $codeNX, $this->fontSize * 1.2, $color, $this->codeFont, $code[$i]);
        }
        $this->code = strtolower(implode('', $code));
        ob_start();
        imagepng($this->imgCanvas);
        $this->byte = ob_get_contents();
        ob_end_clean();
        imagedestroy($this->imgCanvas);
        return $this;
    }

    /**
     * 获取base64图片
     * @return string
     */
    public function getBase64(): string
    {
        return "data:image/png;base64," . base64_encode($this->byte);
    }

    /**
     * 获取验证码
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * 随机浅色
     * @return array
     */
    public function getRandomLightBgColor(): array
    {
        $colors[0] = 150 + mt_rand(1, 75);
        $colors[1] = 150 + mt_rand(1, 75);
        $colors[2] = 150 + mt_rand(1, 75);

        return $colors;
    }

    /**
     * 加载目录下字体文件
     * @param $fontPath
     * @return array
     */
    public function loadFonts($fontPath): array
    {
        $fonts = array_filter(array_slice(scandir($fontPath), 2), function ($file) use ($fontPath) {
            return is_file($fontPath . $file) && strcasecmp(pathinfo($file, PATHINFO_EXTENSION), 'ttf') === 0;
        });
        if (!empty($fonts)) {
            foreach ($fonts as &$font) {
                $font = $fontPath . $font;
            }
            unset($font);
        }
        return $fonts;
    }

    /**
     * 画干扰噪点
     */
    public function drawNoise(): void
    {
        $codeSet = '1234567890abcdefghijklmnopqrstuvwxyz';
        for ($i = 0; $i < 10; $i++) {
            list($red, $blue, $green) = $this->getRandomLightBgColor();
            $noiseColor = imagecolorallocate($this->imgCanvas, $red, $blue, $green);
            for ($j = 0; $j < 5; $j++) {
                // 画杂点
                imagestring($this->imgCanvas, 2, mt_rand(-10, $this->width), mt_rand(-10, $this->height), $codeSet[mt_rand(0, 35)], $noiseColor);
            }
        }
    }

    /**
     * 画干扰曲线
     */
    protected function drawCurve(): void
    {
        $py = 0;
        // 曲线前部分
        $A = mt_rand(1, $this->height / 2);// 振幅
        $b = mt_rand(-$this->height / 4, $this->height / 4); // Y轴方向偏移量
        $f = mt_rand(-$this->height / 4, $this->height / 4); // X轴方向偏移量
        $T = mt_rand($this->height, $this->width * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand($this->width / 2, $this->width * 0.8); // 曲线横坐标结束位置

        list($red, $blue, $green) = $this->fontColor ?: [mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150)];
        $color = imagecolorallocate($this->imgCanvas, $red, $blue, $green);

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->height / 2;  // y = Asin(ωx+φ) + b
                $i = $this->fontSize / 5;
                while ($i > 0) {
                    // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    imagesetpixel($this->imgCanvas, $px + $i, $py + $i, $color);
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A = mt_rand(1, $this->height / 2); // 振幅
        $f = mt_rand(-$this->height / 4, $this->height / 4); // X轴方向偏移量
        $T = mt_rand($this->height, $this->width * 2); // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->height / 2;
        $px1 = $px2;
        $px2 = $this->width;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->height / 2;  // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->imgCanvas, $px + $i, $py + $i, $color);
                    $i--;
                }
            }
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
