<?php
/**
 * maccotsan/Csv
 *
 * @copyright 2016 maccotsan <maccotsan@gmail.com>
 * @license The MIT License (MIT)
 */

namespace maccotsan\Csv;

use \SplFileObject;

/**
 * Class Csv
 * @package maccotsan\Csv
 */
class Csv
{
	/**
	 * @var array 読み込みオプションのデフォルト値
	 */
	private static $readOptionDefaults = [
		'srcEncoding' => 'sjis-win',	// 読み込むCSVファイルの文字コード
		'dstEncording' => 'utf-8',		// 読み込み後に扱う文字コード
		'useHeader' => false,			// ヘッダ行をフィールド名として利用するかどうか
		'fields' => [],					// 任意のフィールド名を利用する場合に指定する
		'ignoreHeader' => false			// ヘッダ行を無視するかどうか
	];

	/**
	 * CSVをファイルから読み込む。
	 *
	 * @param string $filePath ファイルパス
	 * @param array $options 読み込みオプション
	 * @return array CSV配列
	 */
	public static function read($filePath, $options = [])
	{
		$options = array_merge(Csv::$readOptionDefaults, $options);

		$buf = mb_convert_encoding(file_get_contents($filePath), $options['dstEncording'], $options['srcEncoding']);
		return Csv::readFromString($buf, $options);
	}

	/**
	 * CSVを文字列から読み込む
	 *
	 * @param string $buf CSV文字列
	 * @param array $options 読み込みオプション
	 * @return array CSV配列
	 */
	public static function readFromString($buf, $options = [])
	{
		$options = array_merge(Csv::$readOptionDefaults, $options);

		// 改行コードがCRだと正常に読み込めないので、LFに揃える。
		$buf = Csv::convertEOL($buf);

		// ref: http://php-archive.net/php/csv-tsv-array/
		$temp = tmpfile();
		$meta = stream_get_meta_data($temp);
		fwrite($temp, $buf);
		rewind($temp);

		$file = new SplFileObject($meta['uri']);
		$file->setFlags(SplFileObject::READ_CSV);

		$rows = [];
		foreach ($file as $i => $line) {
			if (1 === count($line) && !$line[0]) { // 空行は無視
				continue;
			}
			$rows[] = $line;
		}

		fclose($temp);
		return Csv::postProcessing($rows, $options);
	}

	/**
	 * 読み込み後の加工を行う
	 *
	 * @param array $rows CSV配列
	 * @param array $options 読み込みオプション
	 * @return array CSV配列
	 */
	private static function postProcessing($rows, $options)
	{
		if ($options['ignoreHeader']) {
			array_shift($rows);
		}

	    if ($options['fields'] !== []) {
			// 任意のフィールド名を指定。
			$rows = Csv::setFieldKeys($rows, $options['fields']);
		} else if ($options['useHeader'] && !$options['ignoreHeader']) {
			// ヘッダ行をフィールド名として使用する。
			$fields = array_shift($rows);
			$rows = Csv::setFieldKeys($rows, $fields);
		}

		return $rows;
	}

	/**
	 * カラムをフィールド名=>値の形式に変換する。
	 *
	 * @param array $rows CSV配列
	 * @param array $fields フィールド名の配列
	 * @return array CSV配列
	 */
	private static function setFieldKeys($rows, $fields) {
		$newRows = [];
		foreach ($rows as $row) {
			$newRow = [];
			foreach ($fields as $i => $field) {
				if (isset($row[$i])) {
					$newRow[$field] = $row[$i];
				} else {
					$newRow[$field] = "";
				}
			}
			$newRows[] = $newRow;
		}
		return $newRows;
	}

	/**
	 * 文字列の改行コードを揃える
	 *
	 * @param string $string 文字列
	 * @param string $to 変換後の改行コード
	 * @return mixed 文字列
	 */
	private static function convertEOL($string, $to = "\n")
	{
    	return preg_replace("/\r\n|\r|\n/", $to, $string);
	}
}
