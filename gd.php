<?php
	class gd
	{
		/*
		 * Image type constants
		 *
		 * - Each type have a numeric value of 0x00x
		 * - 0x0Ax means write support
		 * - 0xA0x means read support
		 */

		const JPEG	= 0x001 | 0x0A1 | 0xA01;
		const PNG	= 0x002 | 0x0A2 | 0xA02;
		const WBMP	= 0x003 | 0x0A3 | 0xA03;
		const GIF	= 0x004 | 0x0A4 | 0xA04;
		const WEBP	= 0x005 | 0x0A5 | 0xA05;
		const XPM	= 0x006 | 0xA06;		/* Read-only */
		const XBM	= 0x007 | 0x0A7 | 0xA07;
		const GD 	= 0x008 | 0x0A8 | 0xA08;
		const GD2	= 0x009 | 0x0A9 | 0xA09;


		/* 
		 * Builtin support, this is handled by the extension  
		 * internally, this is determined by gd_info()
		 */
		const SUPPORTS	= self::JPEG | self::PNG | self::WBMP | self::GIF | self::WEBP | self::XPM | self::XBM | self::GD | self::GD2;
	}
?>