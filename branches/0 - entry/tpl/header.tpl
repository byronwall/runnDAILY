      <div class="header">
        <div class="leftheader">
          <div class="PrettyMenu">
            <div class="AspNet-Menu-Horizontal">
              <ul class="AspNet-Menu">
                <li class="AspNet-Menu-Leaf AspNet-Menu-Selected">
                  <a href="index.php" class="AspNet-Menu-Link AspNet-Menu-Selected">MAPS</a>
                </li>
                <li class="AspNet-Menu-Leaf">
                  <a href="routes.php" class="AspNet-Menu-Link">routes</a>
                </li>
                <li class="AspNet-Menu-Leaf">
                  <a href="#" class="AspNet-Menu-Link">times</a>
                </li>
                <li class="AspNet-Menu-Leaf">
                  <a href="users.php" class="AspNet-Menu-Link">users</a>
                </li>
                {{if $activeUser}}
                <li class="AspNet-Menu-Leaf">
                  <a href="settings.php" class="AspNet-Menu-Link">settings</a>
                </li>
                {{/if}}
              </ul>
            </div>
          </div>
        </div>
        <div class="rightheader">
          <div class="siteName">
            running site
          </div>
          {{if $activeUser}} 
          <div>
            Welcome {{$currentUser->username}} <a href="php/login.php?action=logout">logout</a>
          </div>
          {{else}}
          <a href="#TB_inline?height=300&width=300&inlineId=login_temp" title="login" class="thickbox">login</a>
          <a href="#TB_inline?height=300&width=300&inlineId=register_inline" title="register" class="thickbox">register</a>
          <div class="rh_login" id="login_temp">
            <form action="php/login.php?action=login" method="post">
              <ul>
              <li>  <input type="text" value="username" name="username"> </li>
                <li> <input type="password" value="password" name="password"> </li>
	              <li> <input type="checkbox" checked="checked" value="remember?" name="remember"> </li>
	              <li> <input type="submit" value="login"> </li>
              </ul>
            
            </form>
          </div>
          
          <div class="rh_login" id="register_inline">
            <form action="php/login.php?action=register" method="post">
              <ul>
              <li>  <input type="text" value="username" name="username"> </li>
                <li> <input type="password" value="password" name="password"> </li>
	              <li> <input type="submit" value="register"> </li>
              </ul>
            
            </form>
          </div>
          {{/if}}
        </div>
      </div>