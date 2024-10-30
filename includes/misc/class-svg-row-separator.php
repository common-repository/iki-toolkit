<?php

//handles getting svg markup for front end
class Iki_Svg_Separator {
	protected function get_svg_data( $separator_name ) {
		return Iki_Svg_Separator_Data::get_row( $separator_name );
	}

	public function get_separator( $separator_name, $clases = array() ) {

		$el   = '';
		$data = Iki_Svg_Separator_Data::get_row( $separator_name );
		if ( $data ) {
			$clases = Iki_Toolkit_Utils::sanitize_html_class_array( $clases );
			$el     = sprintf( $data['payload'], $clases );
		}

		return $el;
	}
}

/**
 * Class that holds and prints html code (svg) for row separators
 */
class Iki_Svg_Separator_Data {
	protected static $row_data = array(
		//ARROW UP
		'arrow-up'       => array(
			'payload' => '<svg preserveAspectRatio="xMidYMid" class="iki-svg-separator %1$s" viewBox="-274 123 1188 80">
	<defs>
		<path d="M-278.94 203.67l599.97-80 600.03 80h-1200z" id="iki-p-arrow-up-s"/>
	</defs>
	<use xlink:href="#iki-p-arrow-up-s"/>
</svg>'
		),
		'arrow-up-left'  => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="0 0 1190 80" ><path d="M259.2 0L1200 80H0L259.2 0z" bx:shape="triangle 0 0 1200 80 0.216 0 1@1c7aed2d" /></svg>'
		),
		'arrow-up-right' => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="0 0 1190 80"><path d="M801.6 0L1200 80H0L801.6 0z" bx:shape="triangle 0 0 1200 80 0.668 0 1@a40b6976" /></svg>'
		),
		//TILT LEFT L
		'tilt-left-l'    => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="-21 198 1120 244">
	<defs>
		<path d="M1171.17 202.81l-1200 240h1200v-240z" id="iki-p-tilt-left-l"/>
	</defs>
	<use xlink:href="#iki-p-tilt-left-l"/>
</svg>'
		),
		//TILT LEFT M
		'tilt-left-m'    => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="-26 202 1197 160">
	<defs>
		<path d="M1171.17 202.81l-1200 160h1200v-160z" id="iki-p-tilt-left-m"/>
	</defs>
	<use xlink:href="#iki-p-tilt-left-m"/>
</svg>'
		),
		//TILT LEFT S
		'tilt-left-s'    => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="-313 248 1195 80">
	<defs>
		<path d="M884.07 248.52l-1200 80h1200v-80z" id="iki-p-tilt-left-s"/>
	</defs>
	<use xlink:href="#iki-p-tilt-left-s"/>
</svg>'
		),
		//TILT RIGHT L
		'tilt-right-l'   => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="-28 202 1190 239">
	<defs>
		<path d="M-28 202.81l1200 240h-1200v-240z" id="iki-p-tilt-right-l"/>
	</defs>
	<use xlink:href="#iki-p-tilt-right-l"/>
</svg>'
		),
		//TILT RIGHT M
		'tilt-right-m'   => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="-28 202 1185 160">
	<defs>
		<path d="M-28.83 202.81l1200 160h-1200v-160z" id="iki-p-tilt-right-m"/>
	</defs>
	<use xlink:href="#iki-p-tilt-right-m"/>
</svg>'
		),
		//TILT RIGHT S
		'tilt-right-s'   => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="56 187 1200 80">
	<defs>
		<path d="M55.19 187.04l1200 80h-1200v-80z" id="iki-p-tilt-right-s"/>
	</defs>
	<use xlink:href="#iki-p-tilt-right-s"/>
</svg>'

		),
		'circle-up'      => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="0 0 1200 149" >
			<defs>
				<path id="iki-p-circle-up" d="M1201 25.817V150H0V25.817c38.33 63.865 292.347 113.21 600 113.21s561.67-49.345 600-113.21z"/>
			</defs>
			<use xlink:href="#iki-p-circle-up"/>
			</svg>'
		),
		'wave'           => array(
			'payload' => '<svg class="iki-svg-separator %1$s" viewBox="0 0 1199 100">
				<defs>
				<path id="iki-p-wave" d="M1200 25.817c-151.73 128.61-287.595 12.532-606.51 12.8-318.913.268-376.21 113.396-593.49-12.8V150h1200V25.817z" />
				</defs>
			<use xlink:href="#iki-p-wave"/>
			</svg>'
		)

	);


	/** Get row by name
	 *
	 * @param $row_name string Row name
	 *
	 * @return bool|mixed
	 */
	public static function get_row( $row_name ) {
		$row = self::$row_data;

		if ( isset( $row[ $row_name ] ) ) {

			return $row[ $row_name ];

		} else {
			return false;
		}
	}

}
