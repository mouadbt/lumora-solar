<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Plugin;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class TAHEFOBU_Copy_Right extends Widget_Base {

    public function get_name() {
        return 'tahefobu-copy-right';
    }

    public function get_title() {
        return esc_html__('Copy Right', 'header-footer-builder-for-elementor');
    }

    public function get_icon() {
        return 'eicon-footer tahefobu-icon';
    }

    public function get_categories() {
        return ['tahefobu-hf-widgets'];
    }

    public function get_style_depends() {
        return ['tahefobu-copy-right-style'];
    }

    protected function register_controls() {

        // Content Section
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'header-footer-builder-for-elementor'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('company_name', [
            'label' => esc_html__('Company Name', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Your Company', 'header-footer-builder-for-elementor'),
            'dynamic' => [
            'active' => true, // Enable dynamic tags
                ],
            'label_block' => true,
        ]);

        $this->add_control('rights_text', [
            'label' => esc_html__('Rights Text', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('All Rights Reserved.', 'header-footer-builder-for-elementor'),
            'label_block' => true,
        ]);

        $this->add_control('text_align', [
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
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .tahefobu-footer-copy-right' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section('style_section', [
            'label' => esc_html__('Style', 'header-footer-builder-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('text_color', [
            'label' => esc_html__('Text Color', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default'=>'#1a1a1aff',
            'selectors' => [
                '{{WRAPPER}} .tahefobu-footer-copy-right' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'text_typography',
            'label' => esc_html__('Typography', 'header-footer-builder-for-elementor'),
            'selector' => '{{WRAPPER}} .tahefobu-footer-copy-right',
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            'name' => 'background',
            'label' => esc_html__('Background', 'header-footer-builder-for-elementor'),
            'types' => ['classic', 'gradient'],
            'selector' => '{{WRAPPER}} .tahefobu-footer-copy-right',
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'border',
            'label' => esc_html__('Border', 'header-footer-builder-for-elementor'),
            'selector' => '{{WRAPPER}} .tahefobu-footer-copy-right',
        ]);

        $this->add_responsive_control('padding', [
            'label' => esc_html__('Padding', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'default'=>[
                'left' => 10,
                'right' => 10,
                'top' => 10,
                'bottom' => 10,
            ],
            'selectors' => [
                '{{WRAPPER}} .tahefobu-footer-copy-right' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('margin', [
            'label' => esc_html__('Margin', 'header-footer-builder-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .tahefobu-footer-copy-right' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }
   
    protected function render() {
        $settings = $this->get_settings_for_display();
    
        $company_name = !empty($settings['company_name']) ? $settings['company_name'] : esc_html__('Your Company', 'header-footer-builder-for-elementor');
        $rights_text = !empty($settings['rights_text']) ? $settings['rights_text'] : esc_html__('All Rights Reserved.', 'header-footer-builder-for-elementor');
        ?>
        <div class="tahefobu-footer-copy-right">
            &copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html($company_name); ?>. <?php echo esc_html($rights_text); ?>
        </div>
        <?php
    }
}
