<h1>user details</h1>

<h2>name: {{$user_profile->username}}</h2>

<a href="#addFriend" id="a_addfriend" rel="{{$user_profile->userID}}">add as friend</a>


<h2>coming soon: list of routes by this user</h2>

<script type="text/javascript">

$("#a_addfriend").bind("click", click_addFriend);

function click_addFriend(){
	var friend_uid = this.rel;
	$(this).text("adding friend...");
	$.post(
		"/lib/ajax_addFriend.php",
		{f_uid:friend_uid},
		function(data){
			if(data > 0){
				alert(data);
				$("#a_addfriend").text("friend added");
				$("#a_addfriend").unbind("click", click_addFriend);
				$("#a_addfriend").bind("click", function(){ return false; });
			}
			else{
				$("#a_addfriend").text("try adding again");
				$("#a_addfriend").hide();
				$("#a_addfriend").fadeIn("slow");
			}
		},
		"text"
	);
	return false;
}

</script>