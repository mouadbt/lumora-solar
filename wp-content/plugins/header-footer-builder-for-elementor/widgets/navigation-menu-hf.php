<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class TAHEFOBU_Navigation_Menu extends Widget_Base {

    protected $tahefobu_nav_menu_item = 1;

    public function get_name() {
        return 'tahefobu-navigation-menu';
    }

    public function get_title() {
        return esc_html__('Menu', 'header-footer-builder-for-elementor');
    }

    public function get_icon() {
        return 'eicon-nav-menu tahefobu-icon'; 
    }

    public function get_categories(): array {
        return [ 'tahefobu-hf-widgets' ];
    }

    public function get_keywords(): array {
		return [ 'menu', 'menu-item', 'navigation', 'nav', 'header', 'footer' ];
	}

	public function get_style_depends() {
        return ['tahefobu-navigation-menu-style'];
    }

    public function get_script_depends() {
        return [ 'tahefobu-navigation-menu-script' ];
    }

	protected function get_tahefobu_nav_menu_sl() {
		return $this->tahefobu_nav_menu_item++;
	}

	private function tahefobu_get_menus() {
		$tahefobu_navmenu = wp_get_nav_menus();

		$options = [];

		foreach ( $tahefobu_navmenu as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}

	protected function register_controls() {

		$this->start_controls_section(
			'tahefobu_nav_menu_section',
			[
				'label' => 'Menu',
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$tahefobu_navmenu = $this->tahefobu_get_menus();

		if ( ! empty( $tahefobu_navmenu ) ) {
			$this->add_control(
				'tahefobu_nav_menu_select',
				[
					'label' => esc_html__( 'Select Menu', 'header-footer-builder-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $tahefobu_navmenu,
					'default' => array_keys( $tahefobu_navmenu )[0],
					'save_default' => true,
				]
			);
		} else {
			$this->add_control(
				'tahefobu_nav_menu_select',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf(
						/* translators: %s is the admin URL for creating a menu */
						 __( '<span>Make Your Menu First - </span> <strong><a href="%s" target="_blank">Click Here</a> </strong>', 'header-footer-builder-for-elementor' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		$this->add_responsive_control(
			'tahefobu_navmenu_alignment',
			[
				'label' => esc_html__( 'Align', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
				'widescreen_default' => 'left',
				'laptop_default' => 'left',
				'tablet_extra_default' => 'left',
				'tablet_default' => 'left',
				'mobile_extra_default' => 'left',
				'mobile_default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-right',
					]
				],
				'prefix_class' => 'tahefobu-main-menu-align-%s',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tahefobu_navmenu_items_section',
			[
				'label' => esc_html__( 'Menu Items', 'header-footer-builder-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'tahefobu_nav_menu_dropdown_icon',
			[
				'label' => esc_html__( 'Dropdown Icon', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'caret-down',
				'options' => [
					'none' => esc_html__( 'None', 'header-footer-builder-for-elementor' ),
					'caret-down' => esc_html__( 'Triangle', 'header-footer-builder-for-elementor' ),
					'angle-down' => esc_html__( 'Angle', 'header-footer-builder-for-elementor' ),
					'chevron-down' => esc_html__( 'Chevron', 'header-footer-builder-for-elementor' ),
					'plus' => esc_html__( 'Plus', 'header-footer-builder-for-elementor' ),
				],
				'prefix_class' => 'tahefobu-sub-icon-',
			]
		);

		$this->add_control(
			'tahefobu_menu_items_submenu_position',
			[
				'label' => esc_html__( 'Sub Menu Position', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__( 'Inline', 'header-footer-builder-for-elementor' ),
					'absolute' => esc_html__( 'Absolute', 'header-footer-builder-for-elementor' ),
				],
				'prefix_class' => 'tahefobu-sub-menu-position-',
				'condition' => [
					'menu_layout' => 'vertical',
				],
			]
		);

		$this->add_control(
			'tahefobu_nav_menu_dropdown_style',
			[
				'label' => esc_html__( 'Dropdown Display Style', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => esc_html__( 'Hover', 'header-footer-builder-for-elementor' ),
					'click' => esc_html__( 'Click', 'header-footer-builder-for-elementor' ),
				],
			]
		);

        $this->add_control(
			'tahefobu_nav_menu_dropdown_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.2,
				'min' => 0,
				'max' => 5,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .tahefobu-menu-item.tahefobu-pointer-item' => 'transition-duration: {{VALUE}}s',
					'{{WRAPPER}} .tahefobu-menu-item.tahefobu-pointer-item:before' => 'transition-duration: {{VALUE}}s',
					'{{WRAPPER}} .tahefobu-menu-item.tahefobu-pointer-item:after' => 'transition-duration: {{VALUE}}s',
				],
			]
		);

		$this->end_controls_section(); 

		$this->start_controls_section(
			'tahefobu_navmenu_mobile_section',
			[
				'label' => esc_html__( 'Mobile Menu', 'header-footer-builder-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
        $this->start_controls_tabs( 'tahefobu_navmenu_mobile_section_dropdown_tab' );
        $this->start_controls_tab(
            'tahefobu_navmenu_mobile_section_dropdown_normal_tab',
            [
                'label' => esc_html__( 'Dropdown', 'header-footer-builder-for-elementor' ),
            ]
        );
        $breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();

		$this->add_control(
			'tahefobu_navmenu_mobile_display',
			[
				'label' => esc_html__( 'Brakpoint', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mobile',
				'options' => [
					/* translators: %d: Breakpoint number. */
					'mobile' => sprintf( esc_html__( 'Mobile (≤ %dpx)', 'header-footer-builder-for-elementor' ), $breakpoints['mobile']->get_default_value() ),
					/* translators: %d: Breakpoint number. */
					'tablet' => sprintf( esc_html__( 'Tablet (≤ %dpx)', 'header-footer-builder-for-elementor' ), $breakpoints['tablet']->get_default_value() ),
				],
				'prefix_class' => 'tahefobu-nav-menu-bp-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mob_stretch',
			[
				'label' => esc_html__( 'Dropdown Width', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'full-width',
				'options' => [
					'auto-width' => esc_html__( 'None', 'header-footer-builder-for-elementor' ),
					'full-width' => esc_html__( 'Full Width', 'header-footer-builder-for-elementor' ),
					'custom-width' => esc_html__( 'Custom Width', 'header-footer-builder-for-elementor' ),
				],
				'prefix_class' => 'tahefobu-mobile-menu-',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_mob_stretch_width',
			[
				'label' => esc_html__( 'Width', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'tablet_default' => [
					'size' => 300,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 300,
					'unit' => 'px',
				],
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.tahefobu-mobile-menu-custom-width .tahefobu-mobile-nav-menu' => 'width: {{SIZE}}{{UNIT}};',
				],
                'condition' => [
                    'tahefobu_navmenu_mob_stretch' => 'custom-width', 
                ],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mob_dropdown_alignment',
			[
				'label' => esc_html__( 'Dropdown Section Alignment', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-right',
					]
				],
				'prefix_class' => 'tahefobu-mobile-menu-drdown-align-',
                'condition' => [
					'tahefobu_navmenu_mob_stretch' => [ 'custom-width', 'auto-width' ],
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mob_dropdown_item_alignment',
			[
				'label' => esc_html__( 'Item Alignment', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-right',
					]
				],
				'prefix_class' => 'tahefobu-mobile-menu-item-align-',
			]
		);
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tahefobu_navmenu_mobile_section_dropdown_humburger_tab',
            [
                'label' => esc_html__( 'Hamburger', 'header-footer-builder-for-elementor' ),
            ]
        );

		$this->add_control(
			'toggle_btn_burger',
			[
				'label' => esc_html__( 'Toggle Icon', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'v1',
				'options' => [
					'v1' => esc_html__( 'Icon 1', 'header-footer-builder-for-elementor' ),
					'v2' => esc_html__( 'Icon 2', 'header-footer-builder-for-elementor' ),
					'v3' => esc_html__( 'Icon 3', 'header-footer-builder-for-elementor' ),
					'v4' => esc_html__( 'Icon 4', 'header-footer-builder-for-elementor' ),
					'v5' => esc_html__( 'Icon 5', 'header-footer-builder-for-elementor' ),
				],
				'prefix_class' => 'tahefobu-mobile-toggle-',
			]
		);

		$this->add_responsive_control(
			'toggle_btn_align',
			[
				'label' => esc_html__( 'Toggle Align', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'header-footer-builder-for-elementor' ),
						'icon' => 'eicon-h-align-right',
					]
				],
				'selectors_dictionary' => [
					'left' => 'text-align: left',
					'center' => 'text-align: center',
					'right' => 'text-align: right',
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle-wrap' => '{{VALUE}}',
				],
			]
		);
        $this->end_controls_tab();
        $this->end_controls_tabs();
		$this->end_controls_section(); 
		
		// -------------------------------------------------------------------------  Style Section Start -------------------------------------------------------------- //

		$this->start_controls_section(
			'tahefobu_navmenu_style_section',
			[
				'label' => esc_html__( 'Menu', 'header-footer-builder-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tahefobu_navmenu_style_tab' );

		$this->start_controls_tab(
			'tahefobu_navmenu_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_item_color',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item,
					 {{WRAPPER}} .tahefobu-nav-menu > .menu-item-has-children > .tahefobu-sub-icon' => 'color: {{VALUE}};',
				],
			]
		);
		//menu item background color
		$this->add_control(
			'tahefobu_navmenu_item_background_color',
			[
				'label' => esc_html__('Background Color', 'header-footer-builder-for-elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'tahefobu_navmenu_item_highlight',
			[
				'label' => esc_html__( 'Active Item', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'tahefobu_navmenu_items_sub_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 25,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .menu-item-has-children .tahefobu-sub-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.tahefobu-pointer-background:not(.tahefobu-sub-icon-none) .tahefobu-nav-menu-horizontal .menu-item-has-children .tahefobu-pointer-item' => 'padding-right: calc({{SIZE}}px + {{menu_items_padding_hr.SIZE}}px);',
					'{{WRAPPER}}.tahefobu-pointer-border:not(.tahefobu-sub-icon-none) .tahefobu-nav-menu-horizontal .menu-item-has-children .tahefobu-pointer-item' => 'padding-right: calc({{SIZE}}px + {{menu_items_padding_hr.SIZE}}px);',
				],
				'separator' => 'before'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tahefobu_navmenu_items_typography',
				'selector' => '{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item,{{WRAPPER}} .tahefobu-mobile-nav-menu a,{{WRAPPER}} .tahefobu-mobile-toggle-text',
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_items_padding_hr',
			[
				'label' => esc_html__( 'Inner Horizontal Spacing', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 7,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.tahefobu-pointer-background:not(.tahefobu-sub-icon-none) .tahefobu-nav-menu-vertical .menu-item-has-children .tahefobu-sub-icon' => 'text-indent: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.tahefobu-pointer-border:not(.tahefobu-sub-icon-none) .tahefobu-nav-menu-vertical .menu-item-has-children .tahefobu-sub-icon' => 'text-indent: -{{SIZE}}{{UNIT}};',

				]
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_items_padding_bg_hr',
			[
				'label' => esc_html__( 'Horizontal Spacing', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu > .menu-item' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tahefobu-nav-menu-vertical .tahefobu-nav-menu > li > .tahefobu-sub-menu' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.tahefobu-main-menu-align-left .tahefobu-nav-menu-vertical .tahefobu-nav-menu > li > .tahefobu-sub-icon' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.tahefobu-main-menu-align-right .tahefobu-nav-menu-vertical .tahefobu-nav-menu > li > .tahefobu-sub-icon' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_items_padding_vr',
			[
				'label' => esc_html__( 'Vertical Spacing', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tahefobu_navmenu_items_border',
				'fields_options' => [
					'border' => [
						'default' => '',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => true,
						],
					],
					'color' => [
						'default' => '#222222',
					],
				],
				'selector' => '{{WRAPPER}} .tahefobu-menu-item',
			]
		);
		//border radius
		$this->add_control(
			'tahefobu_navmenu_items_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tahefobu_navmenu_style_hover_tab',
			[
				'label' => esc_html__( 'Active', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_item_color_hover',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2e3194',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item:hover,
					 {{WRAPPER}} .tahefobu-nav-menu > .menu-item-has-children:hover > .tahefobu-sub-icon,
					 {{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item.tahefobu-active-menu-item,
					 {{WRAPPER}} .tahefobu-nav-menu > .menu-item-has-children.current_page_item > .tahefobu-sub-icon' => 'color: {{VALUE}};',
				],
			]
		);
		//active item background color
		$this->add_control(
			'tahefobu_navmenu_item_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item:hover,
					 {{WRAPPER}} .tahefobu-nav-menu .tahefobu-menu-item.tahefobu-active-menu-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// ------------------------------------------------------------------------------------ Sub Menu -------------------------------------------------------------- //
		$this->start_controls_section(
			'section_style_sub_menu',
			[
				'label' => esc_html__( 'Sub Menu', 'header-footer-builder-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tahefobu_navmenu_sub_menu_style_tab' );

		$this->start_controls_tab(
			'tahefobu_navmenu_sub_menu_style_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_color',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item,
					 {{WRAPPER}} .tahefobu-sub-menu > .menu-item-has-children .tahefobu-sub-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_color_bg',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item' => 'background-color: {{VALUE}};',
				],
				'separator' => 'after'
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tahefobu_navmenu_sub_menu_typography',
				'selector' => '{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item'
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_sub_menu_padding_hr',
			[
				'label' => esc_html__( 'Padding', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-icon' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.tahefobu-main-menu-align-right .tahefobu-nav-menu-vertical .tahefobu-sub-menu .tahefobu-sub-icon' => 'left: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_sub_menu_padding_vr',
			[
				'label' => esc_html__( 'Padding', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 13,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_sub_menu_offset',
			[
				'label' => esc_html__( 'Vertical Gap', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 150,
					],
				],
				'default' => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu-horizontal .tahefobu-nav-menu > li > .tahefobu-sub-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		// dropdown offset x control

		$this->add_responsive_control(
			'tahefobu_navmenu_sub_menu_offset_x',
			[
				'label' => esc_html__( 'Offset X', 'header-footer-builder-for-elementor' ),
				'type'  => Controls_Manager::SLIDER,

				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],

				'size_units' => [ 'px', '%' ],

				'selectors' => [
					'{{WRAPPER}} .tahefobu-nav-menu-horizontal .tahefobu-nav-menu > li > .tahefobu-sub-menu' 
						=> 'transform: translateX({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_divider',
			[
				'label' => esc_html__( 'Divider', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'tahefobu-sub-divider-',
				'default' => 'yes',
				'return_value' => 'yes'
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_divider_color',
			[
				'label' => esc_html__( 'Divider Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e8e8e8',
				'selectors' => [
					'{{WRAPPER}}.tahefobu-sub-divider-yes .tahefobu-sub-menu li:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'tahefobu_navmenu_sub_menu_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_divider_height',
			[
				'label' => esc_html__( 'Divider Height', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}}.tahefobu-sub-divider-yes .tahefobu-sub-menu li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'tahefobu_navmenu_sub_menu_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_divider_ctrl',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tahefobu_navmenu_sub_menu_border',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => true,
						],
					],
					'color' => [
						'default' => '#E8E8E8',
					],
				],
				'selector' => '{{WRAPPER}} .tahefobu-sub-menu',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tahefobu_navmenu_sub_menu_box_shadow',
				'remove' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .tahefobu-sub-menu',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_hover',
			[
				'label' => esc_html__( 'Hover', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_color_hover',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item:hover,
					 {{WRAPPER}} .tahefobu-sub-menu > .menu-item-has-children .tahefobu-sub-menu-item:hover .tahefobu-sub-icon,
					 {{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item.tahefobu-active-menu-item,
					 {{WRAPPER}} .tahefobu-sub-menu > .menu-item-has-children.current_page_item .tahefobu-sub-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_sub_menu_color_bg_hover',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2e3194',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item:hover,
					 {{WRAPPER}} .tahefobu-sub-menu .tahefobu-sub-menu-item.tahefobu-active-menu-item' => 'background-color: {{VALUE}};',
				],
				'separator' => 'after'
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section(); // End Controls Section

		// -------------------------------------------------------------------- Mobile Menu ---------------------------------------------------------------------------- /
		$this->start_controls_section(
			'tahefobu_navmenu_mobile_menu_section_style',
			[
				'label' => esc_html__( 'Mobile Menu', 'header-footer-builder-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tahefobu_navmenu_mobile_menu_style' );

		$this->start_controls_tab(
			'tahefobu_navmenu_mobile_menu_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_color',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu a,
					 {{WRAPPER}} .tahefobu-mobile-nav-menu .menu-item-has-children > a:after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu li' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'tahefobu_navmenu_mobile_menu_highlight',
			[
				'label' => esc_html__( 'Active Item', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'return_value' => 'yes'
			]
		);
		// mobile menu item width
		$this->add_responsive_control(
			'tahefobu_navmenu_mobile_menu_width',
			[
				'label' => esc_html__( 'Menu Item Width', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu a' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_padding_hr',
			[
				'label' => esc_html__( 'Horizontal Spacing', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu a' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tahefobu-mobile-nav-menu .menu-item-has-children > a:after' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_padding_vr',
			[
				'label' => esc_html__( 'Vertical Spacing', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu .tahefobu-mobile-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_divider',
			[
				'label' => esc_html__( 'Divider', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'tahefobu-mobile-divider-',
				'default' => 'yes',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_divider_color',
			[
				'label' => esc_html__( 'Divider Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e8e8e8',
				'selectors' => [
					'{{WRAPPER}}.tahefobu-mobile-divider-yes .tahefobu-mobile-nav-menu a' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'tahefobu_navmenu_mobile_menu_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_divider_height',
			[
				'label' => esc_html__( 'Divider Height', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}}.tahefobu-mobile-divider-yes .tahefobu-mobile-nav-menu a' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'tahefobu_navmenu_mobile_menu_divider' => 'yes',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_sub_font_size',
			[
				'label' => esc_html__( 'Sub Menu Font Size', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'default' => [
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu .tahefobu-mobile-sub-menu-item' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_sub_padding_vr',
			[
				'label' => esc_html__( 'Sub Menu Vertical Spacing', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'default' => [
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu .tahefobu-mobile-sub-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'tahefobu_navmenu_mobile_menu_offset',
			[
				'label' => esc_html__( 'Dropdown Offset', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'px' => [
					'min' => 1,
					'min' => 50,
				],
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tahefobu_navmenu_mobile_menu_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_color_hover',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu li a:hover,
					 {{WRAPPER}} .tahefobu-mobile-nav-menu .menu-item-has-children > a:hover:after,
					 {{WRAPPER}} .tahefobu-mobile-nav-menu li a.tahefobu-active-menu-item,
					 {{WRAPPER}} .tahefobu-mobile-nav-menu .menu-item-has-children.current_page_item > a:hover:after' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_mobile_menu_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				// 'scheme' => [
				// 	'type' => Color::get_type(),
				// 	'value' => Color::COLOR_3,
				// ],
				'default' => '#2e3194',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-nav-menu a:hover,
					 {{WRAPPER}} .tahefobu-mobile-nav-menu a.tahefobu-active-menu-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section(); // End Controls Section

		// -------------------------------------------------------------------- Toggle Button Start -------------------------------------------------------------------- /
		$this->start_controls_section(
			'tahefobu_navmenu_hamburger_style_section',
			[
				'label' => esc_html__( 'Hamburger', 'header-footer-builder-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tahefobu_navmenu_hamburger_style_tab' );

		$this->start_controls_tab(
			'tahefobu_navmenu_hamburger_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_color',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .tahefobu-mobile-toggle-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tahefobu-mobile-toggle-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'tahefobu_navmenu_hamburger_btn_lines_height',
			[
				'label' => esc_html__( 'Lines Height', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'default' => [
					'size' => 3,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle-line' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_line_space',
			[
				'label' => esc_html__( 'Space', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'default' => [
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle-line' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_width',
			[
				'label' => esc_html__( 'Width', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 150,
					],
				],
				'default' => [
					'size' => 45,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle' => 'width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_padding',
			[
				'label' => esc_html__( 'Padding', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_border_width',
			[
				'label' => esc_html__( 'Border Width', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'default' => [
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle' => 'border-width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tahefobu_navmenu_hamburger_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'header-footer-builder-for-elementor' ),
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_color_hover',
			[
				'label' => esc_html__( 'Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#2e3194',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .tahefobu-mobile-toggle:hover .tahefobu-mobile-toggle-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tahefobu-mobile-toggle:hover .tahefobu-mobile-toggle-line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tahefobu_navmenu_hamburger_btn_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'header-footer-builder-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tahefobu-mobile-toggle:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section(); // End Controls Section

	}

	public function tahefobu_custom_nav_menu_link( $atts, $item, $args, $depth ) {
		$settings = $this->get_active_settings();

		// Main or Mobile
		if ( strpos( $args->menu_id, 'mobile-menu' ) === false ) {
		    $main 	= 'tahefobu-menu-item tahefobu-pointer-item';
		    $sub 	= 'tahefobu-sub-menu-item';
		    $active = $settings['tahefobu_navmenu_item_highlight'] === 'yes' ? ' tahefobu-active-menu-item' : '';
		} else {
		    $main 	= 'tahefobu-mobile-menu-item';
		    $sub 	= 'tahefobu-mobile-sub-menu-item';
		    $active = $settings['tahefobu_navmenu_mobile_menu_highlight'] === 'yes' ? ' tahefobu-active-menu-item' : '';
		}

		$classes = $depth ? $sub : $main;

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= $active;
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' '. $classes;
		}

		return $atts;
	}

	public function tahefobu_custom_nav_menu_submenu( $classes ) {
		$classes[] = 'tahefobu-sub-menu';

		return $classes;
	}

	public function tahefobu_walker_custom_nav_menu( $output, $item, $depth, $args ) {
		$settings = $this->get_active_settings();

		if ( strpos( $args->menu_class, 'tahefobu-nav-menu' ) !== false ) {
			if ( in_array( 'menu-item-has-children', $item->classes ) ) {
				$item_class = 'tahefobu-menu-item tahefobu-pointer-item';

				if ( in_array( 'current-menu-item', $item->classes ) || in_array( 'current-menu-ancestor', $item->classes ) ) {
					$item_class .= ' tahefobu-active-menu-item';
				}

				// Sub Menu Classes
				if ( $depth > 0 ) {
					$item_class = 'tahefobu-sub-menu-item';

					if ( in_array( 'current-menu-item', $item->classes ) || in_array( 'current-menu-ancestor', $item->classes ) ) {
						$item_class .= ' tahefobu-active-menu-item';
					}
				}

				// Add Sub Menu Icon
				$output  ='<a href="'. esc_url($item->url) .'" class="'. esc_attr($item_class) .'">'. esc_html($item->title);
				// GOGA: render language switcher correctly
				$output = '<a href="' . esc_url($item->url) . '" class="' . esc_attr($item_class) . '">'
							. wp_kses($item->title, array(
								'span' => array('class' => array()), // Allow <span> tags with class attribute
								'a' => array( // Allow <a> tags with specified attributes
									'href' => array(),
									'title' => array(),
									'class' => array(),
								),
								'img' => array( // Allow <img> tags with specified attributes
									'src' => array(),
									'alt' => array(),
									'title' => array(),
									'width' => array(),
									'height' => array(),
									'class' => array(),
								),
								'i' => array('class' => array()), // Allow <i> tags with class attribute for icons
							));


				if ( $depth > 0 ) {
					if ( 'inline' === $settings['tahefobu_menu_items_submenu_position'] ) {
						$output .='<i class="tahefobu-sub-icon fas" aria-hidden="true"></i>';
					} else {
						$output .='<i class="tahefobu-sub-icon fas tahefobu-sub-icon-rotate" aria-hidden="true"></i>';
					}
				} else {
					if ( 'absolute' === $settings['tahefobu_menu_items_submenu_position'] ) {
						$output .='<i class="tahefobu-sub-icon fas tahefobu-sub-icon-rotate" aria-hidden="true"></i>';
					} else {
						$output .='<i class="tahefobu-sub-icon fas" aria-hidden="true"></i>';
					}
				}

				$output .='</a>';		
			}
		}

		return $output;
	}

	protected function render() {
		$tahefobu_menu_list = $this->tahefobu_get_menus();
	
		if ( ! $tahefobu_menu_list ) {
			return;
		}

		// Get Settings
		$settings = $this->get_active_settings();

		$args = [
			'echo' => false,
			'menu' => $settings['tahefobu_nav_menu_select'],
			'menu_class' => 'tahefobu-nav-menu',
			'menu_id' => 'menu-'. $this->get_tahefobu_nav_menu_sl() .'-'. $this->get_id(),
			'container' => '',
			'fallback_cb' => '__return_empty_string',
		];
        //add filter for custom menus
		add_filter( 'walker_nav_menu_start_el', [ $this, 'tahefobu_walker_custom_nav_menu' ], 10, 4 );
		add_filter( 'nav_menu_link_attributes', [ $this, 'tahefobu_custom_nav_menu_link' ], 10, 4 );
		add_filter( 'nav_menu_submenu_css_class', [ $this, 'tahefobu_custom_nav_menu_submenu' ] );
		add_filter( 'nav_menu_item_id', '__return_empty_string' );

		// Generate Menu HTML
		$menu_html = wp_nav_menu( $args );

		// Generate Mobile Menu HTML
		$args['menu_id'] 	= 'mobile-menu-'. $this->get_tahefobu_nav_menu_sl() .'-'. $this->get_id();
		$args['menu_class'] = 'tahefobu-mobile-nav-menu';
		$moible_menu_html 	= wp_nav_menu( $args );

		// Remove Custom Filters
		remove_filter( 'nav_menu_link_attributes', [ $this, 'tahefobu_custom_nav_menu_link' ] );
		remove_filter( 'nav_menu_submenu_css_class', [ $this, 'tahefobu_custom_nav_menu_submenu' ] );
		remove_filter( 'walker_nav_menu_start_el', [ $this, 'tahefobu_walker_custom_nav_menu' ] );
		remove_filter( 'nav_menu_item_id', '__return_empty_string' );

		if ( empty( $menu_html ) ) {
			return;
		}

		// Main Menu
		echo '<nav class="tahefobu-nav-menu-container tahefobu-nav-menu-horizontal' .'" data-trigger="'. esc_attr($settings['tahefobu_nav_menu_dropdown_style']) .'">';
			echo ''. $menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</nav>';

		// Mobile Menu
		echo '<nav class="tahefobu-mobile-nav-menu-container">';

			// Toggle Button
			echo '<div class="tahefobu-mobile-toggle-wrap">';
				echo '<div class="tahefobu-mobile-toggle">';

						echo '<span class="tahefobu-mobile-toggle-line"></span>';
						echo '<span class="tahefobu-mobile-toggle-line"></span>';
						echo '<span class="tahefobu-mobile-toggle-line"></span>';

				echo '</div>';
			echo '</div>';

			// Menu
			echo ''. $moible_menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</nav>';
	}

    
}
// Register the widget with Elementor.
// Plugin::instance()->widgets_manager->register_widget_type( new TAHEFOBU_Navigation_Menu() );