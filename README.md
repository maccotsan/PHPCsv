# maccotsan\Csv

## Install
please rewrite your composer.json.
````
・・・
    "repositories": [
     {
      "type":"package",
        "package": {
          "name": "maccotsan/csv",
          "version":"master",
          "source": {       
            "url": "https://github.com/maccotsan/PHPCsv.git",
            "type": "git",
            "reference":"master"
          }
        }
      }
    ],
    "require": {
        "maccotsan/csv: "*"
    },
・・・
````

## Usage

### Read
````
$csvRows = Csv::read("data.csv", $options);
````

#### Options
* 'srcEncoding' default 'sjis-win' // 読み込むCSVファイルの文字コード
* 'dstEncording' default 'utf-8' // 読み込み後に扱う文字コード
* 'useHeader' default true // ヘッダ列をフィールド名として利用するかどうか
* 'fields' default \[\] // 任意のフィールド名を利用する場合に指定する

### UnitTest
````
composer install --dev
composer test
````
