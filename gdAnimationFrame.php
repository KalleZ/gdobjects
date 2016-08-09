<?php
	class gdAnimationFrame
	{
		/* Properties from the constructor */

		public gdImage $im;

		/* These are readonly */

		public int $localCm;
		public int $x;
		public int $y;
		public int $disposal; 


		/* Frame animation constructor */

		public function __construct(gdImage $im, int $local_cm, int $x, int $y, int $disposal = gdAnimation::DISPOSAL_NONE)
		{
			$this->im	= $im;
			$this->local_cm = $local_cm;
			$this->x	= $x;
			$this->y	= $y;
			$this->disposal = $disposal;
		}
	}
?>