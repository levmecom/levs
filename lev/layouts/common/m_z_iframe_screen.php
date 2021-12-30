
<style>
.show_ifr_title .navbar {display:block !important;}
.show_ifr_title .ziframecontentbox {padding-top:38px !important;background-position-y:89px !important;}
.ziframescreen .closescreen {position: absolute;top: 0;z-index: 999999;height:38px;width:44px;font-size:18px;
							 text-align: center;line-height:38px;color:#fff !important;background:#ff4141;}</style>
<div class="login-screen ziframescreen" style="z-index: 11001">
	<div class="view">
		<div class="page">
				<a class="closescreen draggable_btn" style="right:10px;top:30px;border-radius:50%;width:32px;height:32px;font-size:16px;box-shadow: 0 2px 4px rgba(0,0,0,.4);font-family: inherit;line-height: 32px;background:#fb1c46;display: flex">
                    <svg class="icon" aria-hidden="true"><use xlink:href="#fa-close"></use></svg></a>

          	<div class="navbar navbar-bgcolor-red" style="display:none;">
				<div class="navbar-inner">
                    <div class="left"><a class="link closescreen">
                            <svg class="icon" aria-hidden="true"><use xlink:href="#fa-back"></use></svg></a></div>
                    <div class="center titlex_box" title="<?php echo Lev::$app['title']?>"><?php echo Lev::$app['title']?></div>
                    <div class="right">
                        <a class="link closescreen"><svg class="icon" aria-hidden="true"><use xlink:href="#fa-close"></use></svg></a>
                    </div>
				</div>
			</div>
      
			<div class="page-content login-screen-content listscreen-content ziframecontentbox" 
				style="margin:0 !important;padding:0 !important;overflow:hidden !important;">
				
				<iframebox class="content-block" style="margin:0;padding:0;"></iframebox>
			</div>
      </div>
    </div>
</div>

<script>
//screen
function openziframescreenx(obj, _src, creload, titlex, hidetitle) {
    myApp.closeModal('.login-screen.ziframescreen', false);
    showIconLoader();

	var _src = _src + (_src && _src.indexOf('?') > 0 ? '&' : '?') +'ziframescreen=1';

    jQuery('.ziframescreen.login-screen').attr('class', 'login-screen ziframescreen');
	if (jQuery(obj).attr('sclass')) {
        jQuery('.ziframescreen.login-screen').addClass(jQuery(obj).attr('sclass'));
    }

	if (!hidetitle && (titlex || (typeof(siteUri)!='undefined' && (_src +'/').indexOf(siteUri) ==-1 && _src.indexOf('http') ==0))) {
		jQuery('.ziframescreen .page').addClass('show_ifr_title');
		jQuery('.ziframescreen .titlex_box').html(titlex ? titlex : jQuery('.ziframescreen .titlex_box').attr('title'));
	}else {
		jQuery('.ziframescreen .page').removeClass('show_ifr_title');
	}
	openscreen('.ziframescreen');
	var _ziframe = '<iframe width=100% height=100% frameborder=0 marginheight=0 src='+_src+'></iframe>';
	jQuery(document).off('opened', '.ziframescreen').on('opened', '.ziframescreen', function(){
        //jQuery(document).off('opened', '.ziframescreen');
		hideIconLoader();
		jQuery('.ziframescreen iframebox').attr('creload', creload).html(_ziframe);
	});
	
	jQuery('.ziframescreen .left_x_btn').hide();
	
	if(window.top != window.self) {
		jQuery('.ziframescreen .left_x_btn').show();
	}
}
jQuery(function(){
	jQuery(document).on('click', '.openziframescreen', function(){
        myApp.closeModal('.login-screen.ziframescreen', false);
	    return aToLoginScreenForce(this);
	});
    jQuery(document).on('close', '.ziframescreen', function(){
        jQuery('.ziframescreen.login-screen').attr('class', 'login-screen ziframescreen');
        jQuery('.ziframescreen iframebox').html('');
    });
	jQuery(document).on('closed', '.ziframescreen', function(){
		var creload = jQuery('.ziframescreen iframebox').attr('creload');
		jQuery('.ziframescreen iframebox').html('').removeAttr('creload');
		if (creload) {
		    if (creload === 'func') {
                Levme.ziframescreenReload();
                Levme.ziframescreenReload = function () {};
            }else {
                window.location.reload();
            }
		}
	});
});
</script>  

<script>
    function aToLoginScreenForce(obj, _href) {
        aToLoginScreen(obj, _href, true);
        return false;
    }
    function aToLoginScreen(obj, _href, force) {
        var x_src = _href ? _href : jQuery(obj).attr('_src');
        var href = jQuery(obj).attr('href');
        var xcreload = jQuery(obj).attr('creload');
        var titlex = jQuery(obj).attr('titlex');
        var hidetitle = jQuery(obj).attr('hidetitle');
        openziframescreenx(obj, x_src ? x_src : href, xcreload, titlex, hidetitle);
        if (force || href) return false;
    }
</script>




  
  
  