<?php
	class gd
	{
		/* Version constants (Types are intended) */
		const VERSION		= '2.2.3';
		const MAJOR_VERSION	= 2;
		const MINOR_VERSION	= 2;
		const RELEASE_VERSION	= 3;
		const EXTRA_VERSION 	= '';
		const BUNDLED		= true;

		/*
		 * Image type constants
		 *
		 * - Each type have a numeric value of 0x00x
		 * - 0x0Ax means write support
		 * - 0xA0x means read support
		 */
		const JPEG_READ		= 0x0A01;
		const JPEG_WRITE 	= 0xA001;
		const PNG_READ		= 0x0A02;
		const PNG_WRITE		= 0xA002;
		const WBMP_READ		= 0x0A03;
		const WBMP_WRITE 	= 0xA003;
		const GIF_READ		= 0x0A04;
		const GIF_WRITE		= 0xA004;
		const WEBP_READ		= 0x0A05;
		const WEBP_WRITE 	= 0xA005;
		const XPM_READ		= 0x0A06;
		const XPM_WRITE		= 0xA006;					/* For consistency */
		const XBM_READ		= 0x0A07;
		const XBM_WRITE		= 0xA007;
		const GD_READ		= 0x0A08;
		const GD_WRITE		= 0xA008;
		const GD2_READ		= 0x0A09;
		const GD2_WRITE		= 0xA009;

		/* Not implemented in bundled libgd yet */
		const BMP_READ		= 0x0AA;
		const BMP_WRITE		= 0xA0A;
		const TGA_READ		= 0x0AB;
		const TGA_WRITE		= 0xA0B;
		const TIFF_READ		= 0x0AC;
		const TIFF_WRITE	= 0xA0C;

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

		/* Not implemented in bundled libgd yet */
		const BMP		= 0x00A | self::BMP_READ | self::BMP_WRITE;
		const TGA 		= 0x00B | self::TGA_READ | self::TGA_WRITE;
		const TIFF 		= 0x00C | self::TIFF_READ | self::TIFF_WRITE;

		/*
		 * FreeType info
		 *
		 * Since the 'INFO' constant is a bitmask, we need to allow the exposure
		 * of the FreeType linkage by a separate constant here
		 */
		const FREETYPE		= 0x00B;
		const FREETYPE_LINKAGE 	= 'with freetype';


		/* 
		 * Builtin support, this is handled by the extension  
		 * internally, this is determined by gd_info()
		 */
		const INFO	= self::JPEG | self::PNG | self::WBMP | self::GIF | self::WEBP | self::XPM | self::XBM | self::GD | self::GD2 | self::TGA | self::TIFF | self::FREETYPE;


		/*
		 * Create methods for various types
		 *
		 * Note, imagecreatefromgd2part is not covered here, and probably 
		 * should not be implemented. I could not find any trace of any 
		 * usage of this function, nor is the gd image format widely used.
		 *
		 * What we could do, could be to implement an abstraction for all 
		 * types to create from a certain part of the image, although this 
		 * is a fairly simple technique in userland code if you decide so.
		 */
		public static function createFromJPEG(string $path) : gdImage
		{
			return(self::createFrom($path, self::JPEG));
		}

		public static function createFromPNG(string $path) : gdImage
		{
			return(self::createFrom($path, self::PNG));
		}

		public static function createFromWBMP(string $path) : gdImage
		{
			return(self::createFrom($path, self::WBMP));
		}

		public static function createFromGIF(string $path) : gdImage
		{
			return(self::createFrom($path, self::GIF));
		}

		public static function createFromWEBP(string $path) : gdImage
		{
			return(self::createFrom($path, self::WEBP));
		}

		public static function createFromXPM(string $path) : gdImage
		{
			return(self::createFrom($path, self::XPM));
		}

		public static function createFromXBM(string $path) : gdImage
		{
			return(self::createFrom($path, self::XBM));
		}

		public static function createFromGD(string $path) : gdImage
		{
			return(self::createFrom($path, self::GD));
		}

		public static function createFromGD2(string $path) : gdImage
		{
			return(self::createFrom($path, self::GD2));
		}

		/* Not implemented in bundled libgd yet */

		public static function createFromBMP(string $path) : gdImage
		{
			return(self::createFrom($path, self::BMP));
		}

		public static function createFromTGA(string $path) : gdImage
		{
			return(self::createFrom($path, self::TGA));
		}

		public static function createFromTIFF(string $path) : gdImage
		{
			return(self::createFrom($path, self::TIFF));
		}

		public static function createFrom(string $path, int $type = 0) : gdImage
		{
			/*
			 * If $type is 0 (or not known), then ext/gd will attempt to guess the 
			 * signature of the type of image based on its signature and fill out 
			 * the value of $type.
			 *
			 * Then an instance of gdImage is returned with the allocated data
			 */
		}


		/*
		 * Windows only!
		 *
		 * Both of these functions are exclusive to ext/gd, and does not 
		 * require a specific version of libgd (be that bundled or external).
		 *
		 * They both return an instance to gdImage
		 */

		public static function grabScreen() : gdImage
		{
		}

		public static function grabWindow(int $handle, int $client_area = 0) : gdImage
		{
		}
	}
?>