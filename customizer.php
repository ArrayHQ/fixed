<?php

// ------------- Theme Customizer  ------------- //

add_action( 'customize_register', 'fixed_theme_customizer_register' );

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

class Fixed_Customize_Textarea_Control extends WP_Customize_Control {
    public $type = 'textarea';

    public function render_content() {
        ?>
        <label>
        	<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        	<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
        </label>
        <?php
    }
}

/**
 * Customize for user select, extend the WP customizer
 */

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

class User_Dropdown_Custom_Control extends WP_Customize_Control
{

    private $users = false;

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        $this->users = get_users( $options );

        parent::__construct( $manager, $id, $args );
    }

    /**
     * Render the control's content.
     *
     * Allows the content to be overriden without having to rewrite the wrapper.
     *
     * @return  void
     */
    public function render_content()
    {
        if( empty( $this->users ) )
        {
            return false;
        }
	?>
		<label>
			<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
			<select <?php $this->link(); ?>>
			<!-- blank option as default -->
			<option></option>
			<?php foreach( $this->users as $user ) {
				printf( '<option value="%s" %s>%s</option>',
				$user->data->ID,
				selected( $this->value(), $user->data->ID, false ),
				$user->data->display_name );
				} ?>
			</select>
		</label>
	<?php
    }
} // end class

function fixed_theme_customizer_register( $wp_customize ) {

	//Fixed Style Options
	$wp_customize->add_section( 'fixed_theme_customizer_basic', array(
		'title' 	=> __( 'Theme Options', 'fixed' ),
		'priority' 	=> 1
	) );

	//Logo Image
	$wp_customize->add_setting( 'fixed_theme_customizer_logo', array(
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fixed_theme_customizer_logo', array(
		'label' 	=> __( 'Logo Upload', 'fixed' ),
		'section' 	=> 'fixed_theme_customizer_basic',
		'settings' 	=> 'fixed_theme_customizer_logo'
	) ) );

	//Accent Color
	$wp_customize->add_setting( 'fixed_theme_customizer_accent', array(
		'default' 	=> '#999'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'fixed_theme_customizer_accent', array(
		'label'   	=> __( 'Link Color', 'fixed' ),
		'section' 	=> 'fixed_theme_customizer_basic',
		'settings'  => 'fixed_theme_customizer_accent'
	) ) );

	//Infinite Scroll
	$wp_customize->add_setting( 'fixed_theme_customizer_infinite', array(
        'default'	=> 'enabled',
        'capability'=> 'edit_theme_options',
        'type'		=> 'option',
    ) );

    $wp_customize->add_control( 'infinite_select_box', array(
        'settings'	=> 'fixed_theme_customizer_infinite',
        'label'   	=> __( 'Infinite Scrolling', 'fixed' ),
        'section'	=> 'fixed_theme_customizer_basic',
        'type'		=> 'select',
        'choices'   => array(
            'enabled' 	=> __( 'Enabled', 'fixed' ),
            'disabled' 	=> __( 'Disabled', 'fixed' ),
        ),
    ) );

    //User
	$wp_customize->add_setting( 'fixed_theme_customizer_user', array(
        'default'	=> '',
    ) );

    $wp_customize->add_control( new User_Dropdown_Custom_Control( $wp_customize, 'fixed_theme_customizer_user', array(
	    'label'		=> __( 'Header Featured Author', 'fixed' ),
	    'section'	=> 'fixed_theme_customizer_basic',
	    'settings'	=> 'fixed_theme_customizer_user',
	) ) );

    //Custom CSS
	$wp_customize->add_setting( 'fixed_theme_customizer_css', array(
        'default'	=> '',
    ) );

    $wp_customize->add_control( new Fixed_Customize_Textarea_Control( $wp_customize, 'fixed_theme_customizer_css', array(
	    'label'		=> __( 'Custom CSS', 'fixed' ),
	    'section'	=> 'fixed_theme_customizer_basic',
	    'settings'	=> 'fixed_theme_customizer_css',
	) ) );

}