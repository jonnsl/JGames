<?php
// Load the modal behavior script.
JHtml::_('script','system/modal.js', true, true);
JHtml::_('stylesheet','system/modal.css', array(), true);
JHtml::script('games/boxart.js', true, true, false, false);
JHtml::script('games/Fx.Scroll.Carousel.js', true, true, false, false);

?>
<img src="../media/games/images/button_prev.png" alt="Previous" id="previous"/>
	<div id="boxarts" class="carousel">
		<div id="boxarts_inner" class="inner">
		</div>
	</div>
<img src="../media/games/images/button_next.png" alt="Next" id="next"/>