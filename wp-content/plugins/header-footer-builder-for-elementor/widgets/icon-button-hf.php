<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Box_Shadow; 
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TAHEFOBU_Icon_Button extends Widget_Base {

    public function get_name() {
        return 'tahefobu_icon_button_widget';
    }

    public function get_title() {
        return esc_html__( 'H&F Button', 'header-footer-builder-for-elementor' );
    }

    public function get_icon() {
        return 'eicon-button tahefobu-icon tahefobu-icon';
    }

     public function get_categories(): array {
        return [ 'tahefobu-hf-widgets' ];
    }

    public function get_style_depends() {
        return ['tahefobu-icon-button-style'];
    }

    public function get_keywords(): array {
		return [ 'button', 'click', 'icon button' ];
	}

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'header-footer-builder-for-elementor' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Button Text
        $this->add_responsive_control(
            'button_text',
            [
                'label' => esc_html__( 'Button Text', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Contact Us', 'header-footer-builder-for-elementor' ),
                'sanitize_callback' => 'sanitize_text_field',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // Icon
        $this->add_responsive_control(
            'button_icon',
            [
                'label' => esc_html__( 'Icon', 'header-footer-builder-for-elementor' ),
                'type'  => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-arrow-right', // Full class name for Font Awesome 5 Solid
                    'library' => 'fa-solid',
                ],
            ]
        );


        // Button Link
        $this->add_responsive_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'header-footer-builder-for-elementor' ),
                'default' => [
                    'url' => '#',
                    'is_external' => false,
                    'nofollow' => false,
                ],
                'sanitize_callback' => 'esc_url_raw', // Ensure URLs are sanitized properly
            ]
        );

        $this->end_controls_section();

        // ============================== Button Style Section ================================
        //=====================================================================================
         // -----------------------------------------button animation effect
        $this->start_controls_section(
        'tahefobu_button_effect_style_section',
                    [
                        'label' => esc_html__( 'Button Animation', 'header-footer-builder-for-elementor' ),
                        'tab' => Controls_Manager::TAB_STYLE,
                    ]
                    );

          $this->add_control(
                'tahefobu_button_animation_effect',
                [
                    'label'   => esc_html__('Animation Effect', 'header-footer-builder-for-elementor'),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'from-left',
                    'options' => [
                        'none'             => esc_html__( 'None', 'header-footer-builder-for-elementor' ),
                        'from-left'        => esc_html__('From Left', 'header-footer-builder-for-elementor'),
                        'reveal-radial'        => esc_html__('Reveal Radial', 'header-footer-builder-for-elementor'),
                        'from-right'       => esc_html__('From Right', 'header-footer-builder-for-elementor'),
                        'from-top'         => esc_html__('From Top', 'header-footer-builder-for-elementor'),
                        'from-bottom'      => esc_html__('From Bottom', 'header-footer-builder-for-elementor'),
                        'expand-vertical'  => esc_html__('Expand Vertical', 'header-footer-builder-for-elementor'),
                        'expand-horizontal'=> esc_html__('Expand Horizontal', 'header-footer-builder-for-elementor'),
                        'expand-circular'  => esc_html__('Expand Circular', 'header-footer-builder-for-elementor'),
                    ],
                ]
            );

          // Button effect Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'tahefobu_button_effect_background',
                'label'    => esc_html__( 'Effect Background', 'header-footer-builder-for-elementor' ),
                'types'    => [ 'classic', 'gradient' ],
                'selector' => '
                    {{WRAPPER}} .tahefobu-custom-button.from-left::before,
                    {{WRAPPER}} .tahefobu-custom-button.from-right::before,
                    {{WRAPPER}} .tahefobu-custom-button.from-top::before,
                    {{WRAPPER}} .tahefobu-custom-button.from-bottom::before,
                    {{WRAPPER}} .tahefobu-custom-button.expand-vertical::before,
                    {{WRAPPER}} .tahefobu-custom-button.expand-horizontal::before,
                    {{WRAPPER}} .tahefobu-custom-button.expand-circular::before,
                    {{WRAPPER}} .tahefobu-custom-button.reveal-radial::before
                ',
            ]
        );
        $this->end_controls_section();

        
        $this->start_controls_section(
            'tahefobu_button_style_section',
            [
                'label' => esc_html__( 'Button', 'header-footer-builder-for-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
            );

        // button width
        $this->add_responsive_control(
            'icon_button_width',
            [
                'label' => esc_html__('Button Width', 'header-footer-builder-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Button Alignment
        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'header-footer-builder-for-elementor' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'header-footer-builder-for-elementor' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'header-footer-builder-for-elementor' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button-container' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs(
			'tahefobu_button_style_tabs'
		);
        //------------------start normal tab------------
        $this->start_controls_tab(
			'style_normal_button_tab',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);

        // Button Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'tahefobu_button_background',
                'label'    => esc_html__( 'Background', 'header-footer-builder-for-elementor' ),
                'types'    => [ 'classic', 'gradient', 'video' ], 
                'selector' => '{{WRAPPER}} .tahefobu-custom-button',
            ]
        );

        // Padding
        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__( 'Padding', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
          // Button Border Control
          $this->add_group_control(
            Group_Control_Border::get_type(),
             [
                 'name' => 'button_border',
                 'label' => esc_html__('Border', 'header-footer-builder-for-elementor'),
                 'selector' => '{{WRAPPER}} .tahefobu-custom-button',
             ]
         );
          // Button Radius
        $this->add_responsive_control(
            'button_radius',
            [
                'label' => esc_html__( 'Border Radius', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 6,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
          // Button Shadow
          $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow',
                'label' => esc_html__( 'Box Shadow', 'header-footer-builder-for-elementor' ),
                'selector' => '{{WRAPPER}} .tahefobu-custom-button',
            ]
        );
        $this->end_controls_tab(); //---------end normal tab

        //-----------------start hover tab--------------
        $this->start_controls_tab(
			'style_hover_buttoon_tab',
			[
				'label' => esc_html__( 'Hover', 'header-footer-builder-for-elementor' ),
			]
		);

         // Button Background
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name'     => 'tahefobu_button_background_hover',
                'label'    => esc_html__( 'Background', 'header-footer-builder-for-elementor' ),
                'types'    => [ 'classic', 'gradient', 'video' ], 
                'selector' => '{{WRAPPER}} .tahefobu-custom-button:hover',
            ]
        );

        // Padding
        $this->add_responsive_control(
            'padding_hover',
            [
                'label' => esc_html__( 'Padding', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
          // Button Border Control
          $this->add_group_control(
            Group_Control_Border::get_type(),
             [
                 'name' => 'button_border_hover',
                 'label' => esc_html__('Border', 'header-footer-builder-for-elementor'),
                 'selector' => '{{WRAPPER}} .tahefobu-custom-button:hover',
             ]
         );
          // Button Radius
        $this->add_responsive_control(
            'button_radius_hover',
            [
                'label' => esc_html__( 'Border Radius', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
          // Button Shadow
          $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_shadow_hover',
                'label' => esc_html__( 'Box Shadow', 'header-footer-builder-for-elementor' ),
                'selector' => '{{WRAPPER}} .tahefobu-custom-button:hover',
            ]
        );
        
        $this->end_controls_tab(); //end hover control tab
        $this->end_controls_tabs();
        $this->end_controls_section();
// ==============================end button style====================================

// ============================== Typography Style Section ================================
//===================================================================================== 
  $this->start_controls_section(
            'button_typography_style_section',
            [
                'label' => esc_html__( 'Typography', 'header-footer-builder-for-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
           // Button Text Style
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => esc_html__( 'Typography', 'header-footer-builder-for-elementor' ),
                'selector' => '{{WRAPPER}} .tahefobu-custom-button',
            ]
        );

        $this->start_controls_tabs(
			'tahefobu_button_text_style_tabs'
		);
        //------------------start normal tab------------
        $this->start_controls_tab(
			'style_button_text_normal',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);
         $this->add_responsive_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();


        //-----------------start hover tab--------------
        $this->start_controls_tab(
			'style_button_text_hover',
			[
				'label' => esc_html__('Hover', 'header-footer-builder-for-elementor'),
			]
		);
         $this->add_responsive_control(
            'button_text_color_hover',
            [
                'label' => esc_html__( 'Text Color', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
       
// ============================== Icon Style Section ================================
//=====================================================================================

        $this->start_controls_section(
            'button_icon_style_section',
            [
                'label' => esc_html__( 'Icon', 'header-footer-builder-for-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
         // Icon Position
        $this->add_responsive_control(
            'icon_position',
            [
                'label' => esc_html__( 'Icon Position', 'header-footer-builder-for-elementor' ),
                'type'  => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'header-footer-builder-for-elementor' ),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'header-footer-builder-for-elementor' ),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle'  => false,
            ]
        );

        // Icon Size
        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 18,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tahefobu-custom-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

      
        
        // icon top to bottom spacing///
        $this->add_responsive_control(
            'tahefobu_icon_button_top_position',
            [
                'label' => __( 'Top Position', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-icon-button-wrapper' => 'position: relative; top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs(
			'icon_style_tabs'
		);
        //-------------------------start icon normal tab 
        $this->start_controls_tab(
			'icon_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);
         // Icon Color
         $this->add_responsive_control(
            'icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tahefobu-custom-button svg' => 'fill: {{VALUE}};',
                ],
            ]
        );
          // Icon Spacing
        $this->add_responsive_control(
            'icon_spacing',
            [
                'label' => esc_html__( 'Icon Spacing', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button i' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tahefobu-custom-button svg' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_position' => 'left',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_spacing_right',
            [
                'label' => esc_html__( 'Icon Spacing', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button i' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tahefobu-custom-button svg' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_position' => 'right',
                ],
            ]
        );

		$this->end_controls_tab(); //end normal tab

         //-------------------------start icon hover tab 
         $this->start_controls_tab(
			'icon_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'header-footer-builder-for-elementor' ),
			]
		);
         // Icon Color
         $this->add_responsive_control(
            'icon_color_hover',
            [
                'label' => esc_html__( 'Icon Color', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button:hover.tahefobu-custom-button i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .tahefobu-custom-button:hover.tahefobu-custom-button svg' => 'fill: {{VALUE}};',
                ],
            ]
        );
          // Icon Spacing
        $this->add_responsive_control(
            'icon_spacing_hover',
            [
                'label' => esc_html__( 'Icon Spacing', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button:hover i' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tahefobu-custom-button:hover svg' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_position' => 'left',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_spacing_right_hover',
            [
                'label' => esc_html__( 'Icon Spacing', 'header-footer-builder-for-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tahefobu-custom-button:hover i' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .tahefobu-custom-button:hover svg' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'icon_position' => 'right',
                ],
            ]
        );

        
		$this->end_controls_tab(); //end hover tab
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $button_text   = esc_html( $settings['button_text'] );
        $button_url    = isset( $settings['link']['url'] ) ? esc_url( $settings['link']['url'] ) : '#';
        $icon_position = isset( $settings['icon_position'] ) ? esc_attr( $settings['icon_position'] ) : 'left';
        $anim_class    = isset( $settings['tahefobu_button_animation_effect'] ) ? esc_attr( $settings['tahefobu_button_animation_effect'] ) : '';
        $has_icon      = ! empty( $settings['button_icon']['value'] );

        // Link attributes
        $this->add_render_attribute( 'button', [
            'class' => [ 'tahefobu-custom-button', $anim_class ],
            'href'  => $button_url,
            'style' => 'display:inline-flex;align-items:center;',
        ] );

        if ( ! empty( $settings['link']['is_external'] ) ) {
            $this->add_render_attribute( 'button', 'target', '_blank' );
            $this->add_render_attribute( 'button', 'rel', 'noopener noreferrer' );
        }
        if ( ! empty( $settings['link']['nofollow'] ) ) {
            $this->add_render_attribute( 'button', 'rel', 'nofollow' );
        }
        ?>
        <div class="tahefobu-custom-button-container">
            <a <?php $this->print_render_attribute_string( 'button' ); ?>>

                <?php if ( 'left' === $icon_position && $has_icon ) : ?>
                    <span class="tahefobu-icon-button-wrapper">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>

                <span class="text-wrapper"><?php echo esc_html( $button_text ); ?></span>

                <?php if ( 'right' === $icon_position && $has_icon ) : ?>
                    <span class="tahefobu-icon-button-wrapper">
                        <?php \Elementor\Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </span>
                <?php endif; ?>

            </a>
        </div>
        <?php
    }

}

// Widget registration is handled in the main plugin file via the
// elementor/widgets/register hook — do not register manually here.
