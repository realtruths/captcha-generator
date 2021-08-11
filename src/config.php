<?php
/*
 * This file is part of the realtruths/captcha-generator.
 *
 * (c) RealTruths <realtruths@126.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
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

