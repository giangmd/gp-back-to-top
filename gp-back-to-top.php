<?php
/*
 * Plugin Name: GP Back To Top
Plugin URI: http://wordpress.org/plugins/gp-back-to-top
Description: Create Back To Top Button Custom.
Author: Giang Peter
Author URI: http://github.com/giangmd
Version: 1.0
Liciense: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/ 

class GP_Back_To_Top
{
	const VERSION = '1.0';

	protected $fr_file;
	protected $ad_file;
	protected $d_w;
	protected $d_h;
	protected $d_fz;
	protected $d_bgr;
	protected $d_cl;
	protected $d_pd;
	protected $d_bt;
	protected $d_rt;

	function __construct()
	{
		$this->fr_file = 'gp-bttp';
		$this->ad_file = 'gp-admin';
		$this->d_w = 30;
		$this->d_h = 30;
		$this->d_fz = 14;
		$this->d_bgr = '#111f1c';
		$this->d_cl = '#ffffff';
		$this->d_pd = 5;
		$this->d_bt = 45;
		$this->d_rt = 20;

		add_action( 'admin_menu', array( $this, 'gp_create_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'gp_bttb_enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'gp_bttp_enqueue_scripts' ) );
	}

	public function gp_create_menu() {
		add_options_page( "GP Back To Top", "GP Back To Top", 'manage_options', 'gp-back-to-top', array($this, 'gp_create_options' ) );
	}

	public function gp_bttb_enqueue_admin_scripts() {
		wp_enqueue_style( 'bootstrap', plugins_url( '/lib/bootstrap-3.3.4/css/bootstrap.min.css', __FILE__ ), array(), '3.3.4' );
		wp_enqueue_style( 'style', plugins_url( '/css/style.css', __FILE__ ), array(), self::VERSION );

		$file_ad = filesize( dirname(__FILE__) . '\css\/'.$this->ad_file.'.css' );
		if ( $file_ad > 0 ) {
			wp_enqueue_style( 'style-modified', plugins_url( '/css/'.$this->ad_file.'.css', __FILE__ ), array(), self::VERSION );
		}

		wp_register_script( 'gp-bttb-js', plugins_url( '/js/main.js', __FILE__), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( 'gp-bttb-js' );
	}

	public function gp_bttp_enqueue_scripts() {
		wp_register_style( 'bootstrap', plugins_url( '/lib/bootstrap-3.3.4/css/bootstrap.min.css', __FILE__ ), array(), '3.3.4' );
		wp_register_style( 'gp-bttp-style', plugins_url( '/css/'.$this->fr_file.'.css', __FILE__ ), array(), self::VERSION );
    	wp_enqueue_style( 'gp-bttp-style' );

    	wp_register_script( 'gp-bttp-jquery', plugins_url( '/js/'.$this->fr_file.'.js', __FILE__ ), array('jquery'), self::VERSION, true );
    	wp_enqueue_script( 'gp-bttp-jquery' );
	}

	public function gp_create_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __('Permissions dinied to access') );
		}
	?>
		<div class="wrap">
			<h2>GP Back To Top Plugin</h2>
			<form action="" method="POST" class="form-horizontal gpbttb-form">
				<div class="form-group">
					<label for="width" class="col-sm-5 control-label">Width: </label>
					<div class="col-sm-5">
						<input type="number" min="1" max="100" name="width" id="width" class="form-control" value="<?php echo $this->d_w; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="height" class="col-sm-5 control-label">Height: </label>
					<div class="col-sm-5">
						<input type="number" min="1" max="100" name="height" id="height" class="form-control" value="<?php echo $this->d_h; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="font" class="col-sm-5 control-label">Font-size: </label>
					<div class="col-sm-5">
						<input type="number" min="1" max="100" name="font" id="font" class="form-control" value="<?php echo $this->d_fz; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="bg_color" class="col-sm-5 control-label">Background color: </label>
					<div class="col-sm-5">
						<input type="color" name="bg_color" id="bg_color" class="form-control" value="<?php echo $this->d_bgr; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="color" class="col-sm-5 control-label">Color: </label>
					<div class="col-sm-5">
						<input type="color" name="color" id="color" class="form-control" value="<?php echo $this->d_cl; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="bottom" class="col-sm-5 control-label">Bottom: </label>
					<div class="col-sm-5">
						<input type="number" min="1" max="100" name="bottom" id="bottom" class="form-control" value="<?php echo $this->d_bt; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="right" class="col-sm-5 control-label">Right: </label>
					<div class="col-sm-5">
						<input type="number" min="1" max="100" name="right" id="right" class="form-control" value="<?php echo $this->d_rt; ?>">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-5 col-sm-offset-5">
						<input type="submit" name="gp_bttb_up" value="Submit" class="form-control btn btn-primary">
					</div>
				</div>
			</form>
			<p>
				<div class="gp-back-to-top" id="gpToTop">
					<span class="glyphicon glyphicon-chevron-up"></span>
				</div>
			</p>
			<script type="text/javascript">
				(function ($) {
					$(document).ready(function() {
						var hexDigits = new Array
				        ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

						//Function to convert hex format to a rgb color
						function rgb2hex(rgb) {
						 	rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
						 	return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
						}
						function hex(x) {
						  	return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
						}

						var demo = $('.gp-back-to-top'),
							width = $('.gpbttb-form').find('#width'),
							height = $('.gpbttb-form').find('#height'),
							font = $('.gpbttb-form').find('#font'),
							bg_color = $('.gpbttb-form').find('#bg_color'),
							color = $('.gpbttb-form').find('#color'),
							bottom = $('.gpbttb-form').find('#bottom'),
							right = $('.gpbttb-form').find('#right');

						function updateStyle() {
							width.val(demo.outerWidth());
							height.val(demo.outerHeight());
							font.val( demo.css('font-size').replace("px", '') );
							bg_color.val( rgb2hex(demo.css('background-color')) );
							color.val( rgb2hex(demo.css('color')) );
						}

						updateStyle();
					});
				})(jQuery);
			</script>
		</div>
	<?php
		if ( $_POST['gp_bttb_up'] ) {

			$width = ( !empty($_POST['width']) ) ? $_POST['width'] : $this->d_w;
			$height = ( !empty($_POST['height']) ) ? $_POST['height'] : $this->d_h;
			$font = ( !empty($_POST['font']) ) ? $_POST['font'] : $this->d_fz;
			$bg_color = ( !empty($_POST['bg_color']) ) ? $_POST['bg_color'] : $this->d_bgr;
			$color = ( !empty($_POST['color']) ) ? $_POST['color'] : $this->d_cl;
			$bottom = ( !empty($_POST['bottom']) ) ? $_POST['bottom'] : $this->d_bt;
			$right = ( !empty($_POST['right']) ) ? $_POST['right'] : $this->d_rt;

			$txt = "/*
					 * Style GP Back To Top Plugin
					 *
					 * @author Giang Peter
					 */
					.gp-back-to-top {
						display: none;
						width: ".$width."px;
						height: ".$height."px;
						border-radius: 50%;
						padding: 5px;
						background-color: ".$bg_color.";
						color: ".$color.";
						text-align: center;
						position: fixed;
						z-index: 99999;
						bottom: ".$bottom."px;
						right: ".$right."px;
						font-size: ".$font."px;
						cursor: pointer;
					}
					.gp-back-to-top span {
						position: absolute;
						top: 24%;
						-webkit-transform: translateX(-50%);
						-moz-transform: translateX(-50%);
						-ms-transform: translateX(-50%);
						-o-transform: translateX(-50%);
						transform: translateX(-50%);
					}";

			$file = fopen( dirname(__FILE__) . '\css\/'.$this->fr_file.'.css', 'w') or die('File not found.');
			fwrite($file, $txt);
			fclose($file);

			$file_ad = fopen( dirname(__FILE__) . '\css\/'.$this->ad_file.'.css', 'w') or die('File not found.');
			fwrite($file_ad, $txt);
			fclose($file_ad);
			?>
			<script type="text/javascript">
				window.location.reload(true);
			</script>
			<?php
		}
	}
}

$gp_back_to_top = new GP_Back_To_Top();