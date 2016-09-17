# maccotsan\Csv

[![Build Status](https://travis-ci.org/maccotsan/PHPCsv.svg?branch=master)](https://travis-ci.org/maccotsan/PHPCsv)

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
* 'srcEncoding' default 'sjis-win'      // 読み込むCSVファイルの文字コード
* 'dstEncording' default 'utf-8'        // 読み込み後に扱う文字コード
* 'useHeader' default false              // ヘッダ列をフィールド名として利用するかどうか
* 'fields' default \[\]                 // 任意のフィールド名を利用する場合に指定する
* 'ignoreHeader' default false			// ヘッダ行を無視するかどうか
* 'ignoreEmptyRow' default false        // すべての列が空の行を無視するかどうか（0を含む）

### UnitTest
````
composer install --dev
composer test
````
