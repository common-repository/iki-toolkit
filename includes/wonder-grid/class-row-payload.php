<?php

/**
 * Class for creating default row data
 */
class Iki_Row_Payload {

	const LANDSCAPE = 'landscape';
	const PORTRAIT = 'portrait';
	const SQUARE = 'square';

	const C_1 = 1;
	const C_2 = 2;
	const C_3 = 3;
	const C_4 = 4;

	protected static $classic = array(
		'cells'       => '1',
		'orientation' => 'landscape',
		'type'        => 'classic',
		'name'        => null,
		'condensed'   => false,
	);

	protected static $mixed =
		array(
			'mixed-1'         => array(
				'cells'       => 3,
				'orientation' =>
					array(
						0 => 'square',
						1 => 'landscape',
						2 => 'landscape',
					),
				'type'        => 'mixed',
				'name'        => 'mixed-1',
				'condensed'   => false,
			),
			'mixed-1-reverse' => array(
				'cells'       => 3,
				'orientation' =>
					array(
						0 => 'landscape',
						1 => 'landscape',
						2 => 'square',
					),
				'type'        => 'mixed',
				'name'        => 'mixed-1-reverse',
				'condensed'   => false,
			),
			'mixed-2'         => array(
				'cells'       => 5,
				'orientation' =>
					array(
						0 => 'square',
						1 => 'square',
						2 => 'square',
						3 => 'square',
						4 => 'square',
					),
				'type'        => 'mixed',
				'name'        => 'mixed-2',
				'condensed'   => false,
			),
			'mixed-2-reverse' => array(
				'cells'       => 5,
				'orientation' =>
					array(
						0 => 'square',
						1 => 'square',
						2 => 'square',
						3 => 'square',
						4 => 'square',
					),
				'type'        => 'mixed',
				'name'        => 'mixed-2-reverse',
				'condensed'   => false,
			),
			'mixed-3'         => array(
				'cells'       => 5,
				'orientation' =>
					array(
						0 => 'square',
						1 => 'square',
						2 => 'square',
						3 => 'square',
						4 => 'square',
					),
				'type'        => 'mixed',
				'name'        => 'mixed-2-reverse',
				'condensed'   => false,
			)
		);

	public static function get_classic( $orientation, $cells, $condensed = false ) {
		$r                = self::$classic;
		$r['orientation'] = $orientation;
		$r['cells']       = $cells;
		$r['condensed']   = $condensed;

		return $r;
	}

	public static function get_mixed( $name, $condensed = false ) {
		$r = null;
		if ( isset( self::$mixed[ $name ] ) ) {

			$r              = self::$mixed[ $name ];
			$r['condensed'] = $condensed;
		}

		return $r;
	}


}
