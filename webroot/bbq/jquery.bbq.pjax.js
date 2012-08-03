(function($, undefined){
  
  var cache = {}
  var rootUrlPlaceholder = '__root__'
  
  var $container = $currentPage = options = null
  var $pjaxWrapTemplate = $('<div class="pjax-page" />')

  $.fn.bbq_pjax = function(options){
    options = $.extend({
      linkSelector: 'a',
      transition: function($from, $to) {
        $from.hide()
        $to.show()
      }
    }, options)

    // We assume that the initial content is current when pushState is available,
    // but that we still need to load the initial content if we're using the fallback.
    if ($.support.pushState) {
      var lastURL = currentUrl()
    } else {
      // Unless we're at the root
      var lastURL = rootUrlPlaceholder
    }
    
    // We should only use the first matched element as the content container.
    this.each(function(){
      $container = $(this)
      
      // Set up the initial pjax-page content.
      var $pjaxPage = $pjaxWrapTemplate.clone()
      $pjaxPage.append($container.children()).appendTo($container)
      $pjaxPage.data('pjax-title', document.title)
      $currentPage = cache[lastURL] = $pjaxPage
    
      $(options.linkSelector).live('click', function(event){
        $link = $(this)
        
        // Get the url from the link's href attribute, stripping any leading #.
        $.bbq.pushState( $link.attr( 'href' ).replace( /^#/, '' ), 2 )
        // Prevent the default link click behavior.
        return false
      })
      
    })

    // Bind an event to window.onhashchange
    $(window).bind( 'hashchange', function(e) {
      var url = currentUrl()
      if (url === lastURL) { return }
      lastURL = url;
      

      var rel_url = rootRelativeUrl(url)
      // 
      if (cache[rel_url]) {
        transition(cache[rel_url])
      } else {
        $container.addClass('pjax-loading')
        cache[rel_url] = $pjaxWrapTemplate.clone().data('pjax-url', rel_url)
        var delim = url.indexOf('?') == -1 ? '?' : '&'
        cache[rel_url].load(url+delim+'_pjax=true', function(){
          $(this).find('meta').remove()
          $(this).data('pjax-title', $.trim( $(this).find('title').remove().text() ))
          $(this).hide().appendTo($container)
          transition(cache[rel_url])
          $container.removeClass('pjax-loading')
        })
      }
    })
  
    var transition = function($to){
      // Make some guesses about the direction we're navigating by inspecting the URLs.
      var dir = 'same'
      var initialDepth = initialUrl.replace(/\/$/, '').split('/').length
      var fromDepth = $currentPage.data('pjax-url') ? $currentPage.data('pjax-url').replace(/\/$/, '').split('/').length : initialDepth
      var toDepth = $to.data('pjax-url') ? $to.data('pjax-url').replace(/\/$/, '').split('/').length : initialDepth
      
      if (fromDepth > toDepth) {
        dir = 'up'
      } else if (fromDepth < toDepth) {
        dir = 'down'
      }

      options.transition($currentPage, $to, dir)
      $currentPage = $to
    }

    // Since the event is only triggered when the hash changes, we need to trigger
    // the event now, to handle the hash the page may have loaded with.
    $(window).trigger( 'hashchange' );

  }

  var rootRelativeUrl = function(url){
    var l = window.location
    if (url == rootUrlPlaceholder) {
      return url
    } else if (url.indexOf('http') === 0) {
      return url.replace(/^(?:\/\/|[^\/]+)*\//, '/') 
    } else if (url.indexOf('/') === 0) {
      return url
    } else {
      var path = l.pathname.split('/')
      path.pop()
      path.push(url)
      return path.join('/')
    }
  }

  var currentUrl = function(){
    var url = ($.support.pushState) 
      ? window.location.href.replace(/^(?:\/\/|[^\/]+)*\//, '/') 
      : window.location.hash.replace(/^#/, '')
    return url != '' ? url : rootUrlPlaceholder
  }

  var initialUrl = rootRelativeUrl(window.location.href)

})(jQuery)
