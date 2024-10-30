<?php

/**
 * Structure for mixed rows (how the layout should actually look)
 * @return array
 */
function iki_mixed_row_data_structure() {

	return $mixedRowData = array(
		"mixed" => array(
			array(
				"name"        => "mixed-1",
				"orientation" => array(
					"square",
					"landscape",
					"landscape"
				),
				"condensed"   => false,
				"options"     => array(
					"condensed" => true
				)

			),
			array(
				"name"        => "mixed-1-reverse",
				"orientation" => array(
					"landscape",
					"landscape",
					"square"
				),
				"condensed"   => false,
				"options"     => array(
					"condensed" => true
				)

			),
			array(
				"name"        => "mixed-2",
				"orientation" => array(
					"square",
					"square",
					"square",
					"square",
					"square"
				),
				"condensed"   => false,
				"options"     => array(
					"condensed" => true
				)

			),
			array(
				"name"        => "mixed-2-reverse",
				"orientation" => array(
					"square",
					"square",
					"square",
					"square",
					"square"
				),
				"condensed"   => false,
				"options"     => array(
					"condensed" => true
				)

			),
			array(
				"name"        => "mixed-3",
				"orientation" => array(
					"landscape",
					"landscape",
					"square",
					"square",
					"square",
					"square"
				),
				"condensed"   => false,
				"options"     => array(
					"condensed" => true
				)
			)
		)
	);
}