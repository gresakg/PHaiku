<div class="dropdowns">
		<!-- navigation test -->
		<a class="toggleMenu" href="#">Menu</a>
		<ul class="nav">
<?php foreach($menu as $key=>$item): ?>
			<li><a href="<?php echo $baseurl.$key; ?>"><?php echo $item; ?></a></li>

<?php endforeach; ?>
		</ul>
</div>

