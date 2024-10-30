<?php

use Elementor\Controls_Manager;
class Elementor_Custom_Content_Widget extends \Elementor\Widget_Base
{

    public function get_name() {
        return 'escce_custom_content';
    }

    public function get_title() {
        return esc_html__('Custom Contents', 'easysoftonic-elementor-widgets');
    }

    public function get_icon() {
        return 'eicon-post-excerpt';
    }

    public function get_categories() {
        return [ 'es-modules' ];
    }

    protected function _register_controls() {
		
		$escce_contents_array = get_posts(array('post_type' => 'content-elementor', 'posts_per_page' => -1));
        $escce_contents = array(esc_html__('Select', 'easysoftonic-elementor-widgets') => 0);
        if ($escce_contents_array && !is_wp_error($escce_contents_array)) {
            foreach ($escce_contents_array as $val) {
                $escce_contents[get_the_title($val)] = $val->ID;
            }
        }

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Custom Content', 'easysoftonic-elementor-widgets'),
            ]
        );

        $this->add_control(
            'custom_contents',
            [
                'label' => __( 'Custom Contents', 'easysoftonic-elementor-widgets' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => array_flip($escce_contents),
            ]
        );

        $this->add_control(
            'custom_content_class',
            [
                'label' => __('Custom Class', 'easysoftonic-elementor-widgets'),
                'type' => \Elementor\Controls_Manager::TEXT
            ]
        );


        $this->end_controls_section();


    }
               
    protected function render() {
            $settings = $this->get_settings_for_display();
			$pb_id = rand(1000, 100000);
			$custom_contents = $settings['custom_contents'];
			$custom_content_class = $settings['custom_content_class'];
            ?>
 
<div class="escce-custom-content-<?php echo esc_attr( $pb_id ); ?> <?php echo esc_attr( $custom_content_class ); ?>">
      <?php echo \Elementor\Plugin::$instance->frontend->get_builder_content( $custom_contents, true ); ?>
                </div>
		<?php }

    protected function _content_template() {

    }

}