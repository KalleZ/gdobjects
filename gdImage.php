<?php
	class gdImage
	{
		/* Image sizes, readonly */

		public $x		= 0;
		public $y		= 0;


		/* Meta information, readonly */

		public $trueColor 	= false;
		public $type		= 0;		/* gd::XXX type constants */
		public $sendHeader	= false;	/* Auto send header for outputs, defaults to false */


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
	}
?>