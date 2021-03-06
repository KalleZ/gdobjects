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

		/* Filters */

		const FILTER_NEGATE		= 1;
		const FILTER_GRAYSCALE		= 2;
		const FILTER_BRIGHTNESS		= 3;
		const FILTER_CONTRAST		= 4;
		const FILTER_COLORIZE		= 5;
		const FILTER_EDGEDETECT		= 6;
		const FILTER_GAUSSIAN_BLUR	= 7;
		const FILTER_SELECTIVE_BLUR	= 8;
		const FILTER_EMBOSS		= 9;
		const FILTER_MEAN_REMOVAL	= 10;
		const FILTER_SMOOTH		= 11;
		const FILTER_PIXELATE		= 12;
		const FILTER_SCATTER		= 13;

		/* Blur modes */

		const BLUR_DEFAULT		= 2;	/* BLUR_SELECTIVE */
		const BLUR_GAUSSIAN		= 1;
		const BLUR_SELECTIVE		= 2;

		/* Crop modes */

		const CROP_DEFAULT		= self::CROP_TRANSPARENT | self::CROP_SIDES;
		const CROP_TRANSPARENT		= 1;
		const CROP_BLACK		= 2;
		const CROP_WHITE		= 4;
		const CROP_SIDES		= 8;
		const CROP_THRESHOLD		= 16;

		/* Affine constants */

		const AFFINE_TRANSLATE 		= 1;
		const AFFINE_SCALE 		= 2;
		const AFFINE_ROTATE 		= 3;
		const AFFINE_SHEAR_HORIZONTAL 	= 4;
		const AFFINE_SHEAR_VERTICAL 	= 5;

		/* Interpolation constants */

		const BELL 			= 1;
		const BESSEL 			= 2;
		const BILINEAR_FIXED 		= 3;
		const BICUBIC 			= 4;
		const BICUBIC_FIXED 		= 5;
		const BLACKMAN 			= 6;
		const BOX 			= 7;
		const BSPLINE 			= 8;
		const CATMULLROM 		= 9;
		const GAUSSIAN 			= 10;
		const GENERALIZED_CUBIC 	= 11;
		const HERMITE 			= 12;
		const HAMMING 			= 13;
		const HANNING 			= 14;
		const HITCHELL 			= 15;
		const POWER 			= 16;
		const QUADRATIC 		= 17;
		const SINC 			= 18;
		const NEAREST_HEIGHBOUR 	= 19;
		const WEIGHTED4 		= 20;
		const TRIANGLE  		= 21;

		/* Style constants */

		const COLOR_TILED 		= 1;
		const COLOR_STYLED 		= 2;
		const COLOR_BRUSHED 		= 3;
		const COLOR_STYLEBRUSHED 	= 4;
		const COLOR_TRANSPARENT  	= 5;

		/* Filled arc constants */

		const ARC_ROUNDED		= 1;
		const ARC_PIE			= 1; /* Same as ARC_ROUNDED */
		const ARC_CHORD			= 2;
		const ARC_NOFILL		= 3;
		const ARC_EDGED			= 4;


		/* Image sizes, readonly */

		public int $x		= 0;
		public int $y		= 0;


		/*
		 * Meta information
		 *
		 * Some of these properties are overloaded, and have special 
		 * behavior if changed
		 */

		public bool $trueColor 		= false;	/* Calls ->toPalette() or ->toTrueColor() depending on the new value */
		public bool $alphaBlending	= false;	/* Only changable for true color images */
		public bool $saveAlpha		= false;	/* Save alpha flag, only works if $alphaBlending is off */
		public bool $antiAlias		= false;	/* For true color only */
		public bool $interlace		= false;	/* Used for progressive JPEGs */

		public bool $freed		= false;	/* [readonly] is ->destroy() called? This should be checked on all method calls */
		public int $type		= 0;		/* [readonly] gd::XXX type constants */
		public bool $sendHeader		= false;	/* [readonly] Auto send header for outputs, defaults to false */


		/* Animation property */

		public gdAnimation $animation 	= NULL;		/* May be overriden manually or by gdImage::animate() */


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

		/* __clone support */
		public function __clone()
		{
			/* Copy palette if palette etc */
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

						// Not implemented in ext/gd yet
						gd::BMP 	=> gd::BMP_WRITE, 
						gd::TGA 	=> gd::TGA_WRITE, 
						gd::TIFF 	=> gd::TIFF_WRITE
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

						// Not implemented in ext/gd yet
						gd::BMP		=> 'bmp', 
						gd::TGA 	=> 'tga', 
						gd::TIFF 	=> 'tiff'
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

						// Not implemented in ext/gd yet
						gd::BMP 	=> 'image/bmp', 
						gd::TGA 	=> 'image/tga', 
						gd::TIFF 	=> 'image/tiff'
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

		/* Palette copy */

		public function copyPalette(gdImage $im)
		{
			if(!$this->trueColor || !$im->trueColor)
			{
				throw new gdException('Both the destination and source images must be palette based');
			}
		}

		/* Interlace */

		public function interlace(bool $flag) : void
		{
			$this->interlace = $flag;
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

		public function layerEffect(int $effect) : bool
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

		public function rotate(float $angle, gdColor | int $background_color, bool $ignore_transparency = false) : bool
		{
		}

		public function flip($mode) : bool
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

		/* Filters */

		public function filter(int $filter, ... $args) : bool
		{
			static $filter_callbacks;

			if(!$filter_callbacks)
			{
				$filter_callbacks = [
							self::FILTER_NEGATE 		=> 'negate', 
							self::FILTER_GRAYSCALE 		=> 'grayscale', 
							self::FILTER_BRIGHTNESS		=> 'brightness', 
							self::FILTER_CONTRAST		=> 'contrast', 
							self::FILTER_COLORIZE		=> 'colorize', 
							self::FILTER_EDGEDETECT		=> 'edgeDetect', 
							self::FILTER_GAUSSIAN_BLUR	=> 'gaussianBlur', 
							self::FILTER_SELECTIVE_BLUR	=> 'blur', 
							self::FILTER_EMBOSS		=> 'emboss', 
							self::FILTER_MEAN_REMOVAL 	=> 'meanRemoval', 
							self::FILTER_SMOOTH		=> 'smooth', 
							self::FILTER_PIXELATE 		=> 'pixelate', 
							self::FILTER_SCATTER 		=> 'scatter'
							];
			}

			if(!isset($filter_callbacks[$filter]))
			{
				throw new gdException('Invalid filter');
			}

			return(call_user_func_array([$this, $filter_callbacks[$filter]], $args));
		}

		public function negate() : bool
		{
		}

		public function grayscale() : bool
		{
		}

		public function brightness(int $level) : bool
		{
		}

		public function contrast(int $level) : bool
		{
		}

		public function colorize(int $red, int $blue, int $blue, int $alpha) : bool
		{
		}

		public function edgeDetect() : bool
		{
		}

		public function gaussianBlur() : bool
		{
		}

		public function selectiveBlur() : bool
		{
		}

		public function emboss() : bool
		{
		}

		public function meanRemoval() : bool
		{
		}

		public function smooth(int $level) : bool
		{
		}

		public function pixelate(int $block_size, bool $advanced = false) : bool
		{
		}

		public function scatter(int $sub, int $plus, array $colors) : bool
		{
		}

		public function blur(int $blur = self::BLUR_DEFAULT) : bool
		{
			if($blur == self::BLUR_GAUSSIAN)
			{
				return($this->gaussianBlur());
			}
			elseif($blur == self:BLUR_SELECTIVE)
			{
				return($this->selectiveBlur());
			}

			throw new gdException('Invalid blur mode');
		}

		public convolution(array $matrix, float $fiv, float $offset)
		{
		}

		/* Setters */

		public function setThickness(int $thickness) : bool
		{
		}

		public function setTile(gdImage $im) : bool
		{
		}

		public function setBrush(gdImage $im) : bool
		{
		}

		public function setPixel(int $x, int $y, gdColor | int $color) : bool
		{
		}

		public function setInterpolation(int $method) : bool
		{
		}

		public function setStyle(array $style) : bool
		{
		}

		/* Basic fonts */

		public function getFontHeight(int $font) : int
		{
		}

		public function getFontWidth(int $font) : int
		{
		}

		public function getFontInfo(int $font) : array
		{
			return([
				'height'	=> $this->getFontHeight($font), 
				'width'		=> $this->getFontWidth($font)
				]);
		}

		public function loadFont(string $font) : int
		{
		}

		/* Cropping & scaling */

		public function crop(int $x, int $y, int $width, int $height) : bool
		{
		}

		public function cropAuto(int $mode, float $threshold = .5, gdColor | int $color) : bool
		{
		}

		public function scale(int $new_width, int $new_height = -1, int $mode = self::BILINEAR_FIXED) : bool
		{
		}

		/* Bounding box and text writing */

		public static function bbox(float $size, float $angle, string $font, string $text, array $extra_info = NULL) : array
		{
			// Decide internally if font is TTF or FT and use the respectively bbox method
		}

		public static function ftBbox(float $size, float $angle, string $font, string $text, array $extra_info = NULL) : array
		{
		}

		public static function ttfBbox(float $size, float $angle, string $font, string $text) : array
		{
		}

		public function text(float $size, float $angle, int $x, int $y, gdColor | int $color, string $font, string $text, array $extra_info = NULL) : array
		{
			// Decide internally if font is TTF or FT and use the respectively text method
		}

		public function ftText(float $size, float $angle, int $x, int $y, gdColor | int $color, string $font, string $text, array $extra_info = NULL) : array
		{
		}

		public function ttfText(float $size, float $angle, int $x, int $y, gdColor | int $color, string $font, string $text) : array
		{
		}

		/* Drawing */

		public function line(int $x1, int $y1, int $x2, int $y2 : gdColor | int $color) : bool
		{
		}

		public function arc(int $cx, int $cy, int $width, int $height, int $start, int $end, gdColor | int $color) : bool
		{
		}

		public function ellipse(int $cx, int $cy, int $width, int $height, gdColor | int $color) : bool
		{
		}

		public function polygon(array $points, gdColor | int $color) : bool
		{
			/* Note, $num_points is intentionally missing, sizeof($points) should be sufficient */
		}

		public function rectangle(int $x1, int $y1, int $x2, int $y2, gdColor | int $color)
		{
		}

		/* Filled drawing methods */

		public function fill(int $x, int $y, gdColor | int $color) : bool
		{
		}

		public function fillToBorder(int $x, int $y, gdColor | int $border_color, gdColor | int $inner_color) : bool
		{
		}

		public function filledArc(int $cx, int $cy, int $width, int $height, int $start, int $end, gdColor | int $color, int $style) : bool
		{
		}

		public function filledEllipse(int $cx, int $cy, int $width, int $height, gdColor | int $color) : bool
		{
		}

		public function filledPolygon(array $points, gdColor | int $color) : bool
		{
			/* Note, $num_points is intentionally missing, sizeof($points) should be sufficient */
		}

		public function filledRectangle(int $x1, int $y1, int $x2, int $y2, gdColor | int $color) : bool
		{
		}

		/* Affine methods */

		public function affine(array $affine, int $clip_x = NULL, int $clip_y = NULL, int $clip_width = NULL, int $clip_height = NULL) : bool
		{
		}

		public static function affineMatrixConcat(array $matrix1, array $matrix2) : array
		{
		}

		public static function affineMatrixGet(int $type, array $options) : array
		{
		}

		/* Basic font writing (non FreeType) methods */

		public function char(int $font, int $x, int $y, string $char, gdColor | int $color) : bool
		{
			return($this->string($font, $x, $y, $char{0}, $color));
		}

		public function charUp(int $font, int $x, int $y, string $char, gdColor | int $color) : bool
		{
			return($this->stringUp($font, $x, $y, $char{0}, $color));
		}

		public function string(int $font, int $x, int $y, string $string, gdColor | int $color) : bool
		{
		}

		public function stringUp(int $font, int $x, int $y, string $string, gdColor | int $color) : bool
		{
		}

		/* Animation, only one animation object can be attached per gdImage */

		public function animate(gdAnimation $animation)
		{
			/* 
			 * This could be extended to APNG in the future, although this approach 
			 * might not work if no type is defined, so this should potentially be in 
			 * the output methods
			 */
			if($this->type != gd::TYPE_GIF)
			{
				throw new gdException('Only animated GIFs are supported');
			}

			/* 
			 * $prev_im from each frame is generated here
			 * 
			 * Each $frame->im needs to be converted to a matching type with 
			 * that of the output, meaning there can be color loss if palette <> true color
			 * images are mixed
			 */

			$this->animation = $animation;
		}

		/* Clipping methods */

		public function setClip($x1, $y1, $x2, $y2) : bool
		{
		}

		public function getClip() : array
		{
		}

		/* Color methods */

		public function colorAt(int $x, int $y) : gdColor
		{
		}

		public function colorClosest(int $r, int $g, int $b) : gdColor
		{
		}

		public function colorClosestAlpha(int $r, int $g, int $b, int $a) : gdColor
		{
		}

		public function colorClosestHWB(int $r, int $g, int $b) : gdColor
		{
		}

		public function getTotalColors() : int
		{
		}

		public function setTransparentColor(gdColor | int $color = -1) : gdColor | int
		{
			/* May return -1 if there is no transprent pixels, otherwise a gdColor object */
		}

		public function colorSet(gdColor | int $index, int $r, int $g, int $b, int $a = 0) : bool
		{
			/* Variant that is compatible with imagecolorset() */
		}

		public function colorSet(gdColor | int $index, gdColor | int $new_index) : bool
		{
			/* Variant that re-uses gdColor for its second argument */
		}

		public function colorExtract(int $r, int $g, int $b) : gdColor
		{
		}

		public function colorExtractAlpha(int $r, int $g, int $b, int $alpha) : gdColor
		{
		}

		public function colorMatch(gdImage $src_im) : bool
		{
		}

		public function colorResolve(int $r, int $g, int $b) : gdColor
		{
		}

		public function colorResolveAlpha(int $r, int $g, int $b, int $alpha) : gdColor
		{
		}

		/* Copying & Merging */

		public function copy(gdImage $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height) : bool
		{
		}

		public function copyMerge(gdImage $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height, int $pct) : bool
		{
		}

		public function copyMergeGray(gdImage $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height, int $pct) : bool
		{
		}

		public function copyResampled(gdImage $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_width, int $dst_height, int $src_width, int $src_height) : bool
		{
		}

		public function copyResized(gdImage $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_width, int $dst_height, int $src_width, int $src_height) : bool
		{
		}
	}
?>