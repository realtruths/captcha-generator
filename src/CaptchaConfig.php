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
 * Class CaptchaConfig
 * @package RealTruths\Captcha\Generator
 */
class CaptchaConfig
{
    public $charset = '123467890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; // 字符
    public $length = 4; // 位数
    public $width = 150; // 宽度
    public $height = 40; // 高度
    public $fontSize = null; // 字体大小
    public $bgColor = '#F3FBFE'; // 背景颜色 [243, 251, 254]
    public $fontColor = null; // 字体颜色 [0, 0, 0] 或 #000
    public $codeFont = __DIR__ . '/fonts/Cute-Aurora-2.ttf'; // 字体; $isRandomFont = true 则该字体设置无效
    public $borderColor = '#000'; // 边框颜色
    public $isBorder = true; // 是否画边框
    public $isRandomFont = false; // 是否使用随机字体，开启后优先使用 fonts 配置的字体，如 fonts 未设置则使用包内自带的随机字体
    public $isRandomLightBgColor = true; // 是否使用随机浅色背景，开启后背景颜色 $bgColor 无效
    public $isDrawCurve = true; // 是否画干扰曲线
    public $isDrawNoise = true; // 是否画干扰噪点
    public $isDrawLine = true; // 是否画干扰线
    public $fonts = []; // 其他字体
    public $noiseLevel = 10; // 噪点级别

    /**
     *
     * @param int $noiseLevel
     * @return CaptchaConfig
     */
    public function setNoiseLevel(int $noiseLevel): CaptchaConfig
    {
        $this->noiseLevel = $noiseLevel;
        return $this;
    }

    /**
     * 设置是否使用随机字体
     * @param bool $isRandomFont
     * @return CaptchaConfig
     */
    public function setIsRandomFont(bool $isRandomFont): CaptchaConfig
    {
        $this->isRandomFont = $isRandomFont;
        return $this;
    }

    /**
     * 设置边框颜色
     * @param string|array $borderColor
     * @return CaptchaConfig
     */
    public function setBorderColor($borderColor): CaptchaConfig
    {
        is_string($borderColor) && $this->borderColor = $this->hexToRgb($borderColor);
        is_array($borderColor) && $this->borderColor = $borderColor;
        return $this;
    }

    /**
     * 十六进制转RGB
     * @param string $hexColor
     * @return array
     */
    function hexToRgb(string $hexColor): array
    {
        $color = str_replace('#', '', $hexColor);
        if (strlen($color) > 3) {
            return [
                hexdec(substr($color, 0, 2)),
                hexdec(substr($color, 2, 2)),
                hexdec(substr($color, 4, 2)),
            ];
        }

        $color = $hexColor;
        $red = substr($color, 0, 1) . substr($color, 0, 1);
        $green = substr($color, 1, 1) . substr($color, 1, 1);
        $blue = substr($color, 2, 1) . substr($color, 2, 1);
        // var_dump([hexdec($red), hexdec($green), hexdec($blue)]);
        return [hexdec($red), hexdec($green), hexdec($blue)];
    }

    /**
     * @param bool $isDrawLine
     * @return CaptchaConfig
     */
    public function setIsDrawLine(bool $isDrawLine): CaptchaConfig
    {
        $this->isDrawLine = $isDrawLine;
        return $this;
    }

    /**
     * @param array $fonts
     * @return CaptchaConfig
     */
    public function setFonts(array $fonts): CaptchaConfig
    {
        $this->fonts = $fonts;
        return $this;
    }

    /**
     * 设置字体颜色
     * @param array|string $fontColor
     * @return CaptchaConfig
     */
    public function setFontColor($fontColor): CaptchaConfig
    {
        is_array($fontColor) && $this->fontColor = $fontColor;
        is_string($fontColor) && $this->fontColor = $this->hexToRgb($fontColor);
        return $this;
    }

    /**
     * @param bool $isRandomLightBgColor
     * @return CaptchaConfig
     */
    public function setIsRandomLightBgColor(bool $isRandomLightBgColor): CaptchaConfig
    {
        $this->isRandomLightBgColor = $isRandomLightBgColor;
        return $this;
    }

    /**
     * 设置验证码位数
     * @param int $length
     * @return CaptchaConfig
     */
    public function setLength(int $length): CaptchaConfig
    {
        $this->length = $length;
        return $this;
    }

    /**
     * 设置是否画边框
     * @param bool $isBorder
     * @return CaptchaConfig
     */
    public function setIsBorder(bool $isBorder): CaptchaConfig
    {
        $this->isBorder = $isBorder;
        return $this;
    }

    /**
     * @param string $codeFont
     * @return CaptchaConfig
     */
    public function setCodeFont(string $codeFont): CaptchaConfig
    {
        $this->codeFont = $codeFont;
        return $this;
    }

    /**
     * @param int $fontSize
     * @return CaptchaConfig
     */
    public function setFontSize(int $fontSize): CaptchaConfig
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * @param bool $isDrawCurve
     * @return CaptchaConfig
     */
    public function setIsDrawCurve(bool $isDrawCurve): CaptchaConfig
    {
        $this->isDrawCurve = $isDrawCurve;
        return $this;
    }

    /**
     * 设置背景颜色
     * @param string|array $bgColor
     * @return CaptchaConfig
     */
    public function setBgColor($bgColor): CaptchaConfig
    {
        is_array($bgColor) && $this->bgColor = $bgColor;
        is_string($bgColor) && $this->bgColor = $this->hexToRgb($bgColor);
        return $this;
    }

    /**
     * 设置字符
     * @param string $charset
     * @return CaptchaConfig
     */
    public function setCharset(string $charset): CaptchaConfig
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * 设置是否画噪点
     * @param bool $isDrawNoise
     * @return CaptchaConfig
     */
    public function setIsDrawNoise(bool $isDrawNoise): CaptchaConfig
    {
        $this->isDrawNoise = $isDrawNoise;
        return $this;
    }

    /**
     * 设置宽度
     * @param int $width
     * @return CaptchaConfig
     */
    public function setWidth(int $width): CaptchaConfig
    {
        $this->width = $width;
        return $this;
    }

    /**
     * 设置高度
     * @param int $height
     * @return CaptchaConfig
     */
    public function setHeight(int $height): CaptchaConfig
    {
        $this->height = $height;
        return $this;
    }
}
