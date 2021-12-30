// Initialize your app
var myApp = new Framework7({
    //material:true,
    //materialPageLoadDelay:3,
    //allowDuplicateUrls:true,
    //pushStateNoAnimation:true,
    //reloadPages:true,
    //swipePanel: 'left',//左滑动屏幕打开侧边栏
    //fastClicks:false,
    activeState: jQuery('.noActiveState').length <= 0,
    modalTitle: '',
    modalButtonOk: '\u786e\u5b9a',//确定
    modalButtonCancel: '\u53d6\u6d88',///取消
    smartSelectBackText: '\u8fd4\u56de',//返回
    smartSelectPopupCloseText: '\u5173\u95ed',//关闭
    smartSelectPickerCloseText: '\u5b8c\u6210',//完成
    animateNavBackIcon: true,
    pushState: true,
    ajaxLinks: 'a.is_ajax_a',
    cache: typeof myAppCache !=='undefined' ? myAppCache : true,
    onAjaxStart: function (xhr) {
        //myApp.closeModal('', false);
        showIconLoader();
    },
    onAjaxComplete: function (xhr) {
        hideIconLoader(true);//console.log(xhr, JSON.parse(xhr.responseText).message);
        if (xhr.responseText) {
            if (xhr.responseText.indexOf('<title>提示 -') >= 0 && xhr.responseText.indexOf('showMsgBox') >= 0) {
                levtoast(xhr.responseText);
            }else if (xhr.responseText.indexOf('{') === 0) {
                var json = JSON.parse(xhr.responseText);
                if (json) {
                    if (json.confirmurl) {
                        myApp.confirm(json.message, '', function () {
                            Levme.ajaxv.getv(json.confirmurl, function (data, status) {

                            }, {confirmdoit:1});
                        });
                        return;
                    }
                    json.message && levtoast(json.message);
                    json.status === -5 && openLoginScreen();
                    json.tourl && (window.location = json.tourl);
                    json.notices && Levme.showNotices(json.notices);
                }
            }
        }
        if (typeof (ajax_complete_back) === "function") {
            ajax_complete_back(xhr)
        }
    }
});

function openscreen(id, hide, obj) {
    //showIconLoader();
    myApp.closeModal('.picker-modal', false);
    myApp.closeModal('.login-screen', false);
    myApp.closeModal('.popup', false);
    myApp.closeModal(id, false);
    myApp.loginScreen(id);//'.qju_login'
    if (!hide) {
        jQuery(id).on('opened', function(){hideIconLoader();});
    }
    var replyscreen = jQuery(obj).attr('reply');
    if (replyscreen) {
        jQuery(id +' .replytoscreen').attr('class', replyscreen +' replytoscreen');
    }

    jQuery('.cleartzbtn').click();
}

var LoginWinConfirm__ = 0;
function openLoginScreen(_id, animated, onConfirm) {
    var id = _id ? _id : '.my-login-screen';
    showIconLoader(true);
    myApp.closeModal('.actions-modal', false);
    myApp.closeModal('.popup', false);
    myApp.popup(id, false, animated);
    if (!jQuery(id).hasClass('login_screen') && id === '.my-login-screen') {
        hideIconLoader();
        myApp.closeModal('.modal.modal-in', false);
        onConfirm ? Levme.loginv.win() :
        myApp.confirm('抱歉，您需要登陆才能继续操作！', '', function () {
            Levme.loginv.win();
        });
    }
    jQuery(id).on('opened', function(){hideIconLoader();});
}

function arrayProduct(n) {
    if (!n) return 0;
    if (n < 2) {
        return n;
    }else {
        return (n * arrayProduct(n - 1));
    }
}
function arrayA(n, m) {
    if (n < m) return 0;
    if (n === m) return 1;
    return Math.round(arrayProduct(n) / arrayProduct( n - m ));
}
function arrayC(n, m, isrepeat) {
    if (n < m) return 0;
    if (n === m) return 1;
    isrepeat && (n += m - 1);//为真时包含重复组合
    return Math.round(arrayA(n, m) / arrayProduct(m));
}

function levArt(src) {
    var iframe = '<iframe style="min-width:700px;min-height:400px;height:100%;border:0;" src="'+src+'"></iframe>';
    levtoast(iframe, -1);
    return false;
}

var _levtoast2Timeout = null;
function levtoast2(msgtxt, time, cls, ckNoClose) {
    //if (msgtxt && msgtxt.indexOf('</title>') >0) return myApp.confirm('<div style="overflow: auto !important;">'+msgtxt+'</div>');
    clearTimeout(_levtoast2Timeout);
    var _cls = cls ? cls : '';
    var _time = time ? time : 2000;
    var _htm = '<div class="levtoast2 '+_cls+'"><div class="msgTxtBox">'+ msgtxt +'</div></div>';
    jQuery('.levtoast2').remove();
    msgtxt && jQuery('body').append(_htm);
    if (_time > 0) {
        _levtoast2Timeout = window.setTimeout(function(){ jQuery('.levtoast2').remove(); }, _time);
    }
    ckNoClose ? jQuery(document).off('click', '.levtoast2')
        : jQuery(document).on('click', '.levtoast2', function () { jQuery('.levtoast2').remove(); });
}
function levtoast(msgtxt, time, _cls) {
    return levtoast2(msgtxt, time, _cls);
}

function myLoginScreen(cls, animated) {
    openLoginScreen(cls, animated)
}

function getParseUrlQuery(url) {
    var pms = {};
    var _pms = url.split('?');
    if (_pms.length >1) {
        var _pms = _pms[1].split('&');
        for (var k in _pms) {
            var _pmss = _pms[k].split('=');
            pms[_pmss[0]] = _pmss[1];
        }
    }
    return pms;
}

function levConfirm(text, title, canFunc, celFunc) {
    myApp.confirm(text, title, canFunc, celFunc);
}

function ckWxUserAgent(userAgent) {
    return (userAgent ? userAgent : navigator.userAgent).indexOf('MicroMessenger') >0;
}

function deviceType(name) {
    return myApp.device[name];
}

function showToolbar(cls) {
    myApp.showToolbar(cls);
}
function hideToolbar(cls) {
    myApp.hideToolbar(cls);
}

function showIconLoader(clickHide, hideFunc) {
    myApp.showIndicator();
    var objm = '.preloader-indicator-modal, .preloader-indicator-overlay';
    jQuery(objm).css('z-index', '99999999999999999');
    if (clickHide) {
        Levme.onClick(objm, function () {
            hideIconLoader(hideFunc)
        });
    }else {
        jQuery(document).off('click', objm);
    }
}
function hideIconLoader(hideFunc) {
    myApp.hideIndicator();
    typeof hideFunc === "function" && hideFunc();
}

//设置滚动条到最底部
function setScrollBottom(id) {
    var ele = document.getElementById(id);
    if(ele.scrollHeight > ele.clientHeight) {
        ele.scrollTop = ele.scrollHeight;
    }
}

//lev 路由 levToRoute('/ucenter/my', {'uid':'1', 'dd':0})
function levToRoute(_rote, pm) {
    var url = '', pmstr = '';
    if (_rote && (_rote.indexOf('http') === 0 || _rote.indexOf('.php') > 0)) {
        url = _rote;
    }else if (typeof homeFile !== "undefined") {
        var rote = _rote && _rote.indexOf('/') === 0 ? _rote.slice(1) : _rote;
        if (homeFile.indexOf('.php') >=0) {
            url = homeUrl + '/' + homeFile + '?r='+ rote;
        }else {
            url = homeUrl + '/' + homeFile + rote
        }
    }else {
        url = homeUrl + (_rote.indexOf('/') === 0 ? '' : '/') + _rote;
    }
    if (pm) {
        for (var k in pm) {
            url = changeUrlArg(url, k, pm[k]);
        }
    }
    return url;
}
function changeUrlArgs(url, pm) {
    if (pm) {
        for (var k in pm) {
            url = changeUrlArg(url, k, pm[k]);
        }
    }
    return url;
}
function changeUrlArg(url, arg, val){
    if (!url) return url;

    var _replace = false;
    if (url.indexOf('?') <0) {
        url += '?' + arg + '=' + val;
    } else {
        var _pm = url.split('?');
        var _pm2 = _pm[1].split('&');
        for (var k in _pm2) {
            if (_pm2[k] && (_pm2[k].indexOf(encodeURI(arg) + '=') === 0 || _pm2[k].indexOf(arg + '=') === 0)) {
                _pm2[k] = arg +'='+ val;
                _replace = true;
                break;
            }
        }
        url = _pm[0] +'?'+ _pm2.join('&');
        url+= !_replace ? '&'+ arg +'='+ val : '';
    }
    return url;
}

function dom7showmsg(msgtxt, time) {
    myApp.closeModal();
    var time = time ? time : 1500;
    myApp.modal({
        title: false,
        text: '<div class="msg_box">'+ msgtxt +'</div>',
        buttons: [{
            text:'\u786e\u5b9a',//确定
            bold:true
        }]
    });
    if (time > 0) {
        var cls_win = window.setTimeout(function(){
            clearTimeout(cls_win);
            myApp.closeModal();
        }, time);
    }
}

function levmarquee(elementID, _h, _n, _speed, _delay, ckH){
    var t = null;
    var box = '#' + elementID;
    var h = _h ? _h : jQuery(box + ' li').height();
    var n = _n ? _n : 1;
    var speed = _speed ? _speed : 500;
    var delay = _delay ? _delay : 2000;
    if (jQuery(box + ' li').length <2) return false;
    if (ckH && jQuery(box).height() >= jQuery(box).children('ul:first').height()) return false;
    jQuery(box).hover(function(){
        clearInterval(t);
    }, function(){
        t = setInterval(function(){_start(box, h, n, speed)}, delay);
    }).trigger('mouseout');

    function _start(box, h, n, speed){
        jQuery(box).children('ul:first').animate({marginTop: '-='+h}, speed, function(){
            jQuery(this).css({marginTop:'0'}).find('li').slice(0,n).appendTo(this);
        })
    }
}

function loginscreen() {
    //levtoast('抱歉，您需要先登陆才能执行该操作！');
    levtoast('\u62b1\u6b49\uff0c\u60a8\u9700\u8981\u5148\u767b\u9646\u624d\u80fd\u6267\u884c\u8be5\u64cd\u4f5c\uff01');
    myLoginScreen();
}

function actionLocalStorage(key, val, del) {
    if (typeof localStorage === "undefined") {
        return false;
    }
    if (typeof(in__android__app) != 'undefined' && JSON.stringify(localStorage) === null) {
        return false
    }

    if (del) {
        return localStorage.removeItem(key);
    }

    if (val !== undefined) {
        return localStorage.setItem(key, val);
    }else {
        return localStorage.getItem(key);
    }
}

function reload_mainurl() {
    var urlx = window.location.href.split('#!');
    Levme.mainView.router.back({
        url: urlx[0],
        //animatePages:false,
        force:true,
        ignoreCache:true,
        reload:true,
        //reloadPrevious:true,
        pushState:false,
    });
    window.history.pushState('forward', null, urlx[0]);
}

function arr_remove(val, arr) {
    var xindex = arr.indexOf(val);
    if (xindex !=-1) {
        arr.splice(xindex, 1);
    }
    return arr;
}

function levmaxmin(noarr) {//返回数组中最大、最小数；
    var min = 0, max = 0;
    var len = noarr.length;
    for (var i=0; i<len; i++) {
        var no = parseInt(noarr[i]);
        min = i==0 || no <min ? no : min;
        max = i==0 || no >max ? no : max;
    }
    var res = [max,min];
    return res;
}

function errortips(obj, msg) {
    var dmsg = msg ? msg : '未知错误'+ (obj ? obj.status : '');
    if (!obj) {
        msg !== false && levtoast(dmsg, 5000);
    } else if (obj.responseJSON && obj.responseJSON.message) {
        levtoast(obj.responseJSON.message, 15000);
    }else if (obj.error && obj.error.message) {
        levtoast(obj.error.message, 15000);
    }else if (obj.message) {
        levtoast(obj.message, 15000);
    }else {
        dmsg = obj.responseText ? obj.responseText : dmsg;
        msg !== false && jQuery.trim(dmsg) && levtoast(dmsg, 5000);
    }
}

function showFormErrors(data, box) {
    box = box ? box : '';
    jQuery('errors').hide();
    for (var key in data) {
        var obj = jQuery(box +'[name="'+ key +'"], #'+ key);
        var objp = obj.parent();
        if (jQuery(box + '[name="' + key + '[]"]').parents('.checkbox-list').length >0) {
            objp = jQuery(box + '[name="' + key + '[]"]').parents('.checkbox-list');
            obj = objp;
        }
        if (objp.find('errors').html()) {
            objp.find('errors').html(data[key]).show();
        }else {
            objp.append('<errors>'+ data[key] +'</errors>');
        }
        obj.addClass('errorsTip');
        obj.focus();
    }
    jQuery(document).on('change', 'input,textarea', function () {
        jQuery(this).parent().find('errors').hide();
        jQuery(this).removeClass('errorsTip');
    });
}

function levtoMao(val) {
    var myY = jQuery("#"+val).offset().top;
    jQuery("html,body").stop().animate({ scrollTop:myY},800);
}


function levtoMaoLeft(id, pid, ts, extra, top) { //console.log(jQuery(id).offset());
    var _ts = ts ? ts : 300;
    var _extra = parseFloat(extra);
    _extra = isNaN(_extra) ? 0 : _extra;
    var ztop = 0, ctop = 0, myY = 0;
    if (top) {
        ztop = jQuery(pid).find(id).offset().top - jQuery(pid).offset().top;
        ctop = jQuery(pid).scrollTop();
        myY = ztop + ctop + (ztop >0 ? _extra : 0);//console.log(myY, ztop);
        jQuery(pid).stop().animate({scrollTop : myY}, _ts);
    }else {
        ztop = jQuery(pid).find(id).offset().left - jQuery(pid).offset().left;
        ctop = jQuery(pid).scrollLeft();
        myY = ztop + ctop + (ztop > 0 ? _extra : 0);
        jQuery(pid).stop().animate({scrollLeft : myY}, _ts);
    }
}
function levtoMaoTop(id, pid, pts, ts) {
    ts = ts ? ts : 30;
    var ztop = (pts ? jQuery(id).parents(pts).offset().top : jQuery(id).offset().top) - jQuery(pid).offset().top;
    var ctop = jQuery(pid).scrollTop();
    var myY = ztop + ctop;//console.log(ztop, ctop, myY, jQuery(pid));
    jQuery(pid).stop().animate({scrollTop : myY}, ts);
}
function levtoMaoCenter(id, pid, ts, extra, top, navBoxWidth) { //console.log(jQuery(id).offset());
    if (jQuery(pid).find(id).length <1) return;
    var _ts = ts ? ts : 300;
    var _extra = parseFloat(extra);
    _extra = isNaN(_extra) ? 0 : _extra;
    var ztop = 0, ctop = 0, myY = 0;
    if (top) {
        ztop = jQuery(pid).find(id).offset().top - jQuery(pid).offset().top;
        ctop = jQuery(pid).scrollTop();
        myY = ztop + ctop + (ztop >0 ? _extra : 0);//console.log(myY, ztop);
        jQuery(pid).stop().animate({scrollTop : myY}, _ts);
    }else {
        navBoxWidth = navBoxWidth ? navBoxWidth : jQuery(pid).parent().width();
        ztop = jQuery(pid).find(id).offset().left - jQuery(pid).offset().left;
        ctop = jQuery(pid).scrollLeft();
        myY = ztop + ctop + (ztop > 0 ? _extra : 0) - navBoxWidth/2 + jQuery(id).width();
        jQuery(pid).stop().animate({scrollLeft : myY}, _ts);
    }
}

function checkedToggle(toggleId, opid) {
    if (jQuery(toggleId).prop('checked')) {
        jQuery(opid).prop('checked', true);
    }else {
        jQuery(opid).prop('checked', false);
    }
}


function levcutstr(str, len, hasDot) {
    var newLength = 0;
    var newStr = "";
    var chineseRegex = /[^\x00-\xff]/g;
    var singleChar = "";
    var strLength = str.replace(chineseRegex, "**").length;
    for (var i = 0; i < strLength; i++) {
        singleChar = str.charAt(i).toString();
        if (singleChar.match(chineseRegex) != null) {
            newLength += 2;
        }
        else {
            newLength++;
        }
        if (newLength > len) {
            break;
        }
        newStr += singleChar;
    }

    if (hasDot && strLength > len) {
        newStr += "...";
    }
    return newStr;
}

function dlevrandom(max, num){
    var randarr = [];//从0－max随机取出不重复的数字；num取出个数
    var a = [];
    for(var i = 0; i <=max; i++){
        a.push(i);
    }
    for (var j=0; j < num; j++) {
        var b = [];
        var _idx = -1;
        for (i=0; i <=max; i++) {
            if (!isNaN(a[i])) {
                _idx+= 1;
                b[_idx] = a[i];
            }
        }
        var _len = b.length;
        var _randnum = levrandom(1, _len) -1;
        randarr[j] = b[_randnum];
        a.splice(_randnum, 1);
        //a.sort();console.log('数组：', a);
    }
    return randarr;
}
function levrandom(min, max) {
    if (max == null) {
        max = min;
        min = 0;
    }
    return min + Math.floor(Math.random() * (max - min + 1));
}

function animateCSS(element, animationName, callback) {
    var node = document.querySelector(element);
    //node.classList.add('animated', animationName);
    jQuery(element).addClass(animationName).addClass('animated');

    function handleAnimationEnd() {
        //node.classList.remove('animated', animationName);
        jQuery(element).removeClass(animationName);
        node.removeEventListener('animationend', handleAnimationEnd);

        if (typeof callback === 'function') callback();
    }

    node.addEventListener('animationend', handleAnimationEnd);
}

jQuery('.open-panel').on('click', function(){
    var panel_left_open = false;
    jQuery('.panel-left').on('open', function () {
        panel_left_open = true;
    });
    if (!panel_left_open) {
        myApp.closePanel();
    }
});

jQuery('textarea').dblclick(function(){
    var obj = this;
    if (jQuery(obj).hasClass('isBigArea')) {
        if (jQuery(obj).hasClass('dbLc')) {
            jQuery(obj).removeClass('isBigArea dbLc');
            jQuery(obj).parent().removeClass('isBigArea');
        }else {
            jQuery(obj).addClass('dbLc');
            Levme.showNotices('【提示】5秒内两次双击将还原输入框', 0, 5000);
            window.setTimeout(function () { jQuery(obj).removeClass('dbLc'); }, 5000);
        }
    }else {
        jQuery(obj).addClass('isBigArea');
        jQuery(obj).parent().addClass('isBigArea');
    }
});

var Levme = {
    animated:function (obj, animate) {
        var animated = 'animated '+ (animate ? animate : 'heartBeat');
        jQuery(obj).addClass(animated);
        window.setTimeout(function () { jQuery(obj).removeClass(animated); }, 1000);
    },
    setIframeHeight:function (id, ht) {jQuery(id).css('height', ht);},
    unload:function () {
        jQuery(window).unload(function () {
            levtoast('ddd');
        });
    },
    todYmd:function () {
        var date = new Date();
        var year = date.getFullYear().toString();
        var month = Levme.addx0(date.getMonth()+1) +'';
        var day = Levme.addx0(date.getDate()) +'';
        return year + month + day;
    },
    addx0:function (num) {
        var number = parseFloat(num);
        if (isNaN(number)) return num;
        number = number < 10 ? '0'+ number : number;
        return number;
    },
    sign: {
        init: function () {
            Levme.onClick('.signBtn', function () {
                Levme.sign.signBtn(jQuery(this).data('typeid'));
            });
            if (Levme.todYmd() != actionLocalStorage('signYmd')) {
                jQuery('.signNavbarBtn cir.hiddenx').removeClass('hiddenx');
            }
        },
        signBtn: function (typeid) {
            Levme.ajaxv.getv(levToRoute('ajax/sign', {id: 'levsign', typeid: typeid}), function (data, status) {
                if (status > 0) {
                    jQuery('.typeid_' + typeid).removeClass('signBtn').addClass('isSigned');
                    actionLocalStorage('signYmd', Levme.todYmd());
                }
                jQuery('.tabBtnBox .tabBtn_' + typeid + ' cir').remove();
            });
        },
    },
    setups:{
        btn:function () {
            Levme.onClick('.setupsBox a', function () {
                var opid = parseInt(jQuery(this).parents('.setupsBox').attr('opid'));
                if (isNaN(opid) || opid < 1) {
                    Levme.showNotices('抱歉，新增数据，你必须先保存第一步内容，才能进入下一步', 'error');
                    return false;
                }
            });

            var _setup_btn_idx = jQuery('.setupsBox a.active').index('.setupsBox a');
            jQuery('.setupsBox a:lt('+(_setup_btn_idx <0 ? 1 : _setup_btn_idx)+')').addClass('color-black active');
        }
    },
    showNotices:function (str, type, timeout) {
        timeout = timeout ? timeout : 25000;
        type = type ? type : 'info';
        str = str !== undefined ? str : actionLocalStorage('cookieNotices');
        str && str !== "" &&
        window.setTimeout(function () {
            actionLocalStorage('cookieNotices', 0, 1);
            myApp.addNotification({
                title: '<style>.notifications .item-title-row{display: none !important;}</style>',
                message: '<div class="notification notification-session"><div class="item-text alert-'+type+'">' + str + '</div></div>',
                media: '',
                closeOnClick: true,
                hold: timeout,
            });
        }, 200);
    },
    showNoticesAdd:function (str, type, timeout) {
        jQuery('.notification.notification-session .item-text').length >0
            ? jQuery('.notification.notification-session .item-text').append(str)
            : Levme.showNotices(str, type, timeout);
    },
    getNoticsAddhtm:function () {
        return jQuery('.notification.notification-session .item-text').html();
    },
    loginv:{
        btns: function () {
            Levme.onClick('.dLoginBtn', function () {
                Levme.loginv.dLoginScreen();
                return false;
            });
            Levme.onClick('.openLoginBtn', function () {
                Levme.loginv.win(jQuery(this).attr('href'));
                return false;
            });
            Levme.onClick('.openRegisterBtn', function () {
                Levme.loginv.reg(jQuery(this).attr('href'));
                return false;
            });
            Levme.onClick('.exitLoginOutBtn', function () {
                Levme.loginv.exitLogin(this);
                return false;
            });
        },
        win:function (src) {
            src = src ? src : Levme.loginv.loginUrl;
            if (src) {
                return Levme.popupIframe.popupShow(0, src);
            }
            myApp.closeModal('.popup', false);
            myApp.popup('.my-login-screen', false);
            Levme.loginv.loadScreen();
        },
        reg:function (src) {
            src = src ? src : Levme.loginv.registerUrl;
            if (src) {
                return Levme.popupIframe.popupShow(0, src);
            }
            myApp.closeModal('.popup', false);
            myApp.popup('.my-register-screen', false);
            Levme.loginv.loadScreen();
        },
        dLoginScreen:function () {
            myApp.closeModal('.popup', false);
            myApp.popup('.my-login-screen', false);
            Levme.loginv.loadScreen();
        },
        exitLogin:function () {
            myApp.confirm('您确定要退出登陆吗？', '', function () {
                Levme.ajaxv.getv(levToRoute('login/exit', {id:APPVIDEN}), function(data, status) {
                    if (status > 0) {
                        if (data.loginReferer && data.loginReferer !== "") {
                            window.top.location = data.loginReferer;
                        }else if (window.top.location.href.indexOf('accesstoken=') >0) {
                            window.top.location = window.top.location.href.replace(/accesstoken=/g, 'akenx=');
                        }else {
                            window.top.location = window.location.href.split('#!/')[0];
                        }
                    }
                });
            });
        },
        loaded:0,
        loginUrl:0,
        registerUrl:0,
        loadScreen:function () {
            if (Levme.loginv.loaded) return;
            Levme.loginv.loaded = 1;
            //if (jQuery('.my-login-screen').length >0) return;
            showIconLoader(true);
            Levme.ajaxv.getv(levToRoute('login/load-screen', {id: RouteIden}), function (data, status) {
                if (status > 0 && data) {
                    data.loginUrl && (Levme.loginv.loginUrl = data.loginUrl);
                    data.registerUrl && (Levme.loginv.registerUrl = data.registerUrl);
                    data.htms && jQuery('.my-login-screen').length <1 && jQuery('body').append(data.htms);
                    Levme.loginv.win();
                }
            });
        }
    },
    confirm:function (text, title, canFunc, celFunc) {
        text = text ? text : '<b>'+ title+ '</b>';
        var btn = '<p class="flex-box ju-sa">' +
            '<a class="button button-fill Btn1">确定</a><a class="button button-fill color-gray Btn2">取消</a></p>';
        levtoast(text + btn, 3600000, 'LevmeConfirm');
        Levme.onClick('.levtoast2.LevmeConfirm .Btn1', function () { typeof canFunc === "function" && canFunc() });
        Levme.onClick('.levtoast2.LevmeConfirm .Btn2', function () { typeof celFunc === "function" && celFunc() });
    },
    openedScreens:{},
    screenHtml: function(id, htm) {
        if (htm) {
            return jQuery('.lev-screen.swiper-box.' + id + ' .swiper-slide').eq(1).html(htm);
        }else {
            return jQuery('.lev-screen.swiper-box.' + id + ' .swiper-slide').eq(1).html();
        }
    },
    screenClose:function(id, animated, forceClose) {
        Levme.onClick('.closeLevScreen', function () {
            var id = jQuery(this).parents('.lev-screen.swiper-box').data('id');
            closeScreen(id, animated);
        });

        id && closeScreen(id, animated, forceClose);

        function closeScreen(id, animated, forceClose) {
            if (typeof Levme.openedScreens[id] !== "undefined") {
                var len = Levme.openedScreens[id].length - 1;
                if (typeof Levme.openedScreens[id][len] === "function") {
                    if (forceClose === 1) {
                        Levme.openedScreens[id][len](0);
                    } else {
                        Levme.openedScreens[id][len](forceClose ? 0 : animated);
                    }
                    Levme.openedScreens[id].splice(len, 1);
                } else {
                    jQuery('.lev-screen.swiper-box.' + id).remove();
                }
            } else {
                jQuery('.lev-screen.swiper-box.' + id).remove();
            }
        }
    },
    ajaxv:{
        isGetv:0,
        postv:function (url, sucFunc, param) {
            return Levme.ajaxv.dov(url, sucFunc, 'post', param);
        },
        getv:function (url, sucFunc, param) {
            return Levme.ajaxv.dov(url, sucFunc, 'get', param);
        },
        dosubmit:function (formid, action, sucFunc) {
            if (Levme.ajaxv.isGetv) {
                showIconLoader(true);
                return;
            }
            Levme.ajaxv.isGetv = 1;
            jQuery(formid).ajaxSubmit({
                url: action ? action : '',
                data: {dosubmit:1, _csrf:_csrf, inajax:1, _:Math.random()},
                type:'post',
                dataType: 'json',
                success: function(data){
                    Levme.ajaxv.isGetv = 0;
                    hideIconLoader();
                    var status = 0;
                    if (data) {
                        data.message && levtoast(data.message);

                        status = parseInt(data.status);
                        if (status === -5) {
                            openLoginScreen();
                            return false;
                        }
                    }
                    typeof sucFunc === "function" && sucFunc(data, status);
                    showFormErrors(data.errors);
                },
                error: function(data) {
                    Levme.ajaxv.isGetv = 0;
                    typeof sucFunc === "function" && sucFunc(data);
                    hideIconLoader();
                    errortips(data);
                }
            });
            return false;
        },
        dov:function (url, sucFunc, type, param) {
            if (Levme.ajaxv.isGetv) {
                showIconLoader(true);
                return;
            }
            param === undefined && (param = {});
            param['_csrf'] = _csrf;
            param['_'] = Math.random();
            Levme.ajaxv.isGetv = 1;
            jQuery.ajax({
                url: url,
                data: param,
                dataType: 'json',
                type: type ? type : 'get',
                success: function (data) {
                    Levme.ajaxv.isGetv = 0;
                    hideIconLoader();
                    var status = 0;
                    if (data) {
                        data.message && levtoast(data.message);

                        status = parseInt(data.status);
                        if (status === -5) {
                            openLoginScreen();
                            return false;
                        }
                    }
                    typeof sucFunc === "function" && sucFunc(data, status);
                },
                error: function (data) {
                    Levme.ajaxv.isGetv = 0;
                    typeof sucFunc === "function" && sucFunc(data);
                    hideIconLoader();
                    errortips(data);
                }
            });
        }
    },
    screen:function(id, params) {
        params = params ? params : {};
        id = id ? id : 'notId';

        var timeId = 'timeId' + new Date().getTime();
        var animated = params.animated === undefined ? true : params.animated;
        Levme.screenClose(id, animated, 1);

        var swiperScreen;
        var cls = '.lev-screen.swiper-box.'+ id + '.' +timeId;
        var html = params.html ? params.html : '';

        jQuery('body').append('<div class="lev-screen login-screen force-in virtual-list swiper-box '+id+' '+timeId+'" data-id="'+ id +'" data-cls="'+cls+'">' +
            '<div class="swiper-wrapper virtual-list">' +
            '<div class="swiper-slide"></div><div class="swiper-slide '+timeId+'">'+ html +'</div><div class="swiper-slide"></div></div></div>');

        var closeScreen = function (animated) {
            typeof params.onClose === "function" && params.onClose();
            animated ? swiperScreen.slidePrev() : jQuery(cls).remove();
            //Levme.openedScreens[id] = undefined;
        };

        swiperScreen = myApp.swiper(cls, {
            initialSlide:0,
            speed:400,
            //loop:true,
            direction: params.direction ? 'vertical' : undefined,
            onTransitionStart:function (s) {
            },
            onSlideChangeEnd:function (s) {
                myApp.loginScreen(cls+ '.login-screen', false);
                s.activeIndex !== 1 && closeScreen();
            },
        });

        swiperScreen.slideNext();

        //window.setTimeout(function () { jQuery(cls+ '.login-screen').addClass('force-in'); }, 400);

        typeof params.open === "function" && params.open();
        typeof params.opened === "function" && window.setTimeout(function () { params.opened(); }, 400);
        if (typeof Levme.openedScreens[id] !== "undefined") {
            Levme.openedScreens[id][Levme.openedScreens[id].length] = closeScreen;
        }else {
            Levme.openedScreens[id] = [];
            Levme.openedScreens[id][0] = closeScreen;
        }
        return swiperScreen;
    },
    picker:function(id, pickerHTML, openedFunc) {
        myApp.closeModal('.levpicker-modal.'+ id, false);
        var pickerBox = '<div class="picker-modal levpicker-modal '+ id +'">'+ pickerHTML +'</div>';
        myApp.pickerModal(pickerBox);
        jQuery('body').append('<div class="picker-modal-overlay modal-overlay-visible '+ id +'Overlay"></div>');
        jQuery(document).off('picker:closed', '.picker-modal.'+ id).on('picker:closed', '.picker-modal.'+ id, function () {
            jQuery('.picker-modal-overlay.'+id+'Overlay').remove();
        });
        jQuery(document).off('picker:opened', '.picker-modal.'+ id).on('picker:opened', '.picker-modal.'+ id, function () {
            //jQuery('body').append('<div class="picker-modal-overlay modal-overlay-visible '+ id +'Overlay"></div>');
            typeof openedFunc === "function" && openedFunc();
        });
    },
    setMyCity:function (pm) {
        !actionLocalStorage('myCity') && jQuery('iframe.setMyCity').length <1 && jQuery('body').append(Levme.myCity(0,pm,0,0,1));
    },
    myCity:function (inputCls, pm, def, unLoad, initCity) {
        !pm && (pm = {});
        !pm.id && (pm.id = 'levroom');

        var inputValue = jQuery(inputCls).val();
        var city = inputValue ? inputValue : actionLocalStorage('myCity');
        var iframestr = '';
        var mapurl = levToRoute('map', pm);
        var cityInput = '<input type="hidden" name="address" value="' + (city ? city : '') + '">';
        if (city) {
            if (inputCls) {
                window.setTimeout(function () { !inputValue && jQuery(inputCls).val(city); }, 1000);
            }
        }else {
            city = def ? def : '标注城市';
            iframestr = unLoad ? '' : '<iframe class="setMyCity" style="display:none !important;" src="'+mapurl+'"></iframe>';
            if (initCity) return iframestr;
        }
        return '<a target="_blank" href="'+mapurl+'">' +
            '<svg class="icon"><use xlink:href="#fa-weizhi"></use></svg><sas>'+ city +'</sas>' + cityInput + '</a>'+ iframestr;
    },
    APP:{
        init:function () {
            var app = Levme.APP.ckAPP();
            typeof app.hideFab === "function" && app.hideFab();

            if (jQuery('.apkDownBox cir').length > 0) {
                var version = jQuery('.apkDownBox cir').attr('v');
                if (version && typeof app.version === "function" && app.version() != version) {
                    jQuery('.apkDownBox cir').show().removeClass('hiddenx');
                }
            }
        },

        ckAPP:function () {
            if (typeof(in__android__app) != "undefined") {
                return in__android__app;
            }
            return false;
        },
        appNoticeLive:function (uid) {
            if (typeof(in__android__app) != "undefined") {
                var xxuid = in__android__app.setLoginUID(uid);
                if (xxuid == uid) {
                    in__android__app.getNotification(true);
                }
            }
        },
        phone:0,
        myPhoneLogin:function (phone) {
            if (typeof in__android__app !== "undefined") {
                if (phone === true) {
                    window.setTimeout(function () {
                        !Levme.APP.phone && Levme.showNotices('长时间未响应，请确保已开通获取电话号码权限且未开启隐私保护', 'info', 50000);
                    }, 2000);
                    in__android__app.loginMyPhoneDialog();
                } else {
                    Levme.APP.phone = phone;
                    phone = Levme.encodeHTML(phone);
                    var code = Levme.encodeHTML(actionLocalStorage('androidAppSign'));
                    var myphoneurl = levToRoute('auth/myphone',{id:'levmb', phone:phone, code:code, dosubmit:1});
                    Levme.ajaxv.postv(myphoneurl, function (data, status) {
                        status >0 &&
                        window.setTimeout(function () {
                            data.tourl ? (window.top.location = data.tourl) : window.top.location.reload();
                        }, 400);
                    });
                    actionLocalStorage('androidAppSign', '', true);
                }
            }else {
                levtoast('非APP无法使用');
            }
        },
    },
    ziframescreenReload:function () {},
    openziframescreenx:function (obj, _src, creload, titlex, hidetitle) {
        if (typeof creload === "function") {
            Levme.ziframescreenReload = creload;
            creload = 'func';
        }
        openziframescreenx(obj, _src, creload, titlex, hidetitle);
    },
    LoginReferer:false,
    checkLogin:function(not) {
        if (actionLocalStorage('UID') <1) {
            !not && openLoginScreen();
            return false;
        }
        return true;
    },
    onClick:function (cls, func) {
        jQuery(document).off('click', cls).on('click', cls, func);
    },
    photoShow:function (src) {
        myApp.photoBrowser({
            photos:[src],
            theme: 'dark',
            type: 'standalone'
        }).open();
    },
    //eg:Levme.onClick('.msgImgBox .button, .msgImgBox img', function () { Levme.photoShows(this, '.msgImgBox .button, .msgImgBox img'); });
    photoShows:function (obj, img) {
        var photos = [];
        var idx = jQuery(obj).index(img);
        jQuery(img).each(function (n) {
            var src = jQuery(this).attr('src') ? jQuery(this).attr('src') : jQuery(this).attr('title');
            src && photos.push(src.replace('/thumb_320_320_', '/').replace('/thumb_150_0_', '/').replace('/thumb_320_170_', '/'));
        });
        var myPhotoBrowser = myApp.photoBrowser({
            zoom: 400,
            initialSlide: idx,
            photos: photos,
            backLinkText: '<p class="date">上滑关闭</p>',
            ofText: 'of',
            theme: 'dark',
            type: 'standalone',//popup
            toolbarTemplate: '<div class="toolbar tabbar"><div class="toolbar-inner">'
                + '<a href="#" class="link photo-browser-prev"><i class="icon icon-prev color-white"></i></a>'
                + '<a class="link dorotate"><svg class="icon"><use xlink:href="#fa-refresh"></use></svg></a>'
                + '<a href="#" class="link photo-browser-next"><i class="icon icon-next color-white"></i></a>'
                + '</div></div>',
        });
        myPhotoBrowser.open();

        Levme.onClick('.dorotate', function (e, n) {
            var obj = jQuery('.swiper-slide-active .swiper-zoom-container img');//.swiper-zoom-container
            var deg = parseInt(obj.attr('deg'));
            deg = isNaN(deg) ? 90 : deg + 90;
            var tsf = obj.css('transform');
            //console.log(tsf);
            var scl = '';
            if (tsf && tsf.indexOf('3,') > 0) {
                scl = 'scale(3) ';
            }
            obj.css('transform', scl + 'rotate(' + deg + 'deg)').attr('deg', deg);
        });
    },
    formLoader:function (cls, func) {
        cls = cls ? cls : 'form [type=submit]';
        jQuery(document).on('click', cls, function () {
            showIconLoader();
            loopCheckError();
            function loopCheckError() {
                window.setTimeout(function () {
                    if (jQuery('form .has-error').hasClass('has-error')) {
                        hideIconLoader()
                    }else {
                        loopCheckError();
                    }
                }, 500);
            }
            typeof func === "function" && func();
        });
    },
    likes: {
        init:function (opCls) {
            Levme.onClick(opCls ? opCls : '.likesA', function () {
                return Levme.likes.ajaxOp(this);
            });
            Levme.likes.myLikes();
        },
        myLikes:function () {
            var myLikesId = {};
            jQuery('.likesA').each(function () {
                var id = jQuery(this).data('ckid') ? jQuery(this).data('ckid') : jQuery(this).data('link');
                var sg = jQuery(this).data('iden') + id;
                if (actionLocalStorage(sg)) {
                    jQuery(this).addClass('color-red');
                    myLikesId[id] = id;
                }
            });
            return myLikesId;
        },
        ajaxOp:function(obj) {
            if (jQuery(obj).hasClass('noAjax')) {
                return false;
            }
            jQuery(obj).attr('disabled', true);
            var iden = jQuery(obj).data('iden');
            var link = jQuery(obj).data('link');
            var ckid = jQuery(obj).data('ckid');
            jQuery.ajax({
                url: levToRoute('likes/ajax/op'),
                data: {
                    ajax: 1,
                    iden: iden,
                    name: jQuery(obj).data('name'),
                    link: link,
                    ckid: ckid,
                    s: jQuery(obj).attr('s'),
                    _: Math.random()
                },
                dataType: 'json',
                type: 'get',
                success: function (data) {
                    window.setTimeout(function () {
                        jQuery(obj).removeAttr('disabled');
                    }, 2000);
                    if (data.message) {
                        levtoast(data.message);
                    }
                    if (parseInt(data.status) > 0) {
                        var sg = iden + (ckid ? ckid : link);
                        if (data.status === 1) {
                            jQuery(obj).addClass('color-red');
                            actionLocalStorage(sg, 1);
                        } else {
                            jQuery(obj).addClass('color-gray').removeClass('color-red');
                            actionLocalStorage(sg, 1, 1);
                        }
                    } else if (parseInt(data.status) === -5) {
                        openLoginScreen();
                    } else {
                    }
                },
                error: function (data) {
                    window.setTimeout(function () {
                        jQuery(obj).removeAttr('disabled');
                    }, 2000);
                    errortips(data);
                }
            });
            return false;
        },
    },
    deleteUploadImg:function (id, func) {
        showIconLoader();
        jQuery.ajax({
            url: levToRoute('uploads/upload/delete'),
            data:{id:id, _:Math.random()},
            dataType:'json',
            type:'get',
            success:function(data){
                hideIconLoader();
                if (data && data.message) {
                    levtoast(data.message, 5000);
                }
                var status = parseInt(data.status);
                if (status === -5) {
                    openLoginScreen();
                }else if (status > 0) {

                }
                if (typeof func === "function") {
                    func(data);
                }
            },
            error:function(data){
                hideIconLoader();
                errortips(data);
            }
        });
    },
    uploadImgForm:function (clsFile, url, formObj, successFunc, parentBox, notLogin) {
        var uploadIng = false;
        jQuery(document).off('change', clsFile).on('change', clsFile, function(){
            if (!notLogin && !Levme.checkLogin()) return;
            if (uploadIng) {
                showIconLoader(true);
                return false;
            }
            uploadIng = true;
            var obj = this;
            if (jQuery(obj).val()) {
                var formMain = parentBox ? jQuery(this).parents(parentBox).eq(0) : clsFile+'Form';
                showIconLoader(true);
                jQuery(formMain).ajaxSubmit({
                    url: url,
                    data:{tid:jQuery(obj).data('tid'), pid:jQuery(obj).data('pid'), _csrf:_csrf, field:clsFile, _:Math.random()},
                    dataType:'json',
                    type:'POST',
                    processData: false, //需设置为false,因为data值是FormData对象，不需要对数据做处理
                    contentType: false, //需设置为false,因为是FormData对象，且已经声明了属性enctype="multipart/form-data"
                    resetForm: true, //成功提交后，是否重置所有表单元素的值
                    uploadProgress: function (event, position, total, percentComplete) {
                        if(percentComplete >= 100){
                            uploadIng = false;
                            percentComplete = 100;
                        }
                        progress = percentComplete;
                        uploadIngProgressbar(formObj);
                        errortips(event, false);
                    },
                    success: function(data){
                        jQuery(formMain).find('input[type="file"]').val('');
                        uploadIng = false;
                        hideIconLoader();
                        if (data) {
                            if (data.message) {
                                levtoast(data.message);
                            }
                            var status = parseInt(data.status);
                            if (status > 0) {
                                if (jQuery(clsFile + 'Show').length >0 && data.url && data.src) {
                                    jQuery(clsFile +'Show')
                                        .append('<img src="'+
                                            data.url+'" height="40" width="40" data-src="'+
                                            data.src+'" data-dbid="'+
                                            data.dbId+'">');
                                }
                                setImgIds(data.dbId);
                                typeof successFunc === "function" && successFunc(data, formMain);
                            }else if (status === -5) {
                                openLoginScreen();
                            }
                        }else {
                            levtoast('未知错误，上传失败');
                        }
                    },
                    error: function(data) {
                        jQuery(formMain).find('input[type="file"]').val('');
                        uploadIng = false;
                        hideIconLoader();
                        if (Levme.APP.ckAPP() && data.statusText === "error") {
                            //levtoast('图片发送失败，请开启相机权限、存储权限');
                            errortips(data, false);
                        }else {
                            errortips(data);
                        }
                    }
                });
            }
        });

        Levme.onClick(clsFile +'Show img', function () {
            var obj = this;
            var dbid = jQuery(obj).data('dbid');
            if (dbid) {
                Levme.confirm('您确定要删除图片吗？', '', function () {
                    Levme.deleteUploadImg(dbid, function (data) {
                        if (parseInt(data.status) >0) {
                            jQuery(obj).remove();
                            setImgIds(dbid, 1);
                        }
                    });
                });
            }
        });

        function setImgIds(id, del) {
            var imgIdsInput = '._imgIds';
            var ids = jQuery(imgIdsInput).val();
            if (del) {
                if (ids) {
                    var idsArr = ids.split(',');
                    idsArr = arr_remove(id.toString(), idsArr);
                    jQuery(imgIdsInput).val(idsArr.join(',') + ',');
                }
            }else {
                jQuery(imgIdsInput).val(ids + id +',');
            }
        }

        var progress = 0;
        function uploadIngProgressbar(boxCls) {
            if (!boxCls) return;
            var container = jQuery(boxCls);
            //don't run all this if there is a current progressbar loading
            if (container.children('.progressbar, .progressbar-infinite').length) return;

            myApp.showProgressbar(container, 0, 'red');

            // Simluate Loading Something
            function simulateLoading() {
                setTimeout(function () {
                    var progressBefore = progress;
                    myApp.setProgressbar(container, progress);
                    if (progressBefore < 100) {
                        simulateLoading(); //keep "loading"
                    } else {
                        jQuery('.progressbar').html('<div class="progressbar-infinite color-multi"></div>');
                        if (!uploadIng) {
                            window.setTimeout(function () {
                                myApp.hideProgressbar(container);
                            }, 1000);
                        }
                    }
                }, Math.random() * 200 + 200);
            }
            simulateLoading();
        }
    },
    setLoadInfiniteDefault:function (infBox) {
        jQuery(infBox).find('.listInfBox').attr('page', "2");
        jQuery(infBox).find('.infiniteStart').html('点击加载更多').attr('disabled', false).removeAttr('disabled');
    },
    setLoadInfiniteNothing:function (infBox, message) {
        jQuery(infBox).find('.listInfBox').attr('page', "-1");
        jQuery(infBox).find('.infiniteStart').html(message ? message : '没有了').attr('disabled', true);
    },
    loadInfiniteAjax:function (infBox, force) {//list-block virtual-list
        var loadInfiniteAjaxForceIng = false;
        if (parseInt(jQuery(infBox).find('.listInfBox').attr('page')) === 2) {
            jQuery(document).on('infinite', infBox, function () {
                if (!jQuery(infBox).find('.infinite-scroll-preloader').hasClass('hiddenx')) {
                    return false;
                }
                parseInt(jQuery(infBox).find('.listInfBox').attr('page')) > 2 && doInfiniteData();
            });
        }
        if (force) {
            if (loadInfiniteAjaxForceIng) {
                showIconLoader(true);
                return false;
            }
            loadInfiniteAjaxForceIng = true;
            doInfiniteData();
            jQuery(infBox).find('.infinite-scroll-preloader').removeClass('hiddenx');
            return false;
        }
        function doInfiniteData() {
            var virtualListBox = jQuery(infBox).find('.listInfBox .virtual-list');
            var page = parseInt(jQuery(infBox).find('.listInfBox').attr('page'));
            jQuery(infBox).find('.infinite-scroll-preloader').removeClass('hiddenx');
            jQuery.ajax({
                url: jQuery(infBox).find('.listInfBox').attr('url') ? jQuery(infBox).find('.listInfBox').attr('url') : '',
                data:{page:page, infinite:1, _:Math.random()},
                dataType:'json',
                type:'get',
                success:function(data){
                    loadInfiniteAjaxForceIng = false;
                    jQuery(infBox).find('.infinite-scroll-preloader').addClass('hiddenx');
                    if (data && data.message) {
                        levtoast(data.message, 5000);
                    }
                    if (parseInt(data.status) === -5) {
                        openLoginScreen();
                    }else if (parseInt(data.status) >0 && data.htms) {
                        jQuery(infBox).find('.listInfBox').attr('page', data.page ? data.page : page+1);
                        virtualListBox.length <1 ?
                            jQuery(infBox).find('.listInfBox').append(
                                data.htms.indexOf('</tr>') >0
                                    ? '<tbody class="virtual-list">'+ data.htms +'</tbody>'
                                    : '<div class="virtual-list">'+ data.htms +'</div>'
                            ) :
                            virtualListBox.append(data.htms);
                    }else if (data.status === -1 || jQuery.trim(data.htms) === "") {
                        Levme.setLoadInfiniteNothing(infBox, data.message);
                    }
                },
                error:function(data){
                    loadInfiniteAjaxForceIng = false;
                    jQuery(infBox).find('.infinite-scroll-preloader').addClass('hiddenx');
                    errortips(data);
                }
            });
        }
    },
    lotteryChartImg:{
        imgBox:'.Html2ImageBox',
        btnObj:null,
        chartIframe:function (obj, code, imgBox) {
            var thisx = Levme.lotteryChartImg;
            thisx.btnObj = obj;
            imgBox && (thisx.imgBox = imgBox);
            var src = levToRoute('lotterys/charts/zs', {editor:1, code:code});
            openziframescreenx(obj, src);
            return false;
        },
        setChartImg:function (src, data) {
            var thisx = Levme.lotteryChartImg;
            if (src) {
                var img = '<img src='+data.localfile+'>';
                typeof window.editor !== "undefined" && window.editor.insertHtml(img);
                jQuery(thisx.imgBox).append('<img class=zsImg src='+data.localfile+'>');
                jQuery(thisx.btnObj).parent().append('<input type="hidden" class="__zsImgSrcs" name="__zsImgSrcs[]" value="'+ data.file +'">');
            }
            Levme.onClick('img.zsImg', function () {
                var obj = this;
                var idx = jQuery(obj).index();
                Levme.confirm('', '删除图片吗？' + idx, function () {
                    jQuery(obj).remove();
                    jQuery('input.__zsImgSrcs').eq(idx).remove();
                });
            });
            myApp.closeModal('.ziframescreen.login-screen');
        },
    },
    marqueeIng:{},
    marquee:function(boxCls, liCls, _h, _n, _speed, _delay, ckH){
        if (Levme.marqueeIng[boxCls]) return 1;
        Levme.marqueeIng[boxCls] = 1;

        liCls = liCls === undefined ? 'li' : liCls;

        var t = null;
        var box = boxCls;
        var boxLi = box + ' '+ liCls;
        var h = _h ? _h : jQuery(boxLi).height();
        var n = _n ? _n : 1;
        var speed = _speed ? _speed : 500;
        var delay = _delay ? _delay : 2000;
        var liLen = jQuery(boxLi).length;
        if (liLen <2) return false;
        if (ckH && jQuery(box).height() >= liLen * jQuery(boxLi).height()) return false;
        jQuery(box).hover(function(){
            clearTimeout(t);
            t = null;
        }, function(){
            t = window.setTimeout(function(){_start(box, h, n, speed)}, delay);
        }).trigger('mouseout');

        //window.setTimeout(function(){_start(box, h, n, speed)}, delay);

        function _start(box, h, n, speed){
            if (t === null || jQuery(box).length <1 || jQuery(box).data('clear')) {
                jQuery(box).removeAttr('data-clear');
            }else {
                t = window.setTimeout(function(){ clearTimeout(t); _start(box, h, n, speed); }, delay);
            }
            jQuery(box).children().eq(0).animate({marginTop: '-='+h}, speed, function(){
                jQuery(this).css({marginTop:'0'}).find(liCls).slice(0,n).appendTo(this);
            })
        }

    },
    jQuery:function (cls, obj) {
        if (obj === 2) return cls;
        cls = typeof cls === "object" //jQuery.isPlainObject(cls)
            ? jQuery(Levme.conf.showPageContainer).find(cls)
            : Levme.conf.showPageContainer +' '+ cls;
        return !obj ? jQuery(cls) : cls;
    },
    tabs:{
        tabShow: {
            loadeds:{},
            ajaxDatas: {},
            virtuallistLoadBoxDatas: {},
            TabDatas:function (tabid, html, virtual) {
                var dkey = Levme.jQuery('').data('page');
                if (virtual) {
                    typeof Levme.tabs.tabShow.virtuallistLoadBoxDatas[dkey] === "undefined" &&
                    (Levme.tabs.tabShow.virtuallistLoadBoxDatas[dkey] = {});
                    if (html === undefined) {
                        return Levme.tabs.tabShow.virtuallistLoadBoxDatas[dkey][tabid];
                    }
                    Levme.tabs.tabShow.virtuallistLoadBoxDatas[dkey][tabid] = html;
                } else {
                    typeof Levme.tabs.tabShow.ajaxDatas[dkey] === "undefined" &&
                    (Levme.tabs.tabShow.ajaxDatas[dkey] = {});
                    if (html === undefined) {
                        return Levme.tabs.tabShow.ajaxDatas[dkey][tabid];
                    }
                    Levme.tabs.tabShow.ajaxDatas[dkey][tabid] = html;
                }
            },
            DataForce:function (tabId) {
                if (Levme.tabs.tabShow.loadeds[tabId]) {
                    Levme.tabs.tabShow.loadeds[tabId] = 0;//console.log('repeat12');
                    //window.setTimeout(function () { myApp.initImagesLazyLoad('.page-content'); }, 103);
                    return;
                }
                Levme.tabs.tabShow.loadeds[tabId] = 1;
                tabId = tabId ? tabId : jQuery(jQuery('.nav-links .tab-link.active').data('tab')).data('id');
                var obj = jQuery('.nav-links .tab-link.tabBtn_'+ tabId);
                if (obj.length >0) {
                    showIconLoader(true);
                    obj.attr('force', 1);
                    jQuery('.tabs .tab.active').removeClass('active');
                    myApp.showTab(obj.data('tab'), false);
                    window.setTimeout(function () { obj.removeAttr('force'); }, 800);
                }
            },
            getTabId:function (tabid) {
                tabid = tabid ? tabid : Levme.jQuery('.mbTabsBox').data('tabid');

                var thisId = jQuery(Levme.conf.showPageContainer).attr('thisShowTabid');
                thisId && jQuery(Levme.conf.showPageContainer).find('.nav-links .tab-link.tabBtn_'+ thisId).length >0 &&
                (tabid = thisId);
                return tabid;
            },
            init:function(deTabId) {
                var ajaxDatas = {}, virtuallistLoadBoxDatas = {};
                var navBoxWidth = jQuery('.nav-links').width();

                deTabId = Levme.tabs.tabShow.getTabId(deTabId);
                var obj = Levme.jQuery('.nav-links .tab-link.tabBtn_'+ deTabId);
                if (obj.length >0) { //showIconLoader(true);
                    window.setTimeout(function () { //myApp.showTab(obj.data('tab'));
                        Levme.tabs.tabShow.DataForce(deTabId);
                    }, 100);
                }

                jQuery(document).off('tab:show', '.tabs .tab').on('tab:show', '.tabs .tab', function () {
                    var objx = this;
                    setTabShowData(objx);
                    Levme.jQuery('.tabs').removeClass('hiddenx');
                    Levme.jQuery('.initLoader.preloader').remove();
                    jQuery(objx).find('.page-content-inner').show();
                    jQuery(objx).find('.initLoader.preloader').hide();
                    jQuery(objx).find('img.lazy').each(function () {
                        jQuery(this).attr('src', jQuery(this).data('src'));
                    });
                    jQuery(objx).find('.toolbar').length ?
                        hideToolbar('.toolbar.common-toolbarx') :
                        showToolbar('.toolbar.common-toolbarx');
                    window.setTimeout(function () {
                        myApp.initImagesLazyLoad('.page-content');
                    }, 480);
                    return false;
                });
                jQuery(document).off('tab:hide', '.tabs .tab').on('tab:hide', '.tabs .tab', function () {
                    setTabHideData(this);
                    return false;
                });

                jQuery(document).off('refresh', '.pull-to-refresh-content.notReload')
                                .on('refresh', '.pull-to-refresh-content.notReload', function (e) {
                    var obj = '.tabs .tab.active';
                    var id = jQuery(obj).data('id');
                    var btnCls = '.tabBtn_' + id;
                    var url = jQuery(btnCls).data('url');

                    showIconLoader(true);
                    getData(url, id, true);
                    Levme.scrollLoad.setDefault('.tab-' + id);
                });

                function setTabShowData(tobj) {
                    var idx = jQuery(tobj).index();
                    var id = jQuery(tobj).data('id');
                    var btnCls = '.tabBtn_' + id;
                    var url = jQuery(btnCls).data('url');

                    jQuery(Levme.conf.showPageContainer).attr('thisShowTabid', id);

                    Levme.jQuery('.nav-links .tab-link.active').removeClass('active');
                    Levme.jQuery(btnCls).addClass('active');
                    levtoMaoCenter(btnCls, '.nav-links .data-table', 300);

                    getData(url, id, Levme.jQuery(btnCls).attr('force'));

                    (idx - 1) > 0 && Levme.jQuery('.tabs .tab:lt(' + (idx - 1) + ')').each(function () {
                        setTabHideData(this);
                    });
                    Levme.jQuery('.tabs .tab:gt(' + (idx + 1) + ')').each(function () {
                        setTabHideData(this);
                    });
                    !Levme.jQuery('.tabs .tab:eq(' + (idx - 1) + ') .listLoadBox').html() && setTabHideData('.tabs .tab:eq(' + (idx - 1) + ')', 1);
                    !Levme.jQuery('.tabs .tab:eq(' + (idx + 1) + ') .listLoadBox').html() && setTabHideData('.tabs .tab:eq(' + (idx + 1) + ')', 1);
                }

                function setTabHideData(tobj, show) {
                    var id = Levme.jQuery(tobj).data('id');
                    var obj = Levme.jQuery(tobj).find('.listLoadBox');
                    if (show) {
                        obj.html(Levme.tabs.tabShow.TabDatas(id));
                    } else {
                        var objBox = obj.find('.virtual-list');
                        if (objBox.html()) {
                            Levme.tabs.tabShow.TabDatas(id, objBox.html(), true);
                            objBox.html('');
                        }
                        if (jQuery.trim(obj.html())) {
                            Levme.tabs.tabShow.TabDatas(id, obj.html());
                            objBox.length <1 && obj.html('');
                        }
                    }
                }

                function getData(url, key, force) {
                    var tabKey = '.tabs .tab.tab-' + key;
                    var htmBox = Levme.jQuery(tabKey + ' .listLoadBox');
                    var keyData = Levme.tabs.tabShow.TabDatas(key);
                    if (!force && keyData) {
                        htmBox.html(keyData);
                        window.setTimeout(function () {
                            var objBox2 = htmBox.find('.virtual-list');
                            objBox2.html(Levme.tabs.tabShow.TabDatas(key, undefined, 1));
                            //myApp.initImagesLazyLoad(tabKey);
                        }, 101);
                        return '';
                    }
                    var listLoadBox = Levme.jQuery(tabKey).find('.listLoadBox');
                    var page = parseInt(listLoadBox.attr('page'));
                    if (!url || page < 0 || parseInt(listLoadBox.attr('not')) === 1) {
                        hideIconLoader();
                        return '';
                    }
                    !force && htmBox.html('<span class="preloader preloader-red"></span>');
                    jQuery.ajax({
                        url: url,
                        data: {_: Math.random()},
                        dataType: 'json',
                        type: 'get',
                        success: function (data) {
                            hideIconLoader();
                            if (data.message) {
                                levtoast(data.message);
                            }
                            var status = parseInt(data.status);
                            if (status > 0) {
                                Levme.tabs.tabShow.TabDatas(key, data.htms);
                                htmBox.html(data.htms);
                                data.htms === "" || data.not
                                    ? Levme.scrollLoad.setNothing(tabKey, data.message)
                                    : Levme.jQuery(tabKey).find('.loadStart.hiddenx').removeClass('hiddenx');

                                //window.setTimeout(function () {myApp.initImagesLazyLoad(tabKey);}, 101);
                            }
                            if (status <1 || data.not) {
                                Levme.scrollLoad.setNothing(tabKey, data.message);
                                htmBox.find('.preloader.preloader-red').hide();
                            }
                        },
                        error: function (data) {
                            hideIconLoader();
                            errortips(data);
                        }
                    });
                }

            }
        }
    },
    scrollLoad:{
        init:function(loadBox) {
            loadBox && parseInt(jQuery(loadBox).find('.listLoadBox').attr('not')) === 1 && Levme.scrollLoad.setNothing(loadBox);

            Levme.onClick('.loadStart', function () {
                var loadBox = Levme.jQuery(jQuery(this).data('box') + '.infinite-scroll', 1);
                if (jQuery(this).data('box')) {
                    Levme.scrollLoad.ajaxLoad(loadBox, true);//注意空格
                }else {
                    jQuery(this).html('缺少参数[data-box]，无法加载').attr('disabled', true);
                }
            });
        },
        setDefault:function (loadBox) {
            jQuery(loadBox).find('.listLoadBox').attr('page', "2");
            jQuery(loadBox).find('.loadStart').html('点击加载更多').removeClass('hiddenx').attr('disabled', false).removeAttr('disabled');
        },
        setNothing:function (loadBox, message) {
            jQuery(loadBox).find('.infinite-scroll-preloader').addClass('hiddenx');
            jQuery(loadBox).find('.listLoadBox').attr('page', "-1");
            jQuery(loadBox).find('.loadStart').removeClass('hiddenx').html(message ? message : '没有了').attr('disabled', true);
        },
        ajaxLoad:function (loadBox, force) {//list-block virtual-list
            var page = parseInt(jQuery(loadBox).find('.listLoadBox').attr('page'));
            page === -2 && (page = 2) && jQuery(loadBox).find('.listLoadBox').attr('page', page);
            var loadInfiniteAjaxForceIng = false;
            if (page === 2) {
                myApp.attachInfiniteScroll(jQuery(loadBox));//给指定的 HTML 容器添加无限滚动事件监听器
                jQuery(document).on('infinite', loadBox, function () {
                    if (!jQuery(loadBox).find('.infinite-scroll-preloader').hasClass('hiddenx')) {
                        return false;
                    }
                    page >= 2 && doInfiniteData();
                });
            }
            if (force) {
                if (loadInfiniteAjaxForceIng) {
                    showIconLoader(true);
                    return false;
                }
                loadInfiniteAjaxForceIng = true;
                doInfiniteData();
                jQuery(loadBox).find('.infinite-scroll-preloader').removeClass('hiddenx');
                return false;
            }
            function doInfiniteData() {
                var url = jQuery(loadBox).find('.listLoadBox').attr('url');
                var virtualListBox = jQuery(loadBox).find('.listLoadBox .virtual-list');
                virtualListBox = virtualListBox.length <1 ? jQuery(loadBox).find('.listLoadBox.virtual-list') : virtualListBox;
                page = parseInt(jQuery(loadBox).find('.listLoadBox').attr('page'));
                page = isNaN(page) ? 0 : page;
                if (page < 2) return true;
                if (!url) {
                    Levme.scrollLoad.setNothing(loadBox, '未设置[data-url]');
                    return true;
                }
                jQuery(loadBox).find('.infinite-scroll-preloader').removeClass('hiddenx');
                jQuery.ajax({
                    url: url,
                    data:{page:page, infinite:1, _csrf:_csrf, _:Math.random()},
                    dataType:jQuery(loadBox).find('.listLoadBox').attr('jsonp') ? 'jsonp' : 'json',
                    type:'get',
                    success:function(data){
                        loadInfiniteAjaxForceIng = false;
                        jQuery(loadBox).find('.infinite-scroll-preloader').addClass('hiddenx');
                        if (data && data.message) {
                            levtoast(data.message, 5000);
                        }
                        if (parseInt(data.status) === -5) {
                            openLoginScreen();
                        }else if (parseInt(data.status) >0 && data.htms) {
                            jQuery(loadBox).find('.listLoadBox').attr('page', data.page ? data.page : page+1);
                            virtualListBox.length <1 ?
                                jQuery(loadBox).find('.listLoadBox').append('<div class="virtual-list">'+ data.htms +'</div>') :
                                virtualListBox.append(data.htms);
                            window.setTimeout(function () {myApp.initImagesLazyLoad(loadBox);}, 101);
                        }
                        if (data.status === -1 || data.not || jQuery.trim(data.htms) === "") {
                            Levme.scrollLoad.setNothing(loadBox, data.message);
                        }
                    },
                    error:function(data){
                        loadInfiniteAjaxForceIng = false;
                        jQuery(loadBox).find('.infinite-scroll-preloader').addClass('hiddenx');
                        errortips(data);
                    }
                });
            }
        },
    },
    closePP:function (animated) {
        myApp.closeModal('.popup', animated);
        jQuery('.popup-overlay').remove();
    },
    popupAjax:{//无刷新实时记录更新弹窗
        htms:{},
        showv:function (url, clsname, fullScreen) {
            Levme.closePP(false);
            var box = '.LevPopupMain.'+ clsname + ' .page';
            clsname = clsname ? clsname : '';
            if (clsname && clsname.indexOf('Live') <0) {
                if (Levme.popupAjax.htms[clsname]) {
                    Levme.popupAjax.openPopupActJs(box, Levme.popupAjax.htms[clsname]);
                    return;
                }
            }
            var full = fullScreen ? ' tablet-fullscreen' : '';
            var rtbs = fullScreen === 100 ? 'z-index:911001;' : 'max-height:calc(100% - 40px);z-index:911001;';
            showIconLoader(true);
            Levme.ajaxv.getv(changeUrlArg(url, 'ziframescreen', 5), function (data, status) {
                hideIconLoader();
                var Htms = data.htms ? data.htms : data.message;
                var ppupHtml = '<div class="popup LevPopupMain '+clsname+full+'" style="'+rtbs+'">' +
                    '<div class="view"><div class="page">'+ Htms +'</div></div></div>';
                clsname && status >0 && (Levme.popupAjax.htms[clsname] = ppupHtml);
                Levme.popupAjax.openPopupActJs(box, ppupHtml);
            });

            clsname &&
            jQuery(document).off('close', '.LevPopupMain.'+clsname).on('close', '.LevPopupMain.'+clsname, function(){
                Levme.popupAjax.htms[clsname] =
                    '<div class="popup LevPopupMain '+clsname+full+'" style="'+rtbs+'">' + jQuery(this).html() + '</div>';
            });
        },
        openPopupActJs:function (box, htm) {
            myApp.popup(htm);
            window.setTimeout(function () {
                jQuery(box).html(jQuery(box).html());//载入js
            }, 400);
        }
    },
    popupIframe:{
        config:{},
        ppBox:'',
        iframeBox:'',
        init:function () {
            var clsName = 'LevPopupIframeMain';
            var ppBox = '.' + clsName;
            var open = '.openPP';
            var iframeBox = ppBox + ' iframeBox';
            Levme.popupIframe.ppBox = ppBox;
            Levme.popupIframe.iframeBox = iframeBox;

            Levme.onClick(open, function(){
                myApp.closeModal(ppBox, false);
                Levme.popupIframe.popupShow(this);
                return false;
            });
            Levme.onClick('.ppBack, .ppClose', function(){
                myApp.closeModal(ppBox, false);
            });
            Levme.onClick('.closePP', function(){
                myApp.closeModal('.popup', true);
                jQuery('.popup-overlay').remove();
            });

            Levme.onClick('.popupFullBtn', function () {
                var obj = jQuery('.popup.modal-in');
                obj.length <1 && (obj = parent.jQuery('.popup.modal-in'));
                obj.hasClass('tablet-fullscreen') ? obj.removeClass('tablet-fullscreen') : obj.addClass('tablet-fullscreen');
            });

            Levme.copyright.act();
        },
        popupShow:function (obj, src, title, fullScreen, reload) {
            if (obj) {
                src = src ? src : jQuery(obj).data('src');
                src = src ? src : jQuery(obj).attr('href');
                src = src ? src : (jQuery(obj).attr('routev') ? levToRoute(jQuery(obj).attr('routev')) : 0);
                title = title ? title : (jQuery(obj).data('title') || jQuery(obj).attr('title'));
                fullScreen = fullScreen ? fullScreen : jQuery(obj).data('full');
                reload = reload ? reload : jQuery(obj).data('reload');
            }
            if (!src) {
                return true;
            }else if (jQuery(obj).attr('clsname') || src.indexOf('?inajax=') > 0 || src.indexOf('&inajax=') > 0) {
                var clsname = jQuery(obj).attr('clsname');
                return Levme.popupAjax.showv(src, clsname ? clsname : base64EncodeUrl(src), parseInt(jQuery(obj).attr('full')));
            }
            myApp.closeModal('.popup', false);

            var hide = '', style = '', full = '';
            if (!title) {
                hide = ' hiddenx';
                style = 'padding:0 !important;';
            }
            if (fullScreen) {
                full = ' tablet-fullscreen';
            }
            src = changeUrlArg(src, 'ziframescreen', full ? 3 : 2);
            var rtbs = 'max-height:calc(100% - 40px);z-index:911001;';//'border-radius: 0 30px 0 0;';
            var iframe = '<iframe width=100% height=100% frameborder=0 marginheight=0 src='+ src +'></iframe>';
            var ppupHtml = '<div class="popup LevPopupIframeMain'+full+'" style="'+rtbs+'">' +
                '<div class="view"><div class="page">' +
                '<div class="navbar navbar-bgcolor-red'+hide+'">' +
                '<div class="navbar-inner">' +
                '<div class="left"><a class="ppBack link icon-only scale9"><svg class="icon"><use xlink:href="#fa-back"></use></svg></a></div>' +
                '<div class="center">'+title+'</div>' +
                '<div class="right"><a class="popupFullBtn link icon-only"><svg class="icon"><use xlink:href="#fa-datac"></use></svg></a>' +
                '<a class="ppClose link icon-only scale9"><svg class="icon"><use xlink:href="#fa-close"></use></svg></a></div></div>' +
                '</div>' +
                '<div class="page-content" style="margin:0 !important;'+style+'overflow:hidden !important;">' +
                '<iframeBox class="content-block" style="margin:0;padding:0;"></iframeBox>' +
                '</div>' +
                '</div></div>' +
                '</div>';
            myApp.popup(ppupHtml);

            showIconLoader(true);
            jQuery(document).off('opened', Levme.popupIframe.ppBox).on('opened', Levme.popupIframe.ppBox, function(){
                hideIconLoader();
                jQuery(Levme.popupIframe.iframeBox).html(iframe);
            });

            jQuery(document).on('close', Levme.popupIframe.ppBox, function(){
                if (reload) {
                    window.location.reload();
                }
            });

            return false;
        }
    },
    mainView:{},
    conf: {
        pageBack:0,
        pageReinit:0,
        showPageing:null,
        showPageContainer:'.pages .page',
        showPageContainerCls:function () {
            if (Levme.conf.showPageing) {
                return '.pages .' + jQuery(Levme.conf.showPageing.container).attr('class').replace(/ /g, '.');
            }
            return Levme.conf.showPageContainer;
        },
        init:function () {
            //ajax page 调用
            jQuery(document).on('page:afteranimation', function (e) {
                Levme.conf.showPageing = e.detail.page;
                window.setTimeout(function () {
                    Levme.conf.showPageContainer = Levme.conf.showPageContainerCls();
                    var _loadPageAjaxJS = jQuery(Levme.conf.showPageContainer).find('.LoadPageAjaxJS');
                    if (_loadPageAjaxJS.length >0) {//加载JS
                        _loadPageAjaxJS.each(function () {
                            //jQuery(this).html(jQuery(this).prop("outerHTML"));
                            jQuery(this).html(jQuery(this).html());
                        })
                    }
                    window.setTimeout(function () { Levme.showNotices(); }, 102);
                    Levme.tabs.tabShow.init();
                    Levme.scrollLoad.init();
                }, 101);
            });

            jQuery(document).on('page:afterback', function () {
                Levme.conf.pageBack = 1;
            });

            jQuery(document).on('page:reinit', function () {
                Levme.conf.pageReinit = 1;
            });

            Levme.onClick('.ajaxBtn', function () {
                var url = jQuery(this).attr('href');
                var confirmmsg = jQuery(this).attr('confirmmsg');
                if (confirmmsg) {
                    myApp.confirm(confirmmsg, '', function () {
                        ajaxBtnFunc(url);
                    })
                }else {
                    ajaxBtnFunc(url);
                }
                return false;
            });
            function ajaxBtnFunc(url) {
                if (!url) return;

                Levme.ajaxv.getv(url, function (json, status) {
                    if (json.confirmurl) {
                        myApp.confirm(json.message, '', function () {
                            Levme.ajaxv.getv(json.confirmurl, function (data, status) {
                            }, {confirmdoit:1});
                        });
                        return false;
                    }
                    json.message && levtoast(json.message);
                    status === -5 && openLoginScreen();
                    json.tourl && (window.location = json.tourl);
                });
            }

            Levme.onClick('.setBigBox', function () {
                var box = jQuery(this).parents('.card-footer').eq(0);
                var obj = box.find('.bigObjx');
                if (obj.hasClass('bigBox')) {
                    obj.removeClass('bigBox');
                    jQuery(this).html('<absxk>放大</absxk>');
                    box.find('.hint-block').show();
                }else {
                    obj.addClass('bigBox');
                    jQuery(this).html('<absxg>缩小</absxg>');
                    box.find('.hint-block').hide();
                }
            });

//开关按钮设置值，将checkbox 当radio使用
            jQuery(document).on('change', '.setToggleValue input[type=checkbox]', function () {
                var ckval = jQuery(this).parents('.setToggleValue').eq(0).find('input[type=hidden]').val();
                ckval = ckval ==="" || parseInt(ckval) ===0 || !ckval ? 1 : 0;
                if ( jQuery(this).is(':checked') ) {
                    jQuery(this).parents('.setToggleValue').eq(0).find('input[type=hidden]').val(ckval);
                }else {
                    jQuery(this).parents('.setToggleValue').eq(0).find('input[type=hidden]').val(ckval);
                }
            });

            Levme.mainView = myApp.addView('.view-main', {
                // Because we use fixed-through navbar we can enable dynamic navbar
                //dynamicNavbar: true,
                // Enable Dom Cache so we can use all inline pages
                //domCache: true
            });

            //Pull to refresh content 下拉刷新
            jQuery(document).on('pullstart', '.pull-to-refresh-content', function (e) {
                jQuery('.pull-to-refresh-layer .pull-to-refresh-arrow').show();
                window.setTimeout(function () {
                    myApp.pullToRefreshDone();
                    jQuery('.pull-to-refresh-layer .pull-to-refresh-arrow').hide();
                }, 2000);
            });
            jQuery(document).on('refresh', '.pull-to-refresh-content', function (e) {
                if (!jQuery(this).hasClass('notReload')) {
                    showIconLoader();
                    window.setTimeout(function () {
                        //window.location.href = window.location.href;
                        window.location.reload();
                        myApp.pullToRefreshDone();
                        jQuery('.pull-to-refresh-layer .pull-to-refresh-arrow').hide();
                    }, 1000);
                }
            });

            Levme.onClick('a, ax, input[type="button"]', function(){
                if (jQuery(this).hasClass('openPP')) return true;

                var _thrf = jQuery(this).attr('href');
                if (jQuery(this).hasClass('is_ajax_a')) {
                    myApp.closeModal('.login-screen', false);
                }
                var aonclick = jQuery(this).attr('onclick');
                if (aonclick && aonclick.indexOf('showWindow') !=-1) return false;
                var _bk = jQuery(this).attr('_bk');
                if (!_bk && _thrf) {
                    if (typeof (siteUri) != 'undefined' && (_thrf + '/').indexOf(siteUri) == -1 && _thrf.indexOf('http') == 0) {
                        return aToLoginScreenForce(this, _thrf);
                    }
                    if (_thrf.indexOf('javascript:') < 0 && jQuery(this).attr('target') == "_blank") {
                        return aToLoginScreenForce(this, _thrf);
                    }
                }

            });

            Levme.onClick('ax, input[type="button"]', function(){
                if (jQuery(this).hasClass('openPP')) return true;
                var _thrf = jQuery(this).attr('href');
                if (jQuery(this).hasClass('is_ajax_a')) {
                    Levme.mainView.router.loadPage(_thrf);
                    return false;
                }else if (_thrf) {
                    window.location = _thrf;
                }
            });

            Levme.onClick('.closescreen', function(){
                Levme.closePP(false);
                myApp.closeModal('.login-screen,.login_screen');
            });

            Levme.onClick('.editField', function () {
                var pobj   = this;
                var url    = jQuery(pobj).attr('href');
                var opname = jQuery(pobj).attr('opname');
                var opval  = jQuery(pobj).attr('opval');
                myApp.prompt(jQuery(pobj).attr('title') || '修改:'+ opname, function (opval) {
                    jQuery('.modal-inner .input-field inputs input').each(function () {
                        url = changeUrlArg(url, this.name, this.value || '');
                    });
                    Levme.ajaxv.getv(changeUrlArg(url, opname, opval), function (data, status) {
                        status >0 && jQuery(pobj).parent().find(opname).html(opval);
                        data.tourl && window.setTimeout(function () {window.location = data.tourl}, 1000);
                    });
                });
                window.setTimeout(function () {
                    jQuery('.modal-inner .modal-text-input').val(opval);
                    jQuery(pobj).find('inputs').length >0 &&
                    jQuery('.modal-inner .input-field').append('<inputs>'+ jQuery(pobj).find('inputs').html() + '</inputs>');
                    jQuery('.modal-inner .input-field inputs input').addClass('modal-text-input');
                }, 500);
                return false;
            });
        },
        onClick:function (cls, func) {
            jQuery(document).off('click', cls).on('click', cls, func);
        },
    },
    tablesnav:{
        init:function () {
            Levme.tablesnav.addTabTr();
            Levme.tablesnav.openChild();
            Levme.tablesnav.addChild();
            Levme.tablesnav.delChild();
            Levme.onClick('.bigvBtn', function () {
                Levme.tablesnav.setBigv(jQuery(this).parents('tabbox'));
            });
            actionLocalStorage('tabboxBigv') && jQuery('tabbox').addClass('bigv');

            Levme.tablesnav.smartSelect();
        },
        smartSelect:function () {
            var smartSelectObj = null;
            var popupBox = '.popup.smart-select-popup ';
            jQuery(document).on('keyup', popupBox+' input[type=search]', function (e) {
                var val = this.value;
                var key = e.keyCode ? e.keyCode : e.which;
                if (this.value && key == '13') {
                    var trid = '.trid-'+jQuery(popupBox+' hiddenx idk').html();
                    var inputName = jQuery(popupBox+' hiddenx na').html();//console.log(inputName);

                    var opt = jQuery(smartSelectObj).find('select optgroup').length >0
                        ? jQuery(smartSelectObj).find('select optgroup').last()
                        : jQuery(smartSelectObj).find('select');
                    myApp.smartSelectAddOption(opt, '<option value="'+val+'" selected>'+val+'</option>');
                    jQuery(smartSelectObj).find('select[name="'+inputName+'"]').val(val);//console.log(val);
                    jQuery(smartSelectObj).find('.item-after').html(val);
                    Levme.showNotices('已设置为['+ val +'] 保存后生效', 'success', 1500);
                }
            });
            Levme.onClick('.smart-select', function () {
                smartSelectObj = this;
            })
        },
        setBigv:function (objBox) {
            if ( objBox.hasClass('bigv') ) {
                objBox.removeClass('bigv');
                actionLocalStorage('tabboxBigv', 0, 1);
            } else {
                objBox.addClass('bigv');
                actionLocalStorage('tabboxBigv', 1);
            }
        },
        getMaxId:function (obj) {
            var maxNum = 0;
            jQuery(obj).each(function () {
                var num = parseInt(this.value);
                num = isNaN(num) ? 0 : num;
                maxNum = num > maxNum ? num : maxNum;
            });
            return maxNum;
        },
        addTabTr:function () {
            Levme.onClick('.addTabTr', function () {
                var inId = Levme.tablesnav.getMaxId(jQuery(this).parents('tabbox').find('td.fd-id input')) +1;

                var inputname = jQuery(this).data('input');
                var trBox = '.newTabTrBox-'+ inputname;
                var trCon = jQuery(this).parents('.myAddTrx').eq(0).html();
                trCon && (trCon = trCon.replace(/__addtr\[/g, '[').replace(/\[\]\[\]/g, '[idk___'+inId+'][]'));
                jQuery(trBox).append('<tr>'+trCon+'</tr>');
                jQuery(trBox+ ' tr').last().find('td').last().html('<a class="date delTr">x</a>');

                jQuery(trBox+ ' tr').last().find('td.fd-id input').val(inId);
                jQuery(trBox+ ' tr').last().find('td.fd-order input').val(inId);
            });
        },
        openChild:function () {
            Levme.onClick('.tablesnavOpenChildBtn', function () {
                var cls = jQuery(this).data('cls');
                var obj = jQuery('.childBox_'+ cls);
                obj.hasClass('hiddenx') ? obj.removeClass('hiddenx') : obj.addClass('hiddenx');
            });
        },
        addChild:function () {
            Levme.onClick('.tablesnavAddChildBtn', function () {
                var pId = Levme.tablesnav.getMaxId(jQuery(this).parents('tabbox').find('td.fd-id input'));
                var inId = pId +1;

                var cls     = jQuery(this).data('cls');
                var obj     = '.childBox_'+ cls;
                var trBox   = jQuery('.navBox_'+ cls);
                var trCon   = trBox.html();
                trCon && (trCon = trCon.replace(/__addtr\[/g, '[')
                    .replace(/tables\[/g, 'tables[cld__'+ cls +'__')
                    .replace(/tablesFormv\[/g, 'tablesFormv[cld__'+ cls +'__')
                    .replace(/\[\]\[\]/g, '[idk___'+inId+'][]')
                    .replace(new RegExp("\\[idk___" + trBox.data('idk') +"\\]\\[\\]", 'g'), '[idk___'+inId+'][]'));
                jQuery(obj + '.Navsx').append('<tr class="add-c">'+trCon+'</tr>');
                jQuery(obj + '.Navsx tr').last().find('td').eq(0).html('<p class="date">&angrt;</p>');

                jQuery(obj + '.Navsx tr').last().find('td.fd-id input').eq(0).val(inId);
                jQuery(obj + '.Navsx tr').last().find('td.fd-order input').eq(0).val(inId);
            });
        },
        delChild:function () {
            Levme.onClick('.delTr', function () {
                var cls = jQuery(this).data('cls');
                jQuery(this).parents('tr').eq(0).remove();
                if (jQuery(this).parents('tr.navBox_' + cls).length > 0) {
                    jQuery('tbody.childBox_'+ cls).remove();
                }
            });
        }
    },
    dosaveForm:{
        inited:false,
        init:function () {
            !Levme.dosaveForm.inited && (Levme.dosaveForm.inited = 1) &&
            Levme.onClick('.dosaveFormBtn', function () {
                return Levme.dosaveForm.dosubmit(jQuery(this).parents('#saveForm').eq(0));
            });
        },
        dosubmit:function (formObj, func) {
            formObj = formObj ? formObj : '#saveForm';
            showIconLoader(true);
            jQuery(formObj).ajaxSubmit({
                url: jQuery(formObj).attr('action') ? jQuery(formObj).attr('action') : '',
                data: {dosubmit:1, _csrf:_csrf, inajax:1, _:Math.random()},
                type:'post',
                dataType: 'json',
                success: function(data){
                    hideIconLoader();
                    var status = parseInt(data.status);
                    if (status >0) {
                        levtoast(data.message);
                        !func && !data.notReload &&
                        window.setTimeout(function () {
                            data.tourl ?
                                (data.local ? window.location = data.tourl : window.top.location = data.tourl) :
                                (window.location = window.location);
                        }, 400);
                    }else if (data && data.message) {
                        levtoast(data.message, 15000);
                    }
                    showFormErrors(data.errors);

                    typeof func === "function" && func(data, status);
                },
                error: function(data) {
                    hideIconLoader();
                    errortips(data);
                }
            });
            return false;
        }
    },
    openWX: function () {
        var appObj = ckAPP();
        if (appObj !== false) {
            appObj.openWX();
        }else {
            window.location = 'weixin://';
        }
    },
    openQQ: function () {
        var appObj = ckAPP();
        if (appObj !== false) {
            appObj.openQQ();
        }else {
            window.location = 'mqq://';
        }
    },
    openAlipay: function () {
        var appObj = ckAPP();
        if (appObj !== false) {
            //appObj.openAlipay();
            appObj.openAlipayQrcode("");
        }else {
            window.location = 'alipays://';
        }
    },
    checkXssCode:function (a) {
        a += "";
        return a.indexOf('<') >=0 || a.indexOf('>') >=0 || a.indexOf('(') >=0 || a.indexOf(')') >=0 || a.indexOf('"') >=0 || a.indexOf("'") >=0;
    },
    encodeHTML: function(a){
        a += "";
        return a.replace(/&/g, "&0amp;").replace(/</g, "&0lt;").replace(/>/g, "&0gt;").replace(/"/g, "&0quot;").replace(/'/g, "&0apos;")
            .replace(/\(/g, '&99z;').replace(/\)/g, '&00z;');
    },
    decodeHTML: function(a){
        a += "";
        return a.replace(/&amp;/g, "&").replace(/&0amp;/g, "&").replace(/&0lt;/g, "<").replace(/&0gt;/g, ">").replace(/&0quot;/g, '"').replace(/&0apos;/g, "'")
            .replace(/&99z;/g, '(').replace(/&00z;/g, ')');
    },
    iconfonts:function () {
        var icons = {};
        jQuery('svg:not(.icon) symbol').each(function (e) {
            icons[this.id] = this.id;
        });
        return icons;
    },
    iconSelectWin:function (func, btnObj) {
        var icons = Levme.iconfonts();
        var htm = '<div class="iconSelectWinBox flex-box" style="flex-wrap:wrap;height:100px;width:300px;overflow:auto;">';
        for (var k in icons) {
            htm += '<a style="margin:5px;color:#fff;" title="'+icons[k]+'"><svg class="icon" style="font-size:18px"><use xlink:href="#'+icons[k]+'"></use></svg></a>';
        }
        htm += '</div>';
        Levme.confirm(htm,'', function () {
            var value = jQuery('.iconSelectWinBox a.icond').attr('title');
            jQuery(btnObj).parent().find('input').last().val(value);
            if (func) {
                if (typeof func === "function") {
                    func(value);
                }else {
                    jQuery(func).val(value);
                }
            }
        });
    },
    iconSelectWinBtnReg:function (func) {
        Levme.onClick('.iconSelectWinBox a', function () {
            jQuery('.iconSelectWinBox a').removeClass('icond');
            jQuery(this).addClass('icond');
            return false;
        });
        Levme.onClick('.iconSelectWinBtn', function () {
            Levme.iconSelectWin(func ? func : jQuery(this).data('funcname'), this);
        });
    },
    disableBack:function () {
        //禁止后退页面
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });
    },
    checkbox:function (cls, func) {
        /*
        使用
        <label class="ckdbox childsCkbox opCkboxBtn" val="2">
            <svg class="icon icon-ckbox white"><use xlink:href="#fa-ckd"></use></svg>&nbsp;
            全部安装
        </label>
        <svg class="icon icon-ckbox childsCkbox" val="1"><use xlink:href="#fa-ckd"></use></svg>
        */
        Levme.onClick(Levme.jQuery(cls, 1), function () {
            var ckd = jQuery(this).hasClass('ckd');
            if (jQuery(this).hasClass('opCkboxBtn')) {
                !ckd ? Levme.jQuery(cls).addClass('ckd') : Levme.jQuery(cls).removeClass('ckd');
            }
            if (ckd) {
                jQuery(this).removeClass('ckd');
            }else {
                jQuery(this).addClass('ckd');
            }

            typeof func === "function" && func(Levme.checkboxvals(cls), ckd, this);
        });
    },
    checkboxvals: function (cls) {
        var ckdvals = [];
        var val = Levme.jQuery(cls +'.opCkboxBtn').attr('val');
        val !== undefined && ckdvals.push(val);
        jQuery(cls +'.ckd').not('.opCkboxBtn').each(function (n) {
            var val = jQuery(this).attr('val');
            val !== undefined && ckdvals.push(val);
        });
        return ckdvals;
    },
    textSelect:function (inputname) {
        var opCls = Levme.jQuery('._'+ inputname + '_slt', 1);
        var onSlt = Levme.jQuery('.on'+ inputname + 'Slt', 1);

        jQuery(document).on('blur', onSlt, function () {
            var sval = this.value;
            if (sval === "") return;

            var sltd = false;
            var opClsBlur = jQuery(this).parents('.textSelectBox').find('.textSelectSlt');
            opClsBlur.find('option').each(function () {
                if (sval === jQuery(this).text()) {
                    sltd = true;
                    sval = jQuery(this).attr('value');
                    return null;
                }
            });
            if (!sltd) {
                if (opClsBlur.find('.myselfop').length > 0) {
                    opClsBlur.find('.myselfop').attr('value', sval).html(sval);
                }else {
                    jQuery(opClsBlur).append('<option class="myselfop red" value="' + sval + '" selected>' + sval + '</option>');
                }
            }
            jQuery(opClsBlur).val(sval);
        });

        jQuery(document).on('change', opCls, function () {
            setonSlt(jQuery(this).find('option:selected').text(), jQuery(this).parents('.textSelectBox').find('.textSelectTxt'));
        });

        !jQuery(onSlt).val() && jQuery(opCls).find('option:selected').text() &&
        setonSlt(jQuery(opCls).find('option:selected').text());
        function setonSlt(sval, obj) {

            jQuery(obj ? obj : onSlt).val(sval);
        }
    },
    script: {
        loaded:{},
        check:function (key) {
            if (Levme.script.loaded[key]) return true;
            Levme.script.loaded[key] = 1;
            return false;
        },
        ClipboardJS:function (func) {
            if (Levme.script.check('ClipboardJS')) return true;

            typeof func !== "function" && (func = function () {});
            typeof ClipboardJS === "undefined"
                ? jQuery.getScript(assets + '/statics/common/clipboard.min.js', function () { func(); }) : func();
        },
        QRCode:function (func) {
            if (Levme.script.check('QRCode')) return true;

            typeof func !== "function" && (func = function () {});
            typeof QRCode === "undefined"
                ? jQuery.getScript(assets + '/statics/common/qrcode.min.js', function () { func(); }) : func();
        },
        Highcharts:function (func) {
            if (Levme.script.check('Highcharts')) return true;

            typeof func !== "function" && (func = function () {});
            typeof Highcharts === "undefined"
                ? jQuery.getScript(assets + '/statics/common/highstock.js', function () { func(); }) : func();
        },
    },
    clipboardv:function (cls) {
        cls = cls || '.copyBtn';
        Levme.script.ClipboardJS(initCopy) && initCopy();

        function initCopy() {
            var clipboard = new ClipboardJS(cls, {
                text: function(e) {
                    return jQuery(e).attr('copy-txt') || jQuery(jQuery(e).attr('copy-input')).val();
                }
            });

            clipboard.on('success', function(e) {
                Levme.showNotices('复制成功<br>'+ e.text);
            });

            clipboard.on('error', function(e) {
                levtoast('复制失败');
            });
        }

    },
    locationHref:function () {
        var href = window.location.href.split('#!/').pop();
        return href.indexOf('http') !== 0 ? homeUrl + href : href;
    },
    swiper:function (inputname) {
        var swiper = new Swiper('.slides-input-'+inputname+' .swiper-container', {
            speed: 800,
            spaceBetween: 10,
            //preloadImages: true,
            lazyLoading: true,
            lazyLoadingClass: 'lazy',
            pagination: '.slides-input-'+inputname+' .swiper-pagination',
            paginationClickable: true,
            autoplay: 5000,
            //slidesPerView: 2,
            //onSlideChangeEnd: function () {},
            // onSlideChangeStart: function () {
            //     window.setTimeout(function () { myApp.initImagesLazyLoad('.pages'); }, 201);
            // }
        });
    },
    copyright:{
        do:0,
        act:function () {
            if (!Levme.copyright.do) {
                Levme.copyright.do = 1;
                console.log('\n'.concat(' %c 爱路e维 v', '20200914', ' ').concat('Lev', ' %c https://levme.com ', '\n', '\n'), 'color: #fadfa3; background: #030307; padding:5px 0;', 'background: #dadfa3; padding:5px 0;');
            }
        }
    }
};

Levme.APP.init();
Levme.popupIframe.init();
Levme.tablesnav.init();
Levme.dosaveForm.init();
Levme.conf.init();
Levme.loginv.btns();
Levme.setups.btn();
Levme.sign.init();
Levme.clipboardv();
Levme.iconSelectWinBtnReg();
Levme.onClick('.form-group img', function () {
    Levme.photoShow(jQuery(this).attr('src'));
});

function APPmyPhoneLogin(phone) { Levme.APP.myPhoneLogin(phone); }

function base64EncodeUrl(str) { return window.btoa((str)).replace(/=/g, '').replace(/\//g, '_').replace(/\+/g, '-');}
function base64DecodeUrl(str) { return (window.atob(str.replace(/_/g, '/').replace(/-/g, '+'))); }

function loadInfiniteAjax(infBox, force) { return Levme.loadInfiniteAjax(infBox, force); }

function appNoticeLive(uid) { Levme.APP.appNoticeLive(uid); }

function ckAPP() { return Levme.APP.ckAPP(); }

function openHtml2imageWin(obj, code, imgBox) { return Levme.lotteryChartImg.chartIframe(obj, code, imgBox) }
function getHtml2image(src, data) { return Levme.lotteryChartImg.setChartImg(src, data) }

jQuery(function(){
    if (typeof(__mobile__moveFlag__) !='undefined') {
        jQuery(".draggable_btn").draggable({ cursor: "move",
            stop: function() {
                jQuery(this).css({'right':'unset', 'bottom':'unset'});
            }
        });//拖动
    }

    jQuery(document).on('click', '.swipeback-page-opacity', function(){
        jQuery('.swipeback-page-opacity').remove();
    });

    appNoticeLive(actionLocalStorage('UID'));

    window.setTimeout(function () { Levme.showNotices(); }, 101);
});

var levsignJs ={};
(function () {
    'use strict';

    jQuery(function () {
        levsignJs.init();
    });

    levsignJs = {
        config:{},
        init:function () {
            var d = new Date();//console.log(d.getDate());
        },
        signBtn:function (typeid) {
            Levme.sign.signBtn(typeid);
        },
    }

})();