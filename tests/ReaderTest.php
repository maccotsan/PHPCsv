<?php
/**
 * maccotsan/Csv
 *
 * @copyright 2016 maccotsan <maccotsan@gmail.com>
 * @license The MIT License (MIT)
 */

namespace maccotsan\Csv;

/**
 * Class ReaderTest
 * @package maccotsan\Csv
 */
class ReaderTest extends \PHPUnit\Framework\TestCase
{
	private $dataPath = "tests/data/TestData.csv"; // UTF-8, CR
	private $dataNoFieldsPath = "tests/data/TestDataNoFields.csv"; // UTF-8, CR
	private $dataEmptyRowPath = "tests/data/TestDataEmptyRow.csv"; // UTF-8, CR
	private $dataIrregularPath = "tests/data/TestDataIrregular.csv"; // UTF-8, CR
	private $dataFromExcelPath = "tests/data/TestDataFromExcel.csv"; // SJIS, CR+LF
	private $dataFromExcelTabTextPath = "tests/data/TestDataFromExcelTabText.txt"; // SJIS, CR

	private $expectedPlain = [
		[
			"col_string_en",
			"col_string_jp",
			"col_number",
			"col_number_conma",
			"note"
		],
		[
			"csv read test data.",
			"CSVテストデータ。",
			"12345678",
			"12,345,678 ",
			"カンマ区切りの数値は\"\"で括られる。"
		],
		[
			"minus data.",
			"マイナスデータ。",
			"-12345678",
			"-12345678",
			""
		],
		[
			"number function.",
			"数値書式。",
			"12345678 ",
			"12,345,678 ",
			"数値書式の場合、CSV出力すると後ろに半角スペースが自動挿入される"
		],
		[
			"user function.",
			"ユーザ定義。",
			"12345678",
			"12,345,678",
			"半角スペースを取る場合の書式設定"
		],
		[
			"new line.",
			"改行。",
			"1234\n5678",
			"12,345\n678",
			""
		]
	];

	private $expectedFields = [
		[
			"col_string_en" => "csv read test data.",
			"col_string_jp" => "CSVテストデータ。",
			"col_number" => "12345678",
			"col_number_conma" => "12,345,678 ",
			"note" => "カンマ区切りの数値は\"\"で括られる。"
		],
		[
			"col_string_en" => "minus data.",
			"col_string_jp" => "マイナスデータ。",
			"col_number" => "-12345678",
			"col_number_conma" => "-12345678",
			"note" => ""
		],
		[
			"col_string_en" => "number function.",
			"col_string_jp" => "数値書式。",
			"col_number" => "12345678 ",
			"col_number_conma" => "12,345,678 ",
			"note" => "数値書式の場合、CSV出力すると後ろに半角スペースが自動挿入される"
		],
		[
			"col_string_en" => "user function.",
			"col_string_jp" => "ユーザ定義。",
			"col_number" => "12345678",
			"col_number_conma" => "12,345,678",
			"note" => "半角スペースを取る場合の書式設定"
		],
		[
			"col_string_en" => "new line.",
			"col_string_jp" => "改行。",
			"col_number" => "1234\n5678",
			"col_number_conma" => "12,345\n678",
			"note" => ""
		]
	];

	private $expectedFieldsWithoutLF = [
		[
			"col_string_en" => "csv read test data.",
			"col_string_jp" => "CSVテストデータ。",
			"col_number" => "12345678",
			"col_number_conma" => "12,345,678 ",
			"note" => "カンマ区切りの数値は\"\"で括られる。"
		],
		[
			"col_string_en" => "minus data.",
			"col_string_jp" => "マイナスデータ。",
			"col_number" => "-12345678",
			"col_number_conma" => "-12345678",
			"note" => ""
		],
		[
			"col_string_en" => "number function.",
			"col_string_jp" => "数値書式。",
			"col_number" => "12345678 ",
			"col_number_conma" => "12,345,678 ",
			"note" => "数値書式の場合、CSV出力すると後ろに半角スペースが自動挿入される"
		],
		[
			"col_string_en" => "user function.",
			"col_string_jp" => "ユーザ定義。",
			"col_number" => "12345678",
			"col_number_conma" => "12,345,678",
			"note" => "半角スペースを取る場合の書式設定"
		],
		[
			"col_string_en" => "new line.",
			"col_string_jp" => "改行。",
			"col_number" => "1234 5678",
			"col_number_conma" => "12,345 678",
			"note" => ""
		]
	];

	private $expectedEmptyRow = [
		[
			"a" => "",
			"b" => "2",
			"c" => "",
			"d" => "0"
		],
		[
			"a" => "",
			"b" => "3",
			"c" => "",
			"d" => "0"
		],
		[
			"a" => "",
			"b" => "5",
			"c" => "",
			"d" => "0"
		]
	];

	private $expectedIrregularPlain = [
		[
			"col_string_en",
			"col_string_jp",
			"col_number",
			"col_number_conma",
			"note"
		],
		[
			"",
			"先頭空白"
		],
		[
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			""
		],
		[
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"不正なカラム数"
		],
		[
			"",
			"",
			"",
			"",
			"9",
			"",
			"",
			""
		],
		[
			"イリーガル"
		]
	];

	private $expectedIrregularFields = [
		[
			"col_string_en" => "",
			"col_string_jp" => "先頭空白",
			"col_number" => "",
			"col_number_conma" => "",
			"note" => ""
		],
		[
			"col_string_en" => "",
			"col_string_jp" => "",
			"col_number" => "",
			"col_number_conma" => "",
			"note" => ""
		],
		[
			"col_string_en" => "",
			"col_string_jp" => "",
			"col_number" => "",
			"col_number_conma" => "",
			"note" => ""
		],
		[
			"col_string_en" => "",
			"col_string_jp" => "",
			"col_number" => "",
			"col_number_conma" => "",
			"note" => "9"
		],
		[
			"col_string_en" => "イリーガル",
			"col_string_jp" => "",
			"col_number" => "",
			"col_number_conma" => "",
			"note" => ""
		]
	];

	private $testFields = [
		"col_string_en",
		"col_string_jp",
		"col_number",
		"col_number_conma",
		"note"
	];

	/**
	 * 通常のCSV読み込み
	 */
	public function testRead()
	{
		// 内容一致
		$csvRows = Reader::read($this->dataPath, ['srcEncoding' => 'utf-8']);
		$this->assertEquals($this->expectedPlain, $csvRows);

		// 内容一致（ヘッダをフィールド名にする）
		$csvRows = Reader::read($this->dataPath, ['srcEncoding' => 'utf-8', 'useHeader' => true]);
		$this->assertEquals($this->expectedFields, $csvRows);

		// 内容一致（ヘッダを無視してフィールド名を指定する）
		$csvRows = Reader::read($this->dataPath, [
			'srcEncoding' => 'utf-8', 'ignoreHeader' => true, 'fields' => $this->testFields
		]);
		$this->assertEquals($this->expectedFields, $csvRows);
	}

	/**
	 * フィールド列が無いCSVの読み込み
	 */
	public function testNoFieldsRead()
	{
		// 内容一致（任意のフィールド名を指定する）
		$csvRows = Reader::read($this->dataNoFieldsPath, ['srcEncoding' => 'utf-8', 'fields' => $this->testFields]);
		$this->assertEquals($this->expectedFields, $csvRows);
	}

	/**
	 * フィールド列が無いCSVの読み込み
	 */
	public function testEmptyRowRead()
	{
		// 内容一致（任意のフィールド名を指定する）
		$csvRows = Reader::read($this->dataEmptyRowPath, [
			'srcEncoding' => 'utf-8', 'useHeader' => true, 'ignoreEmptyRow' => true
		]);
		$this->assertEquals($this->expectedEmptyRow, $csvRows);
	}

	/**
	 * 不規則なフォーマットのCSVの読み込み
	 */
	public function testIrregularRead()
	{
		// 内容一致
		// 空白行スキップ
		// 先頭カラム空白でも読み込み
		$csvRows = Reader::read($this->dataIrregularPath, ['srcEncoding' => 'utf-8']);
		$this->assertEquals($this->expectedIrregularPlain, $csvRows);

		// 内容一致（ヘッダをフィールド名にする）
		// 空白行スキップ
		// 先頭カラム空白でも読み込み
		// フィールドが指定された場合は、カラム数が足りない・多い場合でもフィールド数に合わさる
		$csvRows = Reader::read($this->dataIrregularPath, ['srcEncoding' => 'utf-8', 'useHeader' => true]);
		$this->assertEquals($this->expectedIrregularFields, $csvRows);
	}

	/**
	 * excelから生成したCSVの読み込み
	 */
	public function testReadFromExcel()
	{
		// 内容一致
		$csvRows = Reader::read($this->dataFromExcelPath);
		$this->assertEquals($this->expectedPlain, $csvRows);

		// 内容一致（ヘッダをフィールド名にする）
		$csvRows = Reader::read($this->dataFromExcelPath, ['useHeader' => true]);
		$this->assertEquals($this->expectedFields, $csvRows);

		// 内容一致（ヘッダを無視してフィールド名を指定する）
		$csvRows = Reader::read($this->dataFromExcelPath, ['ignoreHeader' => true, 'fields' => $this->testFields]);
		$this->assertEquals($this->expectedFields, $csvRows);
	}

	/**
	 * excelから生成したタブ区切りテキスト読み込み
	 */
	public function testReadFromExcelTabText()
	{
		// 内容一致
		$csvRows = Reader::read($this->dataFromExcelTabTextPath, ['delimiter' => "\t"]);
		$this->assertEquals($this->expectedPlain, $csvRows);

		// 内容一致（ヘッダをフィールド名にする）
		$csvRows = Reader::read($this->dataFromExcelTabTextPath, ['delimiter' => "\t", 'useHeader' => true]);
		$this->assertEquals($this->expectedFields, $csvRows);

		// 内容一致（ヘッダを無視してフィールド名を指定する）
		$csvRows = Reader::read($this->dataFromExcelTabTextPath, ['delimiter' => "\t", 'ignoreHeader' => true, 'fields' => $this->testFields]);
		$this->assertEquals($this->expectedFields, $csvRows);
	}

	/**
	 * 文字列からCSV読み込み
	 */
	public function testReadFromString()
	{
		// 内容一致
		$buf = file_get_contents($this->dataPath);
		$csvRows = Reader::readFromString($buf);
		$this->assertEquals($this->expectedPlain, $csvRows);
	}

	/**
	 * フィルタ適用テスト
	 */
	public function testReadFilter()
	{
		// 内容一致
		$csvRows = Reader::read($this->dataPath, [
			'srcEncoding' => 'utf-8',
			'useHeader' => true,
			'filter' => function($field, $value) {
				return str_replace("\n", " ", $value);
			}
		]);
		$this->assertEquals($this->expectedFieldsWithoutLF, $csvRows);
	}
}
