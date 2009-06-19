{{*This is the template for the index page of the root folder.*}}
<div class="grid_12">
<h2 id="page-heading">Welcome to runnDAILY</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		{{if $engine->requirePermission("PV__300")}}
		<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png" />New Route</a>
		<a href="/training/create" class="icon"><img src="/img/icon/training_plus.png" />New Training Item</a>
		{{else}}
		<form id="login_form" action="/user/login" method="post">
			<p>
				<label for="u_username">Username: </label><input type="text" name="username" />
				<label for="u_password">Password: </label><input type="password" name="password" />
				<label>Stay Logged In? </label><input type="checkbox" name="remember" value="1">
				<input type="submit" value="Login" />
			</p>
			<a href="/register" class="icon"><img src="/img/icon/register.png" />Register</a>
		</form>
		{{/if}}
	</div>
</div>
<div class="clear"></div>

{{if $engine->requirePermission("PV__300")}}
<div class="grid_12">
	<h4><img src="/img/icon/star.png" /> New Features / Updates</h4>
	<p><span class="bold">June 19, 2009</span> - Thanks to additional <a href="#feedback_modal" class="facebox icon"><img src="/img/icon/feedback.png" />Feedback</a>, we are proud to annouce several new features and updates to the site!</p>
</div>
<div class="clear"></div>

<div class="grid_3">
	<p><a href="/goals" class="icon"><img src="/img/icon/trophy.png"/ >Goals</a> - <span class="alert_green">NEW feature!</span></p>
	<ul>
		<li>Goals are a great way to establish benchmarks and monitor your long term progress.</li>
		<li>Create a <a href="/goals/create" class="icon"><img src="/img/icon/trophy_plus.png" />New Goal</a> based on distance, pace, and/or time.</li>
		<li>View your <a href="/goals" class="icon"><img src="/img/icon/trophy.png" />Goals</a> and reach new limits as your push yourself!</li>
	</ul>
</div>

<div class="grid_3">
	<p><a href="/training" class="icon"><img src="/img/icon/training.png" />Training</a> - <span class="alert_green">Updated</span></p>
	<ul>
		<li>Training items now show up in more places. Look for them on the routes and goals you create.</li>
		<li>Interested in a summary of your recent training? Look for the <a href="/training/summary" class="icon facebox"><img src="/img/icon/training_summary.png"/>Training Summary</a> on your training page.</li>
		<li>Interested in storing a specific description about a training item? Comments are now included with every training item.</li>
	</ul>
</div>

<div class="grid_3">
	<p><a href="/routes" class="icon"><img src="/img/icon/route.png" /> Routes</a> - <span class="alert_green">Updated</span></p>
	<ul>
		<li><span class="icon bold"><img src="/img/icon/ruler.png" />Elevation</span> plots are now included for most routes. We are working to expand our elevation database, but look for the chart on your routes!</li>
		<li>Additionals options have been introducted into the <span class="icon bold"><img src="/img/icon/settings.png" /> Settings</span> for creating new routes.</li>
	</ul>
</div>

<div class="grid_3">
	<p><a href="/community" class="icon"><img src="/img/icon/community.png" /> Community</a> - <span class="alert_green">Updated</span></p>
	<ul>
		<li><a href="/messages" class="icon"><img src="/img/icon/mail.png" />Messages</a> are now included for all users. You can communicate directly with your friends.</li>
		<li><a href="/confirmation" class="icon"><img src="/img/icon/balloons.png" />Requests</a> are now sent when adding users as friends. This allows all friendships to be mutual.</li>
		<li>A search field has been added to the community page to facilitate finding friends.</li>
	</ul>
</div>
<div class="clear"></div>

<div class="grid_12">
<p>We hope you enjoy the new features and page updates. We took our time to
consider and provide the features that are most useful. We look forward to
hearing any <a href="#feedback_modal" class="facebox icon"><img
		src="/img/icon/feedback.png"
/>Feedback</a> you might have. We will continue to provide the features you want!
Also, we updated nearly every <span class="icon bold"><img
		src="/img/icon/help.png"
/> Help</span> page, so take a look if you are unsure how a page works.</p>
<p>If you would like to find out about updates and other news as they are
available, go ahead and follow us on Twitter at <a
		href="http://twitter.com/runndaily" target="_blank" class="icon"
><img src="/img/icon/balloon_twitter.png" />runnDAILY</a>.</p>
</div>
<div class="clear"></div>
{{/if}}

<div class="grid_12">
<h3>runnDAILY</h3>
<p>Welcome to runnDAILY! runnDAILY is an online dashboard for anyone that
runs, walks, bikes, or jogs. We provide a very unique set of tools that allow a
user to completely track their personal exercise activity. The most powerful of
these tools is a mapping utility that can plot a variety of routes. In addition
to the routing utility, there are numerous other features that allow a user to
track past activity and record times for daily workouts. These features are
provided inside an intuitive, easy to use interface that's built on a social
network of fellow runners. The social networking aspect of runnDAILY allows
users to interact with one another by sharing routes and training activities.</p>
<p><span class="bold">Please take a moment to familiarize yourself
with the site's layout and features by reading the brief descriptions provided
below.</span></p>
<hr>
<h4>Site Features</h4>
<p>As previously stated, there are many aspects to runnDAILY. While each
feature can be used independently, there are several features available on every
page.</p>
<h5><img src="/img/icon/help.png" /> Help</h5>
<p>Available at the top of every page is a link with specific
instructions and explanations for the current page. Help is always just a click
away!</p>
<h5><img src="/img/icon/feedback.png" /> Feedback</h5>
<p>Also available at the top of every page is a link that allows users to
provide direct feedback. We will read and consider all user feedback. We want to
provide every user with an enjoyable experience. Don't be shy; let us know what
you really think!</p>
<h5><img src="/img/icon/distance.png" /> Miles / Kilometers</h5>
<p>Nearly EVERY distance can be clicked and dynamically updated to display miles or
kilometers. <span class="bold">Try it out: <span class="dist-val">10.0
mi</span></span></p>
<hr>
<h4><img src="/img/icon/route.png" /> Routes</h4>
<p>Routes are the core of runnDAILY. Registered users may create, edit, and
share routes. A quick list of the main features is provided below. For a
complete description of route features, click the Help icon on any route page.</p>
<h5><img src="/img/icon/route_plus.png" /> Create Route</h5>
<p>Routes can be created using a variety of methods. Our mapping utility
provides two distinct possibilities: point-by-point or by following roads.
Detailed information about these features and their options can be found in the
Help panel on the create route page.</p>
<h5><img src="/img/icon/route_view.png" /> View Route</h5>
<p>Detailed information is available for any route created with runnDAILY.
In addition to overall distance, individual mile / kilometer markers are
available for every route. Lastly, most routes include a detailed plot of the <span
		class="icon bold"
><img src="/img/icon/ruler.png" />Elevation</span> throughout the route.</p>
<hr>
<h4><img src="/img/icon/training.png" /> Training</h4>
<p>runnDAILY provides a collection of tools that allow a user to completely
track their personal training and exercise habits. The most notable training
feature is the ability to log running, walking, jogging, and bicycling
activities.</p>
<h5><img src="/img/icon/training_plus.png" /> Create Training Item</h5>
<p>Training items can be created and linked to routes. Each training item
has a type, distance, and time associated with it. These values are used to
calculate pace and track a user's progress. Users that provided height and
weight specifications will also be able to view <i>estimated</i> calories
burned.</p>
<h5><img src="/img/icon/training_view.png" /> View Training Items</h5>
<p>Several methods are provided for viewing training items. Training items
may be viewed individually or collectively using a variety of charts and
plotting strategies.</p>
<h5><img src="/img/icon/trophy.png" /> Goals</h5>
<p>Users may create goals to track their long and short term progress. Goals
provide a useful tool for measuring benchmarks and striving to improve.
Currently, goals may be created relative to distance, pace, and/or time.</p>
<hr>
<h4><img src="/img/icon/community.png" /> Community</h4>
<p>runnDAILY is built on a social network. While this network will be small
in the early stages of development, its future applications are immense. For
now, there is a limited set of community features available to all users.</p>
<h5><img src="/img/icon/user_plus.png" /> Add Friend / <img
		src="/img/icon/user_view.png"
/> View Friend</h5>
<p>Relationships can be established between users. Keep track of your
friends' progress by viewing their routes and training entries.</p>
<h5><img src="/img/icon/mail.png" /> Messages</h5>
<p>Users may send messages to their friends. Threaded conversations are
maintained for any messages that are sent or received.</p>
</div>
<div class="clear"></div>