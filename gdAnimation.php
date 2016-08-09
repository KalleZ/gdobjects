<?php
	class gdAnimation extends SplQueue
	{
		/* Disposal constants for gdAnimationFrame */

		const DISPOSAL_UNKNOWN			= 0;
		const DISPOSAL_NONE			= 1;
		const DISPOSAL_RESTORE_BACKGROUND	= 2;
		const DISPOSAL_RESTORE_PREVIOUS		= 3;


		/* Readonly properties */

		public int $globalCm			= 1;
		public int $loops			= -1; /* Infinite */


		/* Methods and overrides for SplQueue */

		public function __construct(int $global_cm = 1, int $loops = -1)
		{
		}

		public function enqueue(gdAnimationFrame $frame) : void
		{
		}
	}
?>