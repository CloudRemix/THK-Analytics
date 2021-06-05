<?php
/**
 * THK Analytics - free/libre analytics platform
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @link http://thk.kanzae.net/analytics/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 *
 * This program has been developed on the basis of the Research Artisan Lite.
 */

/**
 * Research Artisan Lite: Website Access Analyzer
 * Copyright (C) 2009 Research Artisan Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * @copyright Copyright (C) 2009 Research Artisan Project
 * @license GNU General Public License (see license.txt)
 * @author ossi
 */

//header('Content-Type: text/html; charset='. ThkConfig::CHARSET);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo ThkConfig::CHARSET;?>" />
<title>Exception caught</title>
<style>
  body { background-color: #fff; color: #333; }
  body, p, ol, ul, td {
    font-family: verdana, arial, helvetica, sans-serif;
    font-size:   13px;
    line-height: 18px;
  }
  pre {
    background-color: #eee;
    padding: 10px;
    font-size: 11px;
  }
  a { color: #000; }
  a:visited { color: #666; }
  a:hover { color: #fff; background-color:#000; }
</style>
</head>
<body>
<h1><?php echo $exception->getMessage(); ?></h1>
<p>File: <?php echo $exception->getAppErrorFile() !== null ? $exception->getAppErrorFile() : $exception->getFile(); ?>
 (Line: <?php echo $exception->getAppErrorLine() !== null ? $exception->getAppErrorLine() : $exception->getLine(); ?>)</p>
<p>Code: <?php echo $exception->getCode(); ?></p>
<p></p>
<p><pre>
<?php echo $exception->getTraceAsString(); ?>
</pre></p>
</body>
</html>
