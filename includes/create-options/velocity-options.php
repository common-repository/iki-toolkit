<?php

/**
 * Class for creating velocity.js animation options
 * http://julian.com/research/velocity/#uiPack
 */
class Iki_Toolkit_Velocity_Options {

	private static $class = null;
	protected $velocity_animations_in;
	protected $velocity_animation_out;

	/** Get class instance
	 * @return Iki_Toolkit_Velocity_Options
	 */
	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;

	}


	/**
	 * Iki_Toolkit_Velocity_Options constructor.
	 */
	public function __construct() {

		//http://julian.com/research/velocity/#uiPack
		$this->velocity_animations_in = array(
			'transition.fadeIn'             => __( 'Fade in', 'iki-toolkit' ),
			'transition.flipXIn'            => __( 'Flip X in', 'iki-toolkit' ),
			'transition.flipYIn'            => __( 'Flip Y in', 'iki-toolkit' ),
			'transition.flipBounceXIn'      => __( 'Flip bounce X in', 'iki-toolkit' ),
			'transition.flipBounceYIn'      => __( 'Flip bounce Y in', 'iki-toolkit' ),
			'transition.swoopIn'            => __( 'Swoop in', 'iki-toolkit' ),
			'transition.whirlIn'            => __( 'Whirl in', 'iki-toolkit' ),
			'transition.shrinkIn'           => __( 'Shrink in', 'iki-toolkit' ),
			'transition.expandIn'           => __( 'Expand in', 'iki-toolkit' ),
			'transition.bounceIn'           => __( 'Bounce in', 'iki-toolkit' ),
			'transition.bounceUpIn'         => __( 'Bounce up in', 'iki-toolkit' ),
			'transition.bounceDownIn'       => __( 'Bounce down in', 'iki-toolkit' ),
			'transition.bounceLeftIn'       => __( 'Bounce left in', 'iki-toolkit' ),
			'transition.bounceRightIn'      => __( 'Bounce right in', 'iki-toolkit' ),
			'transition.slideUpIn'          => __( 'Slide up in', 'iki-toolkit' ),
			'transition.slideDownIn'        => __( 'Slide down in', 'iki-toolkit' ),
			'transition.slideLeftIn'        => __( 'Slide left in', 'iki-toolkit' ),
			'transition.slideRightIn'       => __( 'Slide right in', 'iki-toolkit' ),
			'transition.slideUpBigIn'       => __( 'Slide up big in', 'iki-toolkit' ),
			'transition.slideDownBigIn'     => __( 'Slide down big in', 'iki-toolkit' ),
			'transition.slideLeftBigIn'     => __( 'Slide left big in', 'iki-toolkit' ),
			'transition.slideRightBigIn'    => __( 'Slide right big in', 'iki-toolkit' ),
			'transition.perspectiveUpIn'    => __( 'Perspective up in', 'iki-toolkit' ),
			'transition.perspectiveDownIn'  => __( 'Perspective down in', 'iki-toolkit' ),
			'transition.perspectiveLeftIn'  => __( 'Perspective left in', 'iki-toolkit' ),
			'transition.perspectiveRightIn' => __( 'Perspective right in', 'iki-toolkit' )
		);

		$this->velocity_animation_out = array(
			"transition.fadeOut"             => __( 'Fade out', 'iki-toolkit' ),
			"transition.flipXOut"            => __( 'Flip X out', 'iki-toolkit' ),
			"transition.flipYOut"            => __( 'Flip Y out', 'iki-toolkit' ),
			"transition.flipBounceXOut"      => __( 'Flip bounce X out', 'iki-toolkit' ),
			"transition.flipBounceYOut"      => __( 'Flip bounce Y out', 'iki-toolkit' ),
			"transition.swoopOut"            => __( 'Swoop out', 'iki-toolkit' ),
			"transition.whirlOut"            => __( 'Whirl out', 'iki-toolkit' ),
			"transition.shrinkOut"           => __( 'Shrink out', 'iki-toolkit' ),
			"transition.expandOut"           => __( 'Expand out', 'iki-toolkit' ),
			"transition.bounceOut"           => __( 'Bounce out', 'iki-toolkit' ),
			"transition.bounceUpOut"         => __( 'Bounce up out', 'iki-toolkit' ),
			"transition.bounceDownOut"       => __( 'Bounce down out', 'iki-toolkit' ),
			"transition.bounceLeftOut"       => __( 'Bounce left out', 'iki-toolkit' ),
			"transition.bounceRightOut"      => __( 'Bounce right out', 'iki-toolkit' ),
			"transition.slideUpOut"          => __( 'Slide up out', 'iki-toolkit' ),
			"transition.slideDownOut"        => __( 'Slide down out', 'iki-toolkit' ),
			"transition.slideLeftOut"        => __( 'Slide left out', 'iki-toolkit' ),
			"transition.slideRightOut"       => __( 'Slide right out', 'iki-toolkit' ),
			"transition.slideUpBigOut"       => __( 'Slide up big out', 'iki-toolkit' ),
			"transition.slideDownBigOut"     => __( 'Slide down big out', 'iki-toolkit' ),
			"transition.slideLeftBigOut"     => __( 'Slide left big out', 'iki-toolkit' ),
			"transition.slideRightBigOut"    => __( 'Slide right bio out', 'iki-toolkit' ),
			"transition.perspectiveUpOut"    => __( 'Perspective up out', 'iki-toolkit' ),
			"transition.perspectiveDownOut"  => __( 'Perspective down out', 'iki-toolkit' ),
			"transition.perspectiveLeftOut"  => __( 'Perspective left out', 'iki-toolkit' ),
			"transition.perspectiveRightOut" => __( 'Perspective right out', 'iki-toolkit' ),
		);

	}

	/** Get velocity animations IN
	 *
	 * @param bool $no_animation_option option not to use the animation
	 *
	 * @return array animations
	 */
	public function get_velocity_animations_in( $no_animation_option = true ) {

		$r = $this->velocity_animations_in;

		if ( $no_animation_option ) {
			$r = array_merge( array(
				'none' => '--' . __( 'No animation', 'iki-toolkit' ),
			), $r );
		}

		return $r;
	}


	/** Get velocity animations OUT
	 *
	 * @param bool $no_animation_option option not to use the animation
	 *
	 * @return array animation out
	 */
	public function get_velocity_animations_out( $no_animation_option = true ) {

		$r = $this->velocity_animation_out;

		if ( $no_animation_option ) {
			$r = array_merge( array(
				'none' => '--' . __( 'No animation', 'iki-toolkit' ),
			), $r );
		}

		return $r;
	}

	/** Creation animation IN options
	 *
	 * @param null $additional_data
	 * @param bool $enable_no_animation
	 * @param null $additional_animations
	 *
	 * @return array
	 */
	public function get_animation_in_option( $additional_data = null, $enable_no_animation = true, $additional_animations = null ) {

		$animations = self::get_velocity_animations_in( $enable_no_animation );

		if ( ! $enable_no_animation ) {
			unset( $animations['none'] );
		}

		if ( $additional_animations ) {
			$animations = array_merge( $animations, $additional_animations );
		}

		$r = array(
			'label'   => __( 'Animation IN', 'iki-toolkit' ),
			'type'    => 'select',
			'value'   => 'transition.slideUpIn',
			'choices' => $animations,
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;
	}
}
