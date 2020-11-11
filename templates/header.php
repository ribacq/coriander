<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= $page['title'] ?></title>
		<style type="text/css">
		body {
			margin: 40px auto;
			max-width: 650px;
			line-height: 1.6;
			font-family: sans-serif;
			font-size: 18px;
			color: #444; 
			padding:0 10px;
		}
		h1, h2, h3 {
			line-height:1.2;
		}
		</style>
	</head>
	<body>
		<header>
			<h1>Coriander</h1>
			<nav>
				<ul>
				<?php foreach($pages as $navpage): if ($navpage['inNav']): ?>
					<li><a href="<?= $navpage['sitePath'] ?>"><?= $navpage['title'] ?></a></li>
				<?php endif; endforeach; ?>
				</ul>
			</nav>
		</header>
		<aside>
			<p>
				<?= $page['title'] ?>, <time><?= $page['date'] ?></time>
			</p>
		</aside>
		<section>
