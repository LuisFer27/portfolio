<?php
class AifMime{

public static $types= array(
    '323' => 'text/h323',
    '7z' => 'application/x-7z-compressed',
    'abw' => 'application/x-abiword',
    'acx' => 'application/internet-property-stream',
    'ai' => 'application/postscript',
    'aif' => 'audio/x-aiff',
    'aifc' => 'audio/x-aiff',
    'aiff' => 'audio/x-aiff',
    'amf' => 'application/x-amf',
    'asc' => 'text/plain',
    'asf' => 'video/x-ms-asf',
    'asr' => 'video/x-ms-asf',
    'asx' => 'video/x-ms-asf',
    'atom' => 'application/atom+xml',
    'au' => 'audio/basic',
    'avi' => array('video/avi', 'video/msvideo', 'video/x-msvideo'),
    'awk' => 'text/plain',
    'bash' => 'text/plain',
    'bcpio' => 'application/x-bcpio',
    'bin' => array('application/octet-stream','application/macbinary'),
    'bmp' => 'image/bmp',
    'book' => 'application/x-maker',
    'bsh' => 'text/plain',
    'bz2' => 'application/x-bzip2',
    'c' => 'text/x-csrc',
    'c++' => 'text/x-c++src',
    'cab' => 'application/x-cab',
    'cc' => 'text/x-c++src',
    'cda' => 'application/x-cdf',
    'cdf' => 'application/x-netcdf',
    'chrt' => 'application/x-kchart',
    'chm' => 'application/octet-stream',
    'class' => 'application/octet-stream',
    'com' => 'application/x-msdos-program',
    'cpio' => 'application/x-cpio',
    'cpp' => 'text/x-c++src',
    'cpt' => 'application/mac-compactpro',
    'csh' => array('text/x-csh', 'application/x-csh'),
    'css' => 'text/css',
    'csv' => array('text/x-comma-separated-values', 'application/vnd.ms-excel', 'text/comma-separated-values', 'text/csv'),
    'dbk' => 'application/docbook+xml',
    'dcr' => 'application/x-director',
    'deb' => 'application/x-debian-package',
    'diff' => 'text/x-diff',
    'dir' => 'application/x-director',
    'divx' => 'video/divx',
    'djv' => 'image/vnd.djvu',
    'djvu' => 'image/vnd.djvu',
    'dl' => 'video/dl',
    'dll' => array('application/octet-stream', 'application/x-msdos-program'),
    'dmg' => 'application/x-apple-diskimage',
    'dms' => 'application/octet-stream',
    'doc' => 'application/msword',
    'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'dot' => 'application/msword',
    'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
    'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
    'dtd' => 'text/xml',
    'dvi' => 'application/x-dvi',
    'dwg' => array('application/acad', 'application/x-acad', 'application/autocad_dwg', 'image/x-dwg', 'application/dwg', 'application/x-dwg', 'application/x-autocad', 'image/vnd.dwg', 'drawing/dwg'),
    'dxr' => 'application/x-director',
    'eml' => 'message/rfc822',
    'eps' => 'application/postscript',
    'etx' => 'text/x-setext',
    'evy' => 'application/envoy',
    'exe' => array('application/x-msdos-program', 'application/octet-stream'),
    'ez' => 'application/andrew-inset',
    'fb' => 'application/x-maker',
    'fbdoc' => 'application/x-maker',
    'fla' => 'application/octet-stream',
    'flac' => 'application/x-flac',
    'flc' => 'video/flc',
    'fli' => 'video/fli',
    'flv' => 'video/x-flv',
    'fm' => 'application/x-maker',
    'frame' => 'application/x-maker',
    'frm' => 'application/x-maker',
    'gawk' => 'text/plain',
    'gif' => 'image/gif',
    'gl' => 'video/gl',
    'gtar' => 'application/x-gtar',
    'gz' => 'application/x-gzip',
    'h' => 'text/x-chdr',
    'h++' => 'text/x-c++hdr',
    'hdf' => 'application/x-hdf',
    'hh' => 'text/x-c++hdr',
    'hpp' => 'text/x-c++hdr',
    'hqx' => 'application/mac-binhex40',
    'hs' => 'text/x-haskell',
    'htm' => 'text/html',
    'html' => 'text/html',
    'ice' => 'x-conference/x-cooltalk',
    'ico' => 'image/x-icon',
    'ics' => 'text/calendar',
    'ief' => 'image/ief',
    'iges' => 'model/iges',
    'igs' => 'model/iges',
    'iii' => 'application/x-iphone',
    'in' => 'text/plain',
    'ini' => 'text/plain',
    'ins' => 'application/x-internet-signup',
    'iso' => 'application/x-iso9660-image',
    'isp' => 'application/x-internet-signup',
    'jad' => 'text/vnd.sun.j2me.app-descriptor',
    'jar' => 'application/java-archive',
    'java' => 'application/x-java-applet',
    'jnlp' => 'application/x-java-jnlp-file',
    'jpe' => array('image/jpeg', 'image/pjpeg'),
    'jpeg' => array('image/jpeg', 'image/pjpeg'),
    'jpg' => array('image/jpeg', 'image/pjpeg'),
    'js' => 'application/javascript',
    'json' => 'application/json',
    'kar' => 'audio/midi',
    'kil' => 'application/x-killustrator',
    'kpr' => 'application/x-kpresenter',
    'kpt' => 'application/x-kpresenter',
    'ksp' => 'application/x-kspread',
    'kwd' => 'application/x-kword',
    'kwt' => 'application/x-kword',
    'latex' => 'application/x-latex',
    'lha' => 'application/octet-stream',
    'log' => array('text/plain', 'text/x-log'),
    'lzh' => 'application/octet-stream',
    'm3u' => 'audio/x-mpegurl',
    'm4a' => 'audio/mpeg',
    'm4p' => 'video/mp4v-es',
    'm4v' => 'video/mp4',
    'maker' => 'application/x-maker',
    'man' => 'application/x-troff-man',
    'md5' => 'text/plain',
    'mdb' => 'application/x-msaccess',
    'me' => 'application/x-troff-me',
    'mesh' => 'model/mesh',
    'mid' => 'audio/midi',
    'midi' => 'audio/midi',
    'mif' => 'application/vnd.mif',
    'mka' => 'audio/x-matroska',
    'mkv' => 'video/x-matroska',
    'mov' => 'video/quicktime',
    'movie' => 'video/x-sgi-movie',
    'mp2' => 'audio/mpeg',
    'mp3' => 'audio/mpeg',
    'mp4' => array('application/mp4','audio/mp4','video/mp4'),
    'mpa' => 'video/mpeg',
    'mpe' => 'video/mpeg',
    'mpeg' => 'video/mpeg',
    'mpg' => 'video/mpeg',
    'mpg4' => 'video/mp4',
    'mpga' => 'audio/mpeg',
    'mpp' => 'application/vnd.ms-project',
    'mpv' => 'video/x-matroska',
    'mpv2' => 'video/mpeg',
    'ms' => 'application/x-troff-ms',
    'msg' => array('application/msoutlook','application/x-msg'),
    'msh' => 'model/mesh',
    'msi' => 'application/x-msi',
    'mxu' => 'video/vnd.mpegurl',
    'nawk' => 'text/plain',
    'nc' => 'application/x-netcdf',
    'nws' => 'message/rfc822',
    'oda' => 'application/oda',
    'odb' => 'application/vnd.oasis.opendocument.database',
    'odc' => 'application/vnd.oasis.opendocument.chart',
    'odf' => 'application/vnd.oasis.opendocument.forumla',
    'odg' => 'application/vnd.oasis.opendocument.graphics',
    'odi' => 'application/vnd.oasis.opendocument.image',
    'odm' => 'application/vnd.oasis.opendocument.text-master',
    'odp' => 'application/vnd.oasis.opendocument.presentation',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    'odt' => 'application/vnd.oasis.opendocument.text',
    'oga' => 'audio/ogg',
    'ogg' => 'application/ogg',
    'ogv' => 'video/ogg',
    'otg' => 'application/vnd.oasis.opendocument.graphics-template',
    'oth' => 'application/vnd.oasis.opendocument.web',
    'otp' => 'application/vnd.oasis.opendocument.presentation-template',
    'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
    'ott' => 'application/vnd.oasis.opendocument.template',
    'p' => 'text/x-pascal',
    'pac' => 'application/x-ns-proxy-autoconfig',
    'pas' => 'text/x-pascal',
    'patch' => 'text/x-diff',
    'pbm' => 'image/x-portable-bitmap',
    'pdb' => 'chemical/x-pdb',
    'pdf' => array('application/pdf', 'application/x-download'),
    'pgm' => 'image/x-portable-graymap',
    'pgn' => 'application/x-chess-pgn',
    'pgp' => 'application/pgp',
    'php' => 'application/x-httpd-php',
    'php3' => 'application/x-httpd-php',
    'php4' => 'application/x-httpd-php',
    'php5' => 'application/x-httpd-php',
    'phps' => 'application/x-httpd-php-source',
    'pht' => 'application/x-httpd-php',
    'phtml' => 'application/x-httpd-php',
    'pl' => 'text/x-perl',
    'pm' => 'text/x-perl',
    'png' => array('image/png', 'image/x-png'),
    'pnm' => 'image/x-portable-anymap',
    'po' => 'text/x-gettext-translation',
    'pot' => 'application/vnd.ms-powerpoint',
    'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
    'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
    'ppa' => 'application/vnd.ms-powerpoint',
    'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
    'ppm' => 'image/x-portable-pixmap',
    'pps' => 'application/vnd.ms-powerpoint',
    'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
    'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
    'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
    'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'prc' => 'application/x-pilot',
    'ps' => 'application/postscript',
    'psd' => array('application/x-photoshop', 'image/x-photoshop'),
    'pub' => 'application/x-mspublisher',
    'py' => 'text/x-python',
    'qdf' => 'application/octet-stream',
    'qt' => 'video/quicktime',
    'ra' => 'audio/x-realaudio',
    'ram' => array('audio/x-realaudio', 'audio/x-pn-realaudio'),
    'rar' => 'application/rar',
    'ras' => 'image/x-cmu-raster',
    'rdf' => 'application/rdf+xml',
    'rgb' => 'image/x-rgb',
    'rm' => 'audio/x-pn-realaudio',
    'roff' => 'application/x-troff',
    'rpm' => array('audio/x-pn-realaudio-plugin', 'application/x-redhat-package-manager'),
    'rss' => 'application/rss+xml',
    'rtf' => 'text/rtf',
    'rtx' => 'text/richtext',
    'rv' => 'video/vnd.rn-realvideo',
    'sb'=> 'application/acad',
    'sb2'=>'application/x-scratch2',
    'sda' => 'application/vnd.stardivision.draw',
    'sdc' => 'application/vnd.stardivision.calc',
    'sdd' => 'application/vnd.stardivision.impress',
    'sdw' => 'application/vnd.stardivision.writer',
    'sea' => 'application/octet-stream',
    'sgl' => 'application/vnd.stardivision.writer-global',
    'sgm' => 'text/sgml',
    'sgml' => 'text/sgml',
    'sh' => 'text/x-sh',
    'sh1' => 'text/plain',
    'sha' => 'text/plain',
    'shar' => 'application/x-shar',
    'shtml' => 'text/html',
    'silo' => 'model/mesh',
    'sis' => 'application/vnd.symbian.install',
    'sit' => 'application/x-stuffit',
    'skd' => 'application/x-koan',
    'skm' => 'application/x-koan',
    'skp' => 'application/x-koan',
    'skt' => 'application/x-koan',
    'smf' => 'application/vnd.stardivision.math',
    'smi' => 'application/smil',
    'smil' => 'application/smil',
    'snd' => 'audio/basic',
    'so' => 'application/octet-stream',
    'spl' => 'application/x-futuresplash',
    'src' => 'application/x-wais-source',
    'stc' => 'application/vnd.sun.xml.calc.template',
    'std' => 'application/vnd.sun.xml.draw.template',
    'sti' => 'application/vnd.sun.xml.impress.template',
    'stw' => 'application/vnd.sun.xml.writer.template',
    'sv4cpio' => 'application/x-sv4cpio',
    'sv4crc' => 'application/x-sv4crc',
    'svg' => 'image/svg+xml',
    'swf' => 'application/x-shockwave-flash',
    'sxc' => 'application/vnd.sun.xml.calc',
    'sxd' => 'application/vnd.sun.xml.draw',
    'sxg' => 'application/vnd.sun.xml.writer.global',
    'sxi' => 'application/vnd.sun.xml.impress',
    'sxm' => 'application/vnd.sun.xml.math',
    'sxw' => 'application/vnd.sun.xml.writer',
    't' => 'application/x-troff',
    'tar' => 'application/x-tar',
    'tcl' => 'text/x-tcl',
    'tex' => 'application/x-tex',
    'texi' => 'application/x-texinfo',
    'texinfo=' => 'application/x-texinfo',
    'text' => 'text/plain',
    'texti' => 'application/x-texinfo',
    'textinfo' => 'application/x-texinfo',
    'tgz' => array('application/x-tar', 'application/x-gzip'),
    'tif' => 'image/tiff',
    'tiff' => 'image/tiff',
    'torrent' => 'application/x-bittorrent',
    'tr' => 'application/x-troff',
    'tsv' => 'text/tab-separated-values',
    'txt' => 'text/plain',
    'ustar' => 'application/x-ustar',
    'vcd' => 'application/x-cdlink',
    'vcf' => 'text/x-vCard',
    'vcs' => 'text/x-vCalendar',
    'vrml' => 'model/vrml',
    'wav' => 'audio/x-wav',
    'wax' => 'audio/x-ms-wax',
    'wbmp' => 'image/vnd.wap.wbmp',
    'wbxml' => array('application/wbxml', 'application/vnd.wap.wbxml'),
    'webm' => 'video/webm',
    'wk' => 'application/x-123',
    'wm' => 'video/x-ms-wm',
    'wma' => 'audio/x-ms-wma',
    'wmd' => 'application/x-ms-wmd',
    'wml' => 'text/vnd.wap.wml',
    'wmlc' => array('application/wmlc', 'application/vnd.wap.wmlc'),
    'wmls' => 'text/vnd.wap.wmlscript',
    'wmlsc' => 'application/vnd.wap.wmlscriptc',
    'wmv' => array('video/x-ms-wmv', 'application/octet-stream'),
    'wmx' => 'video/x-ms-wmx',
    'wmz' => 'application/x-ms-wmz',
    'word' => array('application/msword', 'application/octet-stream'),
    'wp5' => 'application/wordperfect5.1',
    'wpd' => 'application/vnd.wordperfect',
    'wrd' => 'application/msword',
    'wrl' => 'model/vrml',
    'wvx' => 'video/x-ms-wvx',
    'wz' => 'application/x-Wingz',
    'xbm' => 'image/x-xbitmap',
    'xcf' => 'image/xcf',
    'xht' => 'application/xhtml+xml',
    'xhtml' => 'application/xhtml+xml',
    'xl' => array('application/excel', 'application/vnd.ms-excel'),
    'xla' => array('application/excel', 'application/vnd.ms-excel'),
    'xlc' => array('application/excel', 'application/vnd.ms-excel'),
    'xlm' => array('application/excel', 'application/vnd.ms-excel'),
    'xls' => array('application/excel', 'application/vnd.ms-excel'),
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xlt' => array('application/excel', 'application/vnd.ms-excel'),
    'xml' => array('text/xml', 'application/xml'),
    'xmlrpc' => 'application/xml',
    'xrdf' => 'application/xrds+xml',
    'xof' => 'x-world/x-vrml',
    'xpm' => 'image/x-xpixmap',
    'xsl' => 'text/xml',
    'xvid' => 'video/x-xvid',
    'xwd' => 'image/x-xwindowdump',
    'z' => 'application/x-compress',
    'zip' => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
    'default' => ''
    );


  public static function getByExt($ex,$idx=0){
    $ex = strtolower($ex);
    $ty = isset(self::$types[$ex])?self::$types[$ex]:self::$types['default'];
    return is_array($ty)?(isset($ty[$idx])?$ty[$idx]:$ty[0]):$ty;
  }

  public static function getByFile($file,$idx=0){
     $pp = pathinfo($file);
     return self::getByExt($pp['extension'],$idx);
  }

}
