<?php
/*
 * This file is part of the realtruths/captcha-generator.
 *
 * (c) RealTruths <realtruths@126.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace RealTruths\Captcha\Generator;

/**
 * Class CaptchaGenerator
 * @package RealTruths\Captcha\Generator
 */
class CaptchaGenerator
{
    /**
     * 配置资源
     * @var CaptchaConfig
     */
    private $config;
    /**
     * 验证码画布
     * @var resource
     */
    private $imgCanvas;
    /**
     * 验证码
     * @var string
     */
    private $code;
    /**
     * 验证码字节
     * @var string
     */
    private $byte;

    /**
     * CaptchaGenerator constructor.
     * @param array|CaptchaConfig $option 配置对象或配置数组
     */
    public function __construct($option = [])
    {
        $this->init($option);
        !$this->config->fontSize && $this->config->fontSize = intval($this->config->width / ($this->config->length * 1.5));
        // 加载随机字体
        if ($this->config->isRandomFont) {
            $fonts = $this->loadFonts(__DIR__ . '/fonts/');
            $this->config->fonts && $fonts = $this->config->fonts;
            $this->config->codeFont = $fonts[array_rand($fonts)];
        }
    }

    /**
     * 加载配置
     * @param $option
     */
    public function init($option)
    {
        if (is_array($option)) {
            $this->config = new CaptchaConfig();
            if (!empty($option)) {
                foreach ($option as $key => $value) {
                    $method = 'set' . ucfirst($key);
                    if (method_exists($this->config, $method)) {
                        $this->config->$method($value);
                    }
                }
            }
        } elseif ($option instanceof CaptchaConfig) {
            $this->config = $option;
        } else {
            $this->config = new CaptchaConfig();
        }
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
     * 生成验证码
     * @param string $code 验证码
     * @return $this
     */
    public function generate($code = null): self
    {
        if (!is_null($code)) {
            $this->config->length = strlen($code);
        } else {
            $code = substr(str_shuffle($this->config->charset), 0, $this->config->length);
        }

        $code = strval($code);

        // 创建空白画布
        $this->imgCanvas = imagecreate($this->config->width, $this->config->height);

        if (is_string($this->config->bgColor)) $this->config->bgColor = $this->config->hexToRgb($this->config->bgColor);

        [$red, $blue, $green] = $this->config->isRandomLightBgColor ? $this->getRandomLightBgColor() : ($this->config->bgColor ?: $this->getRandomLightBgColor());
        // 设置背景颜色
        $this->config->bgColor = imagecolorallocate($this->imgCanvas, $red, $blue, $green);
        imagefill($this->imgCanvas, 0, 0, $this->config->bgColor);
        // 设置边框
        $rect = is_string($this->config->borderColor) ? $this->config->hexToRgb($this->config->borderColor) : $this->config->borderColor;
        $this->config->isBorder && imagerectangle($this->imgCanvas, 0, 0, $this->config->width - 1, $this->config->height - 1, $rect);
        // 画干扰噪点
        $this->config->isDrawNoise && $this->drawNoise();
        // 画干扰曲线
        $this->config->isDrawCurve && $this->drawCurve();

        // 画验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        for ($i = 0; $i < $this->config->length; $i++) {
            $codeNX += mt_rand($this->config->fontSize * 1, $this->config->fontSize * 1.3);
            [$red, $green, $blue] = is_string($this->config->fontColor) ? $this->config->hexToRgb($this->config->fontColor) : $this->config->fontColor;
            $color = imagecolorallocate($this->imgCanvas, $red, $green, $blue);
            !$color && $color = mt_rand(50, 200);
            imagettftext($this->imgCanvas, $this->config->fontSize, mt_rand(-40, 40), $codeNX, $this->config->fontSize * 1.2, $color, $this->config->codeFont, $code[$i]);
        }
        $this->code = strtolower($code);
        // 输出字节
        ob_start();
        imagepng($this->imgCanvas);
        $this->byte = ob_get_contents();
        ob_end_clean();
        imagedestroy($this->imgCanvas);
        return $this;
    }

    /**
     * 随机浅色背景
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
     * 画干扰噪点
     */
    public function drawNoise(): void
    {
        $codeSet = '1234567890abcdefghijklmnopqrstuvwxyz';
        for ($i = 0; $i < $this->config->noiseLevel ?: 10; $i++) {
            [$red, $blue, $green] = $this->getRandomLightBgColor();
            $noiseColor = imagecolorallocate($this->imgCanvas, $red, $blue, $green);
            for ($j = 0; $j < 5; $j++) {
                // 画杂点
                imagestring($this->imgCanvas, 2, mt_rand(-10, $this->config->width), mt_rand(-10, $this->config->height), $codeSet[mt_rand(0, strlen($codeSet) - 1)], $noiseColor);
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
        $A = mt_rand(1, $this->config->height / 2);// 振幅
        $b = mt_rand(-$this->config->height / 4, $this->config->height / 4); // Y轴方向偏移量
        $f = mt_rand(-$this->config->height / 4, $this->config->height / 4); // X轴方向偏移量
        $T = mt_rand($this->config->height, $this->config->width * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand($this->config->width / 2, $this->config->width * 0.8); // 曲线横坐标结束位置

        [$red, $blue, $green] = is_string($this->config->fontColor) ? $this->config->hexToRgb($this->config->fontColor) : $this->config->fontColor;
        !$red && [$red, $blue, $green] = [mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150)];
        $color = imagecolorallocate($this->imgCanvas, $red, $blue, $green);

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                // y = Asin(ωx+φ) + b
                $py = $A * sin($w * $px + $f) + $b + $this->config->height / 2;
                $i = $this->config->fontSize / 20;
                while ($i > 0) {
                    // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    imagesetpixel($this->imgCanvas, $px + $i, $py + $i, $color);
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A = mt_rand(1, $this->config->height / 2); // 振幅
        $f = mt_rand(-$this->config->height / 4, $this->config->height / 4); // X轴方向偏移量
        $T = mt_rand($this->config->height, $this->config->width * 2); // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->config->height / 2;
        $px1 = $px2;
        $px2 = $this->config->width;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                // y = Asin(ωx+φ) + b
                $py = $A * sin($w * $px + $f) + $b + $this->config->height / 2;
                $i = $this->config->fontSize / 20;
                while ($i > 0) {
                    // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    imagesetpixel($this->imgCanvas, $px + $i, $py + $i, $color);
                    $i--;
                }
            }
        }
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
}
