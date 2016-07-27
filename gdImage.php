<?php
	class gdImage
	{
		/* Image sizes, readonly */

		public $x		= 0;
		public $y		= 0;


		/* Meta information, readonly */

		public $trueColor	= false;


		/*
		 * Constructor for userland usage
		 *
		 * For internal use, this method may be omitted by the 
		 * gd::createFrom[*]() methods.
		 *
		 * This works for creating palette based images or true color 
		 * based images.
		 */
		public function __construct(int $x, int $y, bool $true_color = false)
		{
			if($x < 1 || $y < 1)
			{
				throw new gdException('Invalid image sizes');
			}

			$this->x 		= $x;
			$this->y 		= $y;
			$this->trueColor	= $true_color;
		}
	}
?>