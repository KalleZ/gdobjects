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

		const JPEG_READ		= 0x0A01;
		const JPEG_WRITE	= 0xA001;
		const PNG_READ		= 0x0A02;
		const PNG_WRITE		= 0xA002;
		const WBMP_READ		= 0x0A03;
		const WBMP_WRITE	= 0xA003;
		const GIF_READ		= 0x0A04;
		const GIF_WRITE		= 0xA004;
		const WEBP_READ		= 0x0A05;
		const WEBP_WRITE	= 0xA005;
		const XPM_READ		= 0x0A06;
		const XPM_WRITE		= 0xA006;					/* For consistency */
		const XBM_READ		= 0x0A07;
		const XBM_WRITE		= 0xA007;
		const GD_READ		= 0x0A08;
		const GD_WRITE		= 0xA008;
		const GD2_READ		= 0x0A09;
		const GD2_WRITE		= 0xA009;

		/* These are determined internally at extension load */
		const JPEG		= 0x001 | self::JPEG_READ | self::JPEG_WRITE;
		const PNG		= 0x002 | self::PNG_READ | self::PNG_WRITE;
		const WBMP		= 0x003 | self::WBMP_READ | self::WBMP_WRITE;
		const GIF		= 0x004 | self::GIF_READ | self::GIF_WRITE;
		const WEBP		= 0x005 | self::WEBP_READ | self::WEBP_WRITE;
		const XPM		= 0x006 | self::XPM_READ;			/* Read-only */
		const XBM		= 0x007 | self::XBM_READ | self::XBM_WRITE;
		const GD 		= 0x008 | self::GD_READ | self::GD_WRITE;
		const GD2		= 0x009 | self::GD2_READ | self::GD2_WRITE;


		/* 
		 * Builtin support, this is handled by the extension  
		 * internally, this is determined by gd_info()
		 */
		const SUPPORTS	= self::JPEG | self::PNG | self::WBMP | self::GIF | self::WEBP | self::XPM | self::XBM | self::GD | self::GD2;
	}
?>