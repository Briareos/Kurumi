<?php

include "../index.php";

$shell['title3'] = "hashchange » Basic";

$shell['h2'] = 'Cached AJAX + fragment + history + bookmarking = Tasty!';

// ========================================================================== //
// SCRIPT
// ========================================================================== //

ob_start();
?>

$(function(){

  $('.bbq-item').bbq_pjax({
    linkSelector: 'a',
    transition: function($from, $to, dir){
      $from.slideUp()
      $to.slideDown()
    }
  });
  
});

<?php
$shell['script'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// HTML HEAD ADDITIONAL
// ========================================================================== //

ob_start();
?>
<script type="text/javascript" src="../../jquery.ba-bbq.js"></script>
<script type="text/javascript" language="javascript">
$.support.pushState = <?php print $_GET['disable'] ? 'false' : 'true' ?>
</script>
<script type="text/javascript" src="../../jquery.bbq.pjax.js"></script>
<script type="text/javascript" language="javascript">
<?php print $shell['script']; ?>

$(function(){
  
  // Syntax highlighter.
  SyntaxHighlighter.highlight();
  
});

</script>
<style type="text/css" title="text/css">

/*
bg: #FDEBDC
bg1: #FFD6AF
bg2: #FFAB59
orange: #FF7F00
brown: #913D00
lt. brown: #C4884F
*/

.bbq {
  margin-bottom: 1em;
}

.bbq-content {
  border-left: 1px solid #913D00;
  border-right: 1px solid #913D00;
  padding: 8px;
  margin: 0;
  float: left;
  width: 682px;
  height: 302px;
}

.bbq-item h1 {
  margin: 0;
  font-size: 180%;
}

.bbq-item p {
  font-size: 150%;
  margin: 5px 0 0;
}

.bbq-item img {
  border: 1px solid #913D00;
  float: right;
  margin-left: 10px;
}

a.bbq-current {
  font-weight: 700;
  text-decoration: none;
}

.bbq-nav {
  padding: 0.3em;
  color: #C4884F;
  border: 1px solid #C4884F;
  background: #FFD6AF;
  clear: both;
  text-align: center;
}

.bbq-nav-top {
  margin-bottom: 0;
  -moz-border-radius-topleft: 10px;
  -moz-border-radius-topright: 10px;
  -webkit-border-top-left-radius: 10px;
  -webkit-border-top-right-radius: 10px;
}

.bbq-nav-bottom {
  margin-top: 0;
  -moz-border-radius-bottomleft: 10px;
  -moz-border-radius-bottomright: 10px;
  -webkit-border-bottom-left-radius: 10px;
  -webkit-border-bottom-right-radius: 10px;
}

#page {
  width: 700px;
}

</style>
<?php
$shell['html_head'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// HTML BODY
// ========================================================================== //

ob_start();
?>
<?php print $shell['donate'] ?>

<p>
  With <a href="http://benalman.com/projects/jquery-bbq-plugin/">jQuery BBQ</a> you can keep track of state, history and allow bookmarking while dynamically modifying the page via AJAX and/or DHTML.. just click the links, use your browser's back and next buttons, reload the page.. and when you're done playing, check out the code!
</p>

<p>
  In this basic example, window.location.hash is used to store a simple string value of the file to be loaded via AJAX, so that not only a history entry is added, but also so that the page, in its current state, can be bookmarked. Because the hash contains only a single filename, this example doesn't support multiple content boxes, each with their own state, on the same page, but that's definitely still possible! Just check out the <a href="../fragment-advanced/">advanced window.onhashchange</a> example.
</p>

<h3>Navigation</h3>

<div class="bbq">
  <div class="bbq-nav bbq-nav-top">
    <a href="burger.html">Burgers</a> |
    <a href="chicken.html">Chicken</a> |
    <a href="kebabs.html">Kebabs</a> |
    <a href="kielbasa.html">Kielbasa</a> |
    <a href="ribs.html">Ribs</a> |
    <a href="steak.html">Steak</a>
  </div>
  
  <div class="bbq-content">
    
    <!-- This will be shown while loading AJAX content. You'll want to get an image that suits your design at http://ajaxload.info/ -->
    <div class="bbq-loading" style="display:none;">
      <img src="/shell/images/ajaxload-15-white.gif" alt="Loading"/> Loading content...
    </div>
    
    <!-- This content will be shown if no path is specified in the URL fragment. -->
    <div class="bbq-default bbq-item">
      <img src="bbq.jpg" width="400" height="300">
      <h1>jQuery BBQ</h1>
      <p>Click a nav item above or below to load some delicious AJAX content! Also,
        once the content loads, feel free to further explore our savory delights by
        clicking any inline links you might see.</p>
    </div>
    
  </div>
  
  <div class="bbq-nav bbq-nav-bottom">
    <a href="burger.html">Burgers</a> |
    <a href="chicken.html">Chicken</a> |
    <a href="kebabs.html">Kebabs</a> |
    <a href="kielbasa.html">Kielbasa</a> |
    <a href="ribs.html">Ribs</a> |
    <a href="steak.html">Steak</a>
  </div>
</div>

<h3>The code</h3>

<p>This example attempts to wrap up a pjax-style interaction as an add-on to jquery-bbq. After you've included the jquery.bbq.pjax.js file, implementing it is as simple as:</p>

<pre class="brush:js">
<?php print htmlspecialchars( $shell['script'] ); ?>
</pre>

<?php
$shell['html_body'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// DRAW SHELL
// ========================================================================== //

draw_shell();

?>