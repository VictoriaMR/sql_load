<?php include(APP_PATH.'html/header.php'); ?>
<div class="container">
	<?php if (!empty($siteList)) { ?>
	<div class="header">
		<ul class="header-type-list">
			<?php foreach ($siteList as $key => $value) { ?>
				<li class="site-type <?php if($value == $type){ echo 'selected'; } ?>" >
					<a href="./index.php?type=<?php echo $value;?>"><?php echo $value;?></a></li>
			<?php } ?>
				<li class="del-li">
					<a href="./index.php?type=<?php echo $type;?>&action=del">删除</a>
				</li>
		</ul>
	</div>
	<div class="clear"></div>
	<?php } ?>
	<div class="content">
		<?php foreach ($data as $key => $value) {?>
			<?php echo $value;?>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
$('.query-content').on('click', function(e){
	//实例化复制对象
})

var titleTop = $('.content').offset().top;

//监听页面滚动
$(window).scroll(function(event){
	var scrollTop = $(window).scrollTop();
	if (scrollTop > titleTop) {
		$('.header').addClass('fixed');
		$('.content').css({'margin-top': titleTop});
	} else {
		$('.header').removeClass('fixed');
		$('.content').css({'margin-top': 0});
	}
});

</script>
<?php include(APP_PATH.'html/footer.php'); ?>