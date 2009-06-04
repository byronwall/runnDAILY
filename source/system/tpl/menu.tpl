<ul class="header nav main">
		<li><a href="/index" class="icon"><img src="/img/icon/home.png" />Home</a></li>
		<li><a href="/routes/index" class="icon"><img src="/img/icon/route.png" />Routes</a>
			<ul>
				<li><a href="/routes/create">New Route</a></li>
<!--				<li><a href="/routes/browse">Search</a></li>-->
			</ul>
		</li>
		<li><a href="/training/index" class="icon"><img src="/img/icon/training.png" />Training</a>
			<ul>
					<li><a href="/training/create">New Training Item</a></li>
					<li><a href="/goals">View Goals</a></li>
					<li><a href="/goals/create">New Goal</a></li>
<!--					<li><a href="/training/browse">Search</a></li>-->
			</ul>
		</li>
		<li><a href="/community/index" class="icon"><img src="/img/icon/community.png" />Community</a>
			<ul>
				<li><a href="/confirmation">Requests</a></li>
			</ul>
		</li>
		
		<li><a href="/about" class="icon"><img src="/img/icon/runndaily.png"/>runnDAILY</a>
		<ul>
				<li><a href="/about/index">About Us</a></li>
				<li><a href="/about/contact">Contact</a></li>
				<li><a href="/about/credits">Credits</a></li>
		</ul>
		</li>
		<li class="secondary">
		{{if $engine->requirePermission("PV__300")}}
			<a href="/community/view_user/{{$currentUser->uid}}/{{$currentUser->username}}" class="icon"><img src="/img/icon/user_friend.png" />{{$currentUser->username}}</a>
		<ul>
				<li><a href="/settings" class="icon">Settings</a></li>
				<li><a href="/user/logout" class="icon">Logout</a></li>
		</ul>
		{{else}}
		<li class="secondary"><a href="#login_modal" class="facebox icon"><img src="/img/icon_login.png" />Login</a></li>
		<li class="secondary"><a href="/register" class="icon"><img src="/img/icon/register.png" />Register</a></li>
		{{/if}} {{if $engine->requirePermission("PV__100")}}
		<li class="secondary"><a href="/admin/index" class="icon"><img src="/img/icon_application_monitor.png" />Admin</a></li>
		{{/if}}
		<li class="secondary">
			<a href="/help/view/{{$engine->getCommonName()}}" class="facebox icon"><img src="/img/icon/help.png" />Help</a>
			<a href="/help/view" class="facebox icon"><img src="/img/icon/help.png" />Help (Byron broke this)</a>
			<ul>
				<li><a href="/guides" class="icon">Guides</a></li>
			</ul>
		</li>
		<li class="secondary"><a href="#feedback_modal" class="facebox icon"><img src="/img/icon/feedback.png" />Feedback </a></li>
</ul>