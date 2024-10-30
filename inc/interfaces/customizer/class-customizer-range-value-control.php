<?php
if (!class_exists('Customizer_Range_Value_Control')){
class Customizer_Range_Value_Control extends WP_Customize_Control {
	public $type = 'range-value';
	
	//enqueue css /js 
	public function enqueue() {
		wp_enqueue_script( 'customizer-range-value-control', LLMS_BKPK_URL . '/inc/interfaces/customizer/js/customizer-range-value-control.js', array( 'jquery' ), rand(), true );
		wp_register_style( 'customizer-range-value-control', LLMS_BKPK_URL . '/inc/interfaces/customizer/css/customizer-range-value-control.css' );
		wp_enqueue_style( 'customizer-range-value-control' );
	}
	
	/**
	 * Render the control's content.
	 *
	 * @author soderlind
	 * @version 1.2.0
	 */
	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="range-slider"  style="width:100%; display:flex;flex-direction: row;justify-content: flex-start;">
				<span  style="width:100%; flex: 1 0 0; vertical-align: middle;"><input onlclick='console.log("bkpk:clicked");' class="range-slider__range" type="range" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->input_attrs(); $this->link(); ?>>
				<span class="range-slider__value">01</span></span>
			</div>
			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<?php
	}
}
}