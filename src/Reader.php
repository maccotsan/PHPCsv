<?php
/**
 * maccotsan/Csv
 *
 * Character-separated values file Reader and Writer.
 * ref: https://ja.wikipedia.org/wiki/Comma-Separated_Values
 *
 * @copyright 2016 maccotsan <maccotsan@gmail.com>
 * @license The MIT License (MIT)
 */

namespace maccotsan\Csv;

use \SplFileObject;
use \maccotsan\StringTool;

/**
 * Class Csv
 * @package maccotsan\Reader
 */
class Reader
{
	/**
	 * @var array 読み込みオプションのデフォルト値
	 */
	private static $readOptionDefaults = [
		'delimiter' => ',', // 区切り文字
		'srcEncoding' => 'sjis-win', // 読み込むCSVファイルの文字コード
		'dstEncording' => 'utf-8', // 読み込み後に扱う文字コード
		'useHeader' => false, // ヘッダ行をフィールド名として利用するかどうか
		'fields' => [], // 任意のフィールド名を利用する場合に指定する
		'ignoreHeader' => false, // ヘッダ行を無視するかどうか
		'ignoreEmptyRow' => false, // すべての列が空の行を無視するかどうか（0を含む）
		// TODO: useHeader=falseの場合でもfilterが利用できるようにする
		'filter' => null // 値を加工するためのフィルタ関数。引数は配列で、'field'と'value'を取ります。useHeaderがtrue、もしくはfieldsを利用する場合に限りコールされることに注意してください。
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
		$options = array_merge(self::$readOptionDefaults, $options);

		$buf = mb_convert_encoding(file_get_contents($filePath), $options['dstEncording'], $options['srcEncoding']);
		return self::readFromString($buf, $options);
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
		$options = array_merge(self::$readOptionDefaults, $options);

		// 改行コードがCRだと正常に読み込めないので、LFに揃える。
		$buf = StringTool\GeneralSupport::convertEOL($buf);

		// ref: http://php-archive.net/php/csv-tsv-array/
		$temp = tmpfile();
		$meta = stream_get_meta_data($temp);
		fwrite($temp, $buf);
		rewind($temp);

		$file = new SplFileObject($meta['uri']);
		$file->setFlags(SplFileObject::READ_CSV);
		$file->setCsvControl($options['delimiter']);

		$rows = [];
		foreach ($file as $i => $line) {
			if (1 === count($line) && !$line[0]) { // 空行は無視
				continue;
			}
			$rows[] = $line;
		}

		fclose($temp);
		return self::postProcessing($rows, $options);
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

		if ($options['ignoreEmptyRow']) {
			$rows = array_filter($rows, function ($row) {
				$a = array_filter($row);
				return !empty($a);
			});
		}

	 	if ($options['fields'] !== []) {
			// 任意のフィールド名を指定。
			$rows = self::setFieldKeys($rows, $options['fields'], $options['filter']);
		} else if ($options['useHeader'] && !$options['ignoreHeader']) {
			// ヘッダ行をフィールド名として使用する。
			$fields = array_shift($rows);
			$rows = self::setFieldKeys($rows, $fields, $options['filter']);
		}

		return $rows;
	}

	/**
	 * カラムをフィールド名=>値の形式に変換する。
	 *
	 * @param array $rows CSV配列
	 * @param array $fields フィールド名の配列
	 * @param function $filter フィルタ関数
	 * @return array CSV配列
	 */
	private static function setFieldKeys($rows, $fields, $filter)
	{
		$newRows = [];
		foreach ($rows as $row) {
			$newRow = [];
			foreach ($fields as $i => $field) {
				if (isset($row[$i])) {
					$value = $row[$i];
				} else {
					$value = "";
				}
				if ($filter !== null) {
					$value = call_user_func_array($filter, [ $field, $value ]);
				}
				$newRow[$field] = $value;
			}
			$newRows[] = $newRow;
		}
		return $newRows;
	}
}
