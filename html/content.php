<?php include(APP_PATH.'html/header.php'); ?>
<div class="container">
	<?php if (!empty($siteList)) { ?>
	<div class="header">
		<ul class="header-type-list">
			<?php foreach ($siteList as $key => $value) { ?>
				<li class="<?php if($value == $type){ echo 'selected'; } ?>" >
					<a href="./index.php?type=<?php echo $value;?>" title="refresh"><?php echo $value;?></a></li>
			<?php } ?>
				<li class="del-li">
					<a href="./index.php?type=<?php echo $type;?>&action=del" title="delete">Delete</a>
				</li>
		</ul>
	</div>
	<div class="clear"></div>
	<?php } ?>
	<div class="content" page="<?php echo $_GET['page'] ?? 1;?>" size="<?php echo $_GET['size'] ?? 200;?>" total="<?php echo $total ?? 0;?>">
		<?php foreach ($data as $key => $value) {?>
			<?php echo $value;?>
		<?php } ?>
	</div>
	<div class="loading hidden center">
		<img class="rotate" src="/html/img/loading.png">
	</div>
	<div class="no-content hidden center">
		<span class="orange">我是有底线的</span>
	</div>

</div>
<script type="text/javascript">
//点击复制
function copy()
{
	$('.query-content').unbind('click').bind('click', function(){
		var text = document.getElementById($(this).attr('attr'));
		var selection = window.getSelection();
		var range = document.createRange();
		range.selectNodeContents(text);
		selection.removeAllRanges();
		selection.addRange(range);
		document.execCommand("Copy");
	});
}

copy();

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

	if ($(document).scrollTop() > 0 && ($(document).scrollTop() + $(window).height() + 100 >= $(document).height())) {
		if (!$('.no-content').is(':visible') && !$('.loading').is(':visible')) {
           getNextPage(); //获取分页
		}
    }
});

//滚动分页
function getNextPage()
{
	if ($('.no-content').is(':visible')) return false;

	var page = $('.content').attr('page');
	var size = $('.content').attr('size');
	var total = $('.content').attr('total');

	if ((page - 1) * size >= total) {
		$('.no-content').show();
		return false;
	}
	$('.loading').show();
	$.get('./index.php?type=<?php echo $type;?>', {page: (parseInt(page)+1), size: size, is_ajax: 1}, function(res){
		$('.loading').hide();
		$('.content').attr('page', parseInt(page)+1);
		$('.content').attr('total', res.total);

		var html = '';
		for (var i in res.list) {
			html += res.list[i];
		}
		$('.content').append(html);
		copy();
	}, 'json');
}

</script>
<?php include(APP_PATH.'html/footer.php'); ?>