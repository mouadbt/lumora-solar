<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class TAHEFOBU_Site_Logo extends Widget_Base {

    public function get_name() {
        return 'tahefobu-site-logo';
    }

    public function get_title() {
        return esc_html__('Site Logo', 'header-footer-builder-for-elementor');
    }

    public function get_icon() {
        return 'eicon-site-logo tahefobu-icon';
    }

    public function get_categories() {
        return ['tahefobu-hf-widgets'];
    }

    public function get_style_depends() {
        return ['tahefobu-site-logo-style'];
    }

    protected function register_controls() {

        // ----------------------------------------------
        // CONTENT SECTION
        // ----------------------------------------------
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Site Logo', 'header-footer-builder-for-elementor'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('logo_type', [
            'label' => esc_html__('Logo Source', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'site_logo' => esc_html__('Use Site Logo (Customizer)', 'header-footer-builder-for-elementor'),
                'custom' => esc_html__('Custom Logo', 'header-footer-builder-for-elementor'),
            ],
            'default' => 'site_logo',
        ]);

        $this->add_control('custom_logo', [
            'label' => esc_html__('Custom Logo', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::MEDIA,
            'condition' => [
                'logo_type' => 'custom'
            ],
        ]);

        $this->add_control('logo_link', [
            'label' => esc_html__('Link to Homepage', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        // ----------------------------------------------
        // STYLE SECTION
        // ----------------------------------------------
        $this->start_controls_section('style_section', [
            'label' => esc_html__('Style', 'header-footer-builder-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);


         $this->add_responsive_control('logo_align', [
            'label' => esc_html__('Alignment', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'header-footer-builder-for-elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'header-footer-builder-for-elementor'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'header-footer-builder-for-elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .tahefobu-site-logo-wrapper' => 'text-align: {{VALUE}};',
            ],
            'default' => 'left',
        ]);

        $this->add_responsive_control('logo_width', [
            'label' => esc_html__('Width', 'header-footer-builder-for-elementor'),
            'type'  => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 1000,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 50,
                    'step' => 0.1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .tahefobu-site-logo img' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('logo_max_width', [
            'label' => esc_html__('Max Width', 'header-footer-builder-for-elementor'),
            'type'  => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 1000,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .tahefobu-site-logo img' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('logo_margin', [
            'label' => esc_html__('Margin', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .tahefobu-site-logo-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('logo_padding', [
            'label' => esc_html__('Padding', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .tahefobu-site-logo-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'logo_border',
            'selector' => '{{WRAPPER}} .tahefobu-site-logo-wrapper',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'logo_shadow',
            'selector' => '{{WRAPPER}} .tahefobu-site-logo-wrapper',
        ]);

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        // Get proper logo URL
        if ($settings['logo_type'] === 'custom' && !empty($settings['custom_logo']['url'])) {
            $logo_url = $settings['custom_logo']['url'];
        } else {
            $logo_id = get_theme_mod('custom_logo');
            $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
        }

        if (!$logo_url) {
            echo '<div class="tahefobu-site-logo-wrapper">Logo Not Set</div>';
            return;
        }

        $logo_html = '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" />';

        ?>
        <div class="tahefobu-site-logo-wrapper">
            <div class="tahefobu-site-logo">
                <?php if ($settings['logo_link'] === 'yes') : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                      <?php echo wp_kses( $logo_html, tahefobu_hf_allowed_html() ); ?>
                    </a>
                <?php else : ?>
                   <?php echo wp_kses( $logo_html, tahefobu_hf_allowed_html() ); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
