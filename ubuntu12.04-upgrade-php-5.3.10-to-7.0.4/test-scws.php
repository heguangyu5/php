<?php

$text = 'SCWS 是 Simple Chinese Word Segmentation 的首字母缩写（即：简易中文分词系统）。

这是一套基于词频词典的机械式中文分词引擎，它能将一整段的中文文本基本正确地切分成词。 词是中文的最小语素单位，但在书写时并不像英语会在词之间用空格分开， 所以如何准确并快速分词一直是中文分词的攻关难点。

SCWS 采用纯 C 语言开发，不依赖任何外部库函数，可直接使用动态链接库嵌入应用程序， 支持的中文编码包括 GBK、UTF-8 等。此外还提供了 PHP 扩展模块， 可在 PHP 中快速而方便地使用分词功能。

分词算法上并无太多创新成分，采用的是自己采集的词频词典，并辅以一定的专有名称，人名，地名， 数字年代等规则识别来达到基本分词，经小范围测试准确率在 90% ~ 95% 之间， 基本上能满足一些小型搜索引擎、关键字提取等场合运用。首次雏形版本发布于 2005 年底。

SCWS 由 hightman 开发， 并以 BSD 许可协议开源发布，源码托管在 github。';

$so = scws_new();
$so->send_text($text);
$words = $so->get_tops(200);
$so->close();

echo "word\ttimes\tweight\tattr\n";
foreach ($words as $word) {
    echo $word['word'], "\t";
    echo $word['times'], "\t";
    echo $word['weight'], "\t";
    echo $word['attr'], "\n";
}
