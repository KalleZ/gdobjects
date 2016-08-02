<?php
	class gdImage
	{
		/* PNG filters */

		const PNG_NO_FILTER		= 1;
		const PNG_FILTER_NONE		= 2;
		const PNG_FILTER_SUB		= 3;
		const PNG_FILTER_UP		= 4;
		const PNG_FILTER_AVG		= 5;
		const PNG_FILTER_PAETH		= 6;
		const PNG_ALL_FILTERS		= 7;

		/* GD2 types */

		const GD2_RAW			= 1;
		const GD2_COMPRESSED		= 2;

		/* Layer effects */

		const EFFECT_REPLACE		= 1;
		const EFFECT_ALPHABLEND		= 2;
		const EFFECT_NORMAL		= 2;	/* Same as EFFECT_ALPHABLEND */
		const EFFECT_OVERLAY		= 3;

		/* Flip modes */

		const FLIP_HORIZONTAL		= 1;
		const FLIP_VERTICAL		= 2;
		const FLIP_BOTH			= 3;


		/* Image sizes, readonly */

		public $x		= 0;
		public $y		= 0;


		/*
		 * Meta information
		 *
		 * Some of these properties are overloaded, and have special 
		 * behavior if changed
		 */

		public $trueColor 	= false;	/* Calls ->toPalette() or ->toTrueColor() depending on the new value */
		public $alphaBlending	= false;	/* Only changable for true color images */
		public $saveAlpha	= false;	/* Save alpha flag, only works if $alphaBlending is off */
		public $antiAlias	= false;	/* For true color only */

		public $freed		= false;	/* [readonly] is ->destroy() called? */
		public $type		= 0;		/* [readonly] gd::XXX type constants */
		public $sendHeader	= false;	/* [readonly] Auto send header for outputs, defaults to false */


		/*
		 * Constructor for userland usage
		 *
		 * For internal use, this method may be omitted by the 
		 * gd::createFrom[*]() methods.
		 *
		 * This works for creating palette based images or true color 
		 * based images.
		 *
		 * The type is to hint a type for the shorthand syntaxes:
		 *
		 *  . __toString()
		 *  . __invoke()
		 *
		 * The type is may be auto set by gd::createFrom[*]() methods, 
		 * but not yet decided.
		 */
		public function __construct(int $x, int $y, bool $true_color = false, int $type = 0)
		{
			if($x < 1 || $y < 1)
			{
				throw new gdException('Invalid image sizes');
			}
			elseif($type && !self::isValidType($type))
			{
				throw new gdException('Invalid image type, or write support not supported for this type');
			}

			$this->x = $x;
			$this->y = $y;

			if($true_color)
			{
				$this->trueColor = $true_color;
			}

			if($type)
			{
				$this->type = $type;
			}
		}

		/* Destroy methods */

		public function __destruct()
		{
		}

		public function destroy()
		{
			$this->freed = true;
		}

		/* Internal helper function for verification of a type hint -- Marked as protected so child classes can use it */

		protected isValidType(int $type) : bool
		{
			static $type_map;

			if(!$type_map)
			{
				$test = [
						gd::JPEG 	=> gd::JPEG_WRITE, 
						gd::PNG 	=> gd::PNG_WRITE, 
						gd::WBMP 	=> gd::WBMP_WRITE, 
						gd::GIF 	=> gd::GIF_WRITE, 
						gd::WEBP 	=> gd::WEBP_WRITE, 
						gd::XPM 	=> gd::XPM_WRITE, 
						gd::XBM 	=> gd::XBM_WRITE, 	/* Maybe one day we will have write support */
						gd::GD 		=> gd::GD_WRITE, 
						gd::GD2 	=> gd::GD2_WRITE, 
						gd::BMP 	=> gd::BMP_WRITE
						];

				foreach($test as $intern_type => $write)
				{
					if(gd::INFO & $write)
					{
						$type_map[$intern_type] = true;
					}
				}
			}

			return(isset($type_map[$type]));
		}

		/*
		 * Output main handler and per type methods
		 *
		 * Individual methods have their own signatures for things like quality and filters
		 * and the $location parameter may be either a file path or a stream to save the 
		 * buffer to
		 */

		public function output() : void
		{
			static $method_map;

			if(!$this->type)
			{
				throw new gdException('No output type have been set');
			}

			if(!$method_map)
			{
				$method_map = [
						// XPM intentionally missing
						gd::JPEG	=> 'jpeg', 
						gd::PNG		=> 'png', 
						gd::WBMP	=> 'wbmp', 
						gd::gif		=> 'gif', 
						gd::WEBP	=> 'webp', 
						gd::XBM		=> 'xbm', 
						gd::GD		=> 'gd', 
						gd::GD2		=> 'gd2', 
						gd::BMP		=> 'bmp'
						];
			}

			call_user_func([$this, $method_map[$this->type]], ... func_get_args());
		}

		public function jpeg(mixed $location = NULL, int $quality = 75) : void
		{
		}

		public function png(mixed $location = NULL, int $compression = 0, int $filters = self::PNG_NO_FILTERS) : void
		{
		}

		public function wbmp(mixed $location = NULL, int $foreground = NULL) : void
		{
		}

		public function gif(mixed $location) : void
		{
		}

		public function webp(mixed $location) : void
		{
		}

		public function xbm(mixed $location, gdColor | int $foreground = NULL) : void
		{
		}

		public function gd(mixed $location) : void
		{
		}

		public function gd2(mixed $location, int $chunk_size, int $type = self::GD2_RAW) : void
		{
		}

		public function bmp(mixed $location, bool $compression) : void
		{
		}

		/*
		 * Output helper methods aka black magic
		 *
		 * These methods require that a valid type has been hinted at the 
		 * constructor.
		 *
		 * This allow elegant syntaxes such as:
		 *
		 * <code>
		 * $im = gd::createFromPNG('php.png');
		 * 
		 * echo $im;
		 * </code>
		 *
		 * and
		 *
		 * <code>
		 * $im = gd::createFromPNG('pecl.png');
		 *
		 *
		 * $im();
		 * </code>
		 */

		public function __invoke() : void
		{
			if(!$this->type)
			{
				throw new gdException('No output type have been set');
			}

			$this->sendHeader();
			$this->output();
		}

		/* Notice missing sendHeader() call here */

		public function __toString() : string
		{
			ob_start();

			$this->output();

			return(ob_get_clean());
		}

		/* Send header helper method, this applies to __invoke() and output() */

		protected function sendHeader() : void
		{
			if(!$this->sendHeader)
			{
				return;
			}

			static $headers;

			if(!$headers)
			{
				$headers = [
						gd::JPEG 	=> 'image/jpeg',  
						gd::PNG 	=> 'image/png', 
						gd::WBMP 	=> 'image/vnd.wap.wbmp', 
						gd::GIF 	=> 'image/gif', 
						gd::WEBP 	=> 'image/webp', 
						gd::XPM 	=> 'image/xpm', 
						gd::XBM 	=> 'image/xbm', 		/* Maybe one day we will have write support */
						gd::GD 		=> 'application/octet-stream', 	/* Unknown type, so defaults to binary */
						gd::GD2 	=> 'application/octet-stream', 	/* Unknown type, so defaults to binary */
						gd::BMP 	=> 'image/bmp'
						];
			}

			header('Content-Type: ' . $headers[$this->type]);
		}

		/* Palette <> True color convertion methods */

		public function toPalette() : bool
		{
			/* ... */

			$this->trueColor = false;
		}

		public function toTrueColor() : bool
		{
			/* ... */

			$this->trueColor = true;
		}

		/* Gamma correction */

		public function gammaCorrect(float $input, float $output) : bool
		{
		}

		/* Alpha related methods */

		public function alphaBlending(bool $flag) : void
		{
			$this->alphaBlending = $flag;
		}

		public function saveAlpha(bool $flag) : void
		{
			$this->saveAlpha = $flag;
		}

		public function antiAlias(bool $flag) : void
		{
			$this->antiAlias = $flag;
		}

		/* Layer effect */

		public function layerEffect(int $effect)
		{
			static $effects;

			if(!$effects)
			{
				$effects = [
						// EFFECT_NORMAL intentionally missing
						self::EFFECT_REPLACE	=> 1, 
						self::EFFECT_ALPHABLEND	=> 1, 
						self::EFFECT_OVERLAY	=> 1
						];
			}

			if(!isset($effects[$effect]))
			{
				throw new gdException('Invalid effect');
			}

			/* ... */
		}

		/* Flip & Rotation */

		public function rotate(float $angle, gdColor | int $background_color, bool $ignore_transparency = false)
		{
		}

		public function flip($mode)
		{
			static $modes;

			if(!$modes)
			{
				$modes = [
						self::FLIP_HORIZONTAL	=> 1, 
						self::FLIP_VERTICAL	=> 1, 
						self::FLIP_BOTH 	=> 1
						];
			}

			if(!isset($modes[$mode]))
			{
				throw new gdException('Invalid flip mode');
			}

			/* ... */
		}
	}
?>