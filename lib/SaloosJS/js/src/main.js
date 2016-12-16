// Code haye moshtarak beine tamame safahat
// Az jomle meqdar dehi haye avvalie
// va etesale roydad haye mokhtalef

$(document).ready(function()
{
  var regex = /(?:\?|&)?lang=\w*/g;
  if(regex.test(location.search)) {
    Navigate({
      url: location.pathname + location.search.replace(regex, '') + location.hash,
      replace: true
    });
  }

    /* Blur inputs with ESC key */
  $(document).keydown(function(e) {
    if(e.keyCode === 27) {
      $('input').blur();
    }
  });

  // Ajaxify links and forms
  $(document).on('submit', 'form', function(e)
  {
    if($(this).hasAttr('data-action')) return;

    e.preventDefault();
    $(this).ajaxify();

  });

  $(document).on('click', '[data-ajaxify]', function(e)
  {
    e.preventDefault();
    $(this).ajaxify({link: true});
  });


  // $(document).on('change', '#langlist', function() {
  //   var regex = /(?:\?|&)?lang=\w*/g;
  //   var srch = location.search.replace(regex, ''),
  //       url = location.pathname + (srch ? srch + '&' : '?') + 'lang='+this.value;

  //   location.replace(url);
  // });

  $(document).on('keypress', 'input[type="date"],\
                            input[type="datetime-local"],\
                            input[type="number"],\
                            input[type="tel"],\
                            input#mobile', function(e) {
                              if(this.getAttribute('data-allowpersian') !== null || e.which < 32) return;
                              e.preventDefault();

                              if(e.which === 32) return;

                              var val = '';
                              var key = String.fromCharCode(e.which);

                              if(e.which <= 1785 && e.which >= 1776) {
                                try {
                                  var start = this.selectionStart,
                                      end = this.selectionEnd;

                                  val = this.value.slice(0, start) + key.toEnglish() + this.value.slice(end);
                                } catch(e) {
                                  val = this.value + key.toEnglish();
                                }
                              } else {
                                val = this.value + key;
                              }

                              if(isNaN(+val)) {
                                $this = $(this);
                                $this.addClass('invalid');
                                setTimeout(function() {
                                  $this.removeClass('invalid');
                                }, 500);
                              } else {
                                this.value = val;
                              }
                              return false;
                            });



  // 'a:not([target="_blank"])\
  // :not([data-ajaxify])\
  // :not([data-action])\
  // :not([data-modal])',
  $(document.body).on('click', 'a', function(e)
  {
    var $this = $(this);

    if(
        $this.attr('target') === '_blank' ||
        $this.hasAttr('data-ajaxify') ||
        $this.hasAttr('data-action') ||
        $this.hasAttr('data-direct') ||
        $this.hasAttr('data-modal') ||
        $this.isAbsoluteURL()
      )
      {
        return;
      }

    e.preventDefault();

    if(!$this.attr('href') || $this.attr('href').indexOf('#') > -1) return;

    var href = $this.attr('href');

    if(href.indexOf('lang=') > -1) return location.replace(href);

    Navigate({
      url: href,
      fake: !!$this.attr('data-fake')
    });
  });
});


route('*', function()
{
  // $('input').prop('lang', 'en');
  /* MODALS */

  // Things to do after closing/opening modal
  $('.modal', this).on('close', function()
  {
    var $this = $(this);

    $this.removeClass('visible');

    $.each($this.data(), function(key)
    {
      if(key === 'modal') return;
      $(this).removeAttr(key);
    });
  });


  $('.modal', this).on('open', function()
  {
    $(this).addClass('visible');

    var $send = $('[data-ajaxify]', this);

    if (!$send.length) return;

    $.each($send.data(), function(key)
    {
      if(key === 'modal') return;

      $send.removeAttr(key);
    });

    $send.copyData(this, ['modal']);
  });


  $(".panel .panel-heading", this).click(
    function ()
    {
      var el = $(this).parent();
      if(el.hasClass('closed'))
      {
        el.children('.panel-footer').slideDown(300);
        el.children('.panel-body').slideDown(600, function(){
          el.removeClass('closed');
        });
        // el.children('.panel-body').fadeIn();
      }
      else
      {
        el.children('.panel-footer').slideUp(300);
        el.children('.panel-body').slideUp(500, function(){
          el.addClass('closed');
        });
        // el.children('.panel-body').hide();
      }
    }
  );


  // run watchScroll func to watch all elements
  watchScroll(this);

}).once(function(_module)
{
  // run only one time after changing url
  var hashtag     = $(window.location.hash);
  var hashtagFake = urlParam('hashtag')? $('#'+ urlParam('hashtag')): '';
  // handle scroll
  if(hashtag.length)
  {
    // after 0.5s of loading page scroll to specefic area if exist
    scrollSmooth(hashtag, null, 1000);
  }
  else if(hashtagFake.length)
  {
    scrollSmooth(hashtagFake, null, 1000);
  }
  else if(_module !== undefined)
  {
    findPushStateScroll();
  }
});


/**
 * [urlParam description]
 * @param  {[type]} _request [description]
 * @return {[type]}          [description]
 */
function urlParam(_request)
{
  var sPageURL = decodeURIComponent(window.location.search.substring(1)),
    sURLVariables = sPageURL.split('&'),
    sParameterName,
    i;
  var result = null;
  var params = [];

  // detect and set key and value into params
  for(i = 0; i < sURLVariables.length; i++)
  {
    var cParameter   = sURLVariables[i].split('=');
    var sParameterValue = cParameter[1] === undefined ? true : cParameter[1];
    params[cParameter[0]] = sParameterValue;
  }

  // if params is requested, return it
  // else return all of params into object
  if(_request)
  {
    if(params[_request])
    {
      result = params[_request];
    }
  }
  else
  {
    result = params;
  }

  return result;
};


/**
 * [watchScroll description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function watchScroll(_this)
{
  // watch simple links
  $('a[href*="#"]:not([href="#"])', _this).click(function()
  {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname)
    {
      var hash   = this.hash;
      var target = $(hash);
      target = target.length ? target : $('[name=' + hash.slice(1) +']');
      if (target.length)
      {
        scrollSmooth(target, hash);
        return false;
      }
    }
  });

  $('[data-scroll-to]', _this).click(function()
  {
    var hashtag = $(this).attr('data-scroll-to');
    var target  = $('#'+ hashtag);
    scrollSmooth(target, hashtag);
  });
}


/**
 * [findPushStateScroll description]
 * @return {[type]} [description]
 */
function findPushStateScroll()
{
  var target = $('[data-scroll]');
  scrollSmooth(target, null, 500);
}


/**
 * [scrollSmooth description]
 * @param  {[type]} _target  [description]
 * @param  {[type]} _hashtag [description]
 * @param  {[type]} _timing  [description]
 * @return {[type]}          [description]
 */
function scrollSmooth(_target, _hashtag, _timing)
{
  if(_target && _target.length == 1)
  {
    var diff   = Math.abs(document.body.scrollTop - _target.offset().top);
    var timing = Math.round(diff/3);

    if(_timing)
    {
      timing += 500;
      setTimeout(function()
      {
        scrollSmoothTo(_target, _hashtag, timing);
      }, 500);
    }
    else
    {
      scrollSmoothTo(_target, _hashtag, timing);
    }
  }
}


/**
 * [scrollSmoothTo description]
 * @param  {[type]} _target  [description]
 * @param  {[type]} _hashtag [description]
 * @param  {[type]} _timing  [description]
 * @return {[type]}          [description]
 */
function scrollSmoothTo(_target, _hashtag, _timing)
{
  if(_timing<300)
  {
    _timing = 300;
  }
  $('html, body').animate(
  {
    scrollTop: _target.offset().top - 10
  }, _timing, function()
  {
    if(_hashtag)
    {
      window.location.hash = _hashtag;
    }
  });
}