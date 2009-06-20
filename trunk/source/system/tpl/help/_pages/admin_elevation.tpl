<h3>Admin Elevation Control Panel</h3>

<p>This page serves as the single place to handle all of the 
elevation data for the site.  Here you can add new data to the site
or look through existing routes that need data.  The main idea is that 
this page should make it as easy as possible to get new data into the system. </p>

<h4>Actions</h4>
<p class="notice">Show existing list</p>
<p>This action will show a list of the elevation regions that are already defined.
These are also on the map underneath as green regions.</p>
<p class="notice">Get routes without elevation</p>
<p>This will load 50 routes that do not have elevation.  This will first show a list of the routes
which you cannot do much with.  Once that goes away, you can then look over the map to find places 
where routes do not have data.  This will only show routes that do not already fit into regions.
If you would like to add data for a route or a region then box it in and follow the steps.</p>
<p class="notice">Update elevation</p>
<p>This will show a confirmation dialog which will then do the actual work.  This sends a request
to the server to grab up to 10 routes that do not have elevation data.  They will then be processed
if they can be.</p>

<h4>The rest of the page</h4>
<p>The rest of the page allows for adding data into the system.  Select the clicking mode and then
click on the map to select an area.  You can then download that area from the USGS site.  You then
want to pack that data by using step 2.  Finally, you can add the packed file into the database.
The name that is chosen will be shown on this page only.</p>