# maccotsan\Csv

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
$csvRows = Csv::read("data.csv", $options);
````

#### Options
* 'srcEncoding' default 'sjis-win'      // 読み込むCSVファイルの文字コード
* 'dstEncording' default 'utf-8'        // 読み込み後に扱う文字コード
* 'useHeader' default true              // ヘッダ列をフィールド名として利用するかどうか
* 'fields' default \[\]                 // 任意のフィールド名を利用する場合に指定する
* 'ignoreHeader' default false			// ヘッダ行を無視するかどうか

### UnitTest
````
composer install --dev
composer test
````
