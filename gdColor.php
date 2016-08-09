<?php
	class gdColor
	{
		/* Index of the color, readonly */

		public int $index;


		/* Whether or not this is freed from memory, readonly */

		public bool $freed		= false;


		/* Allocates a color */

		public function __construct(int $index)
		{
			/* Indexed color */

			$this->index = $index;
		}

		public function __construct(int $r, int $g, int $b, int $alpha = 0)
		{
			/* RGB w/o alpha */

			/*
			 * RGB w/o alpha
			 *
			 * Converts it into an index and populates $this->index
			 */
		}

		/* Freeing methods */

		public function __destruct()
		{
			$this->freed = true;
		}

		public function free()
		{
			$this->__destruct();
		}

		/* Helper methods */

		public function toIndex()
		{
			return($this->index);
		}

		public function toRGB() : array
		{
			/* Converts $this->index into their RGB values with alpha */

			return([
				'red'	=> XXX, 
				'blue'	=> XXX, 
				'green' => XXX, 
				'alpha' => XXX
				]);
		}

		public function toHex() : int
		{
			/* Converts to a HEX value without alpha */
		}

		public function toHexAlpha() : int
		{
			/* Converts to a HEX value with alpha */
		}

		public function toCMYK() : array
		{
			/* Converts to CMYK color value, returns an array with floats */
		}

		public function toHSL() : array
		{
			/* Converts to HSL color value, returns an array with floats */
		}

		public function toHSV() : array
		{
			/* Converts to HSV color value, returns an array with floats */
		}
	}
?>