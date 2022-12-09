<style>
  .bar {
    border: 2px solid #666;
    border-radius: 5px;
    height: 20px;
    width: 100%;
  }

  .bar .in {
    animation: fill 10s linear 1;
    height: 100%;
    background-color: red;
  }

  @keyframes fill {
    0% {
      width: 0%;
    }

    100% {
      width: 100%;
    }
  }
  .hidden {
    display: none;
  } 
</style>
<?php if($countdown):?>

<div onclick="clearTimeout(timeout);document.getElementById(`bar`).classList.add(`hidden`);" style="margin: auto; width: 100%; text-align: center; padding: 10px;">
All tests have been completed, and in ten seconds, you will be sent to the application. If you want to cancel this, click here.
  <div class="bar">
    <div id="bar" class="in"></div>
  </div>
</div>

<script>var timeout = setTimeout(() => parent.window.location.reload(true), 10000);</script>
<?php else: ?>
  <div onclick="parent.window.location.reload(true)" style="margin: auto; width: 100%; text-align: center; padding: 10px;">
  <h1 style="color: red">All tests have been completed, please read the warning and then click here to launch the application...</h1>
</div>
<?php endif; ?>
</section>
</body>
</html>