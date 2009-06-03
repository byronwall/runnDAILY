/*!
 * Facebox (for jQuery)
 * version: 1.2 (05/05/2008)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://famspam.com/facebox/
 *
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 */

(function($) {
  $.facebox = function(data, timeout) {
    $.facebox.loading()
    if (data.ajax) fillFaceboxFromAjax(data.ajax)
    else if (data.image) fillFaceboxFromImage(data.image)
    else if (data.div) fillFaceboxFromHref(data.div)
    else if ($.isFunction(data)) data.call($)
    else $.facebox.reveal(data)
    
    if(timeout){
    	$.facebox.close(timeout);
    }
  }

  /*
   * Public, $.facebox methods
   */

  $.extend($.facebox, {
    settings: {
    	dom_data: null,
    	dom: null,
    	modal: false,
      opacity      : .50,
      overlay      : true,
      loadingImage : '/img/loadingAnimation.gif',
      imageTypes   : [ 'png', 'jpg', 'jpeg', 'gif' ],
      faceboxHtml  : '\
	<div id="facebox" style="display:none;"> \
	  <div class="popup"> \
        <div class="content"> \
        </div> \
        <div class="footer"> \
          <a href="#" class="close"> \
            close \
          </a> \
        </div> \
      </div> \
    </div>'
    },

    loading: function() {
      init()
      if ($('#facebox .loading').length == 1) return true
      
      //prevents an error where the old contents are emptied if the facebox is not closed in between calls.      
      if($.facebox.settings.dom){
  		$($.facebox.settings.dom).append($.facebox.settings.dom_data);
  		
  		$.facebox.settings.dom = null;
  		$.facebox.settings.dom_data = null;
  	}
      
      showOverlay()

      $('#facebox .content').empty()
      $('#facebox .body').children().hide().end().
        append('<div class="loading"><img src="'+$.facebox.settings.loadingImage+'"/></div>')

      $('#facebox').css({
        top:	(getPageHeight() / 10),
        left:	385.5
      }).show()

      $(document).bind('keydown.facebox', function(e) {
        if (e.keyCode == 27) $.facebox.close()
        return true
      })
      $(document).trigger('loading.facebox')
    },

    reveal: function(data) {
      $(document).trigger('beforeReveal.facebox')
      $('#facebox .content').append(data)
      $('#facebox .loading').remove()
      $('#facebox .body').children().fadeIn('normal')
		$("#facebox .close").text("close").bind("click", $.facebox.close);
      
      $('#facebox').css('left', $(window).width() / 2 - ($('#facebox .popup').width() / 2))
      $(document).trigger('reveal.facebox').trigger('afterReveal.facebox')
      $.facebox.settings.isOpen = true;
    },

    close: function(delay) {
    	if(delay) $.facebox.settings.close_delay = delay;
      $(document).trigger('close.facebox')
      return false
    },
    clickHandler: function(settings) {
    	init(settings);
        $.facebox.loading(true);

        fillFaceboxFromHref(this.href);
        return false;
      }
  })

  /*
   * Private methods
   */

  // called one time to setup facebox on this page
  function init(settings) {
    if ($.facebox.settings.inited) return true
    else $.facebox.settings.inited = true

    $(document).trigger('init.facebox')

    var imageTypes = $.facebox.settings.imageTypes.join('|')
    $.facebox.settings.imageTypesRegexp = new RegExp('\.' + imageTypes + '$', 'i')

    if (settings) $.extend($.facebox.settings, settings)
    $('body').append($.facebox.settings.faceboxHtml)

    var preload = [ new Image() ]
    preload[0].src = $.facebox.settings.loadingImage
  }
  
  // getPageScroll() by quirksmode.com
  function getPageScroll() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;	
    }
    return new Array(xScroll,yScroll) 
  }

  // Adapted from getPageSize() by quirksmode.com
  function getPageHeight() {
    var windowHeight
    if (self.innerHeight) {	// all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }	
    return windowHeight
  }

  // Figures out what you want to display and displays it
  // formats are:
  //     div: #id
  //   image: blah.extension
  //    ajax: anything else
  function fillFaceboxFromHref(href) {
    $.facebox.settings.modal = href.match(/modal/)
    
    // div
    if (href.match(/#/)) {
      var url    = window.location.href.split('#')[0]
      var target = href.replace(url,'')
      $.facebox.settings.dom = target;
      $.facebox.settings.dom_data = $(target).children();
      $.facebox.reveal($(target).children().show())

    // image
    } else if (href.match($.facebox.settings.imageTypesRegexp)) {
      fillFaceboxFromImage(href, klass)
    // ajax
    } else {
      fillFaceboxFromAjax(href);
    }
  }

  function fillFaceboxFromImage(href, klass) {
    var image = new Image()
    image.onload = function() {
      $.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
    }
    image.src = href
  }

  function fillFaceboxFromAjax(href) {
    $.get(
    	href,
    	function(data) {
    		$.facebox.reveal(data) 
    	}
    );
  }

  function skipOverlay() {
    return $.facebox.settings.overlay == false || $.facebox.settings.opacity === null || $.facebox.settings.isOpen
  }

  function showOverlay() {
    if (skipOverlay()){
    	return
    }

    if ($('#facebox_overlay').length == 0) 
      $("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')

    $('#facebox_overlay').hide().addClass("facebox_overlayBG")
      .css('opacity', $.facebox.settings.opacity)
      .click(function() { 
      	if(!$.facebox.settings.modal){
      		$(document).trigger('close.facebox');
      	} 
      })
      .fadeIn(200)
    return false
  }

  function hideOverlay() {
    if (skipOverlay()) return

    $('#facebox_overlay').fadeOut(200, function(){
      $("#facebox_overlay").removeClass("facebox_overlayBG")
      $("#facebox_overlay").addClass("facebox_hide") 
      $("#facebox_overlay").remove()
    })
    
    return false
  }

  /*
   * Bindings
   */

  $(document).bind('close.facebox', function() {
    if($.facebox.settings.close_delay){
    	var timeout = $.facebox.settings.close_delay;
    	$.facebox.settings.close_delay = false;
    	setTimeout(close, timeout);    	
    }
    else close();
  	
  });
  function close(){
    $.facebox.settings.isOpen = false;
  	$(document).unbind('keydown.facebox');
    $('#facebox').fadeOut(function() {
      $('#facebox .content').removeClass().addClass('content');
      hideOverlay();
      $('#facebox .loading').remove();
      if($.facebox.settings.dom){
  		$($.facebox.settings.dom).append($.facebox.settings.dom_data);
  		
  		$.facebox.settings.dom = null;
  		$.facebox.settings.dom_data = null;
  	}
    });
  }

})(jQuery);

jQuery(document).ready(function($) {
  $('a[class*=facebox]').live("click", $.facebox.clickHandler);
}) 