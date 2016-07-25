<?php
	class gd
	{
		/*
		 * Image type constants
		 *
		 * - Each type have a numeric value of 0x00X
		 * - 0x0A0 means write support
		 * - 0xA00 means read support
		 */

		const JPEG	= 0x001 | 0x0A0 | 0xA00;
		const PNG	= 0x002 | 0x0A0 | 0xA00;
		const WBMP	= 0x003 | 0x0A0 | 0xA00;
		const GIF	= 0x004 | 0x0A0 | 0xA00;
		const WEBP	= 0x005 | 0x0A0 | 0xA00;
		const XPM	= 0x006 | 0xA00;		/* Read-only */
		const XBM	= 0x007 | 0x0A0 | 0xA00;
		const GD	= 0x008 | 0x0A0 | 0xA00;
		const GD2	= 0x009 | 0x0A0 | 0xA00;


		/* 
		 * Builtin support, this is handled by the extension  
		 * internally, this is determined by gd_info()
		 */
		const SUPPORTS	= self::JPEG | self::PNG | self::WBMP | self::GIF | self::WEBP | self::XPM | self::XBM | self::GD | self::GD2;
	}
?>