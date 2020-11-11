<?php
/**
 * coriander static website generator
 * @author Quentin RIBAC
 * @since 2020-11-10
 * @license WTFPL
 */

// dependencies
require 'vendor/autoload.php';
use Michelf\Markdown;

// config
$sitebasedir = rtrim(getcwd(), '/').'/';
$datadir = './data/';
$outdir = './out/';

// header and footer functions
function template($name, $pages, $pageI) {
	$templatesdir = './templates/';
	$page = $pages[$pageI];
	ob_start();
	include rtrim($templatesdir, '/').'/'.$name.'.php';
	return ob_get_clean();
}

// load filenames list
$filenames = [];
exec('tree -fin --noreport '.$datadir, $filenames);

// reading
echo 'reading metadata'.PHP_EOL;
$pages = [];
foreach ($filenames as $filename) {
	if (is_dir($filename)) {
		echo "> $filename ... ";
		$filename = str_replace($datadir, $outdir, $filename);
		if (!is_dir($filename)) {
			mkdir($filename, 0775, true);
		}
		echo 'OK'.PHP_EOL;
	} else if (is_file($filename)) {
		echo "> $filename ... ";
		$newfilename = str_replace($datadir, $outdir, $filename);
		$filenameparts = explode('.', basename($filename));
		$base = $filenameparts[0];
		unset($filenameparts[0]);
		$tail = array_pop($filenameparts);

		$pageTitle = null;
		$pageDate = date('Y-m-d', filemtime($filename));
		if ($tail === 'md') {
			$newfilename = dirname($newfilename).'/'.basename($newfilename, '.md').'.html';
			$contents = file_get_contents($filename);
			$pageTitle = [];
			preg_match('/^# (.+)$/m', $contents, $pageTitle);
			$pageTitle = $pageTitle[1];
			$inNav = preg_match('/^#!nav$/m', $contents) === 1;
			unset($contents);

			if ($inNav) {
				echo '*';
			}
			echo "[$pageDate] $pageTitle ";

			$pages []= [
				'mdPath' => $filename,
				'htmlPath' => $newfilename,
				'sitePath' => $sitebasedir.$newfilename,
				'inNav' => $inNav,
				'title' => $pageTitle,
				'date' => $pageDate,
			];
		} else {
			copy($filename, $newfilename);
		}

		echo 'OK'.PHP_EOL;
	}
}

// converting
echo 'converting pages'.PHP_EOL;
foreach ($pages as $pageI => $page) {
	echo '> '.$page['htmlPath'].' ... ';
	$outfile = fopen($page['htmlPath'], 'w');
	if (!$outfile) {
		die('[error] could not open file '.$page['htmlPath']);
	}
	if (!fwrite($outfile, template('header', $pages, $pageI))) {
		die('[error] could not write htmlHeader into '.$page['htmlPath']);
	}
	$contents = file_get_contents($page['mdPath']);
	$contents = preg_replace('/^#!nav$/m', '', $contents);
	$contents = Markdown::defaultTransform($contents);
	if (!fwrite($outfile, $contents)) {
		die('[error] could not write converted markdown contents into '.$page['htmlPath']);
	}
	unset($contents);
	if (!fwrite($outfile, template('footer', $pages, $pageI))) {
		die('[error] could not write htmlFooter into '.$page['htmlPath']);
	}
	if (!fclose($outfile)) {
		die('[error] could not close file '.$page['htmlPath']);
	}
	echo 'OK'.PHP_EOL;
}
