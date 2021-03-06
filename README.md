# maccotsan\Csv

[![Build Status](https://travis-ci.org/maccotsan/PHPCsv.svg?branch=master)](https://travis-ci.org/maccotsan/PHPCsv)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/maccotsan/PHPCsv/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/maccotsan/PHPCsv/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/maccotsan/PHPCsv/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/maccotsan/PHPCsv/?branch=master)

## Install
please rewrite your composer.json.
````
・・・
    "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/maccotsan/PHPCsv.git"
            }
        ],
        "require": {
            "maccotsan/csv": "dev-master"
        }
・・・
````

## Usage

### Read
````
use maccotsan\Csv;
$csvRows = Csv\Reader::read("data.csv", $options);
````

#### Options
* 'delimiter' default ',',              // 区切り文字
* 'srcEncoding' default 'sjis-win'      // 読み込むCSVファイルの文字コード
* 'dstEncording' default 'utf-8'        // 読み込み後に扱う文字コード
* 'useHeader' default false             // ヘッダ列をフィールド名として利用するかどうか
* 'fields' default \[\]                 // 任意のフィールド名を利用する場合に指定する
* 'ignoreHeader' default false          // ヘッダ行を無視するかどうか
* 'ignoreEmptyRow' default false        // すべての列が空の行を無視するかどうか（0を含む）
* 'filter' default null                 // 値を加工するためのフィルタ関数。引数は配列で、'field'と'value'を取ります。useHeaderがtrue、もしくはfieldsを利用する場合に限りコールされることに注意してください。

## Document
https://maccotsan.github.io/PHPCsv/

### UnitTest
````
composer install --dev
composer test
````
