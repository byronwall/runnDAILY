/*
 * runnDAILY Mapping Library
 */
var MapHistory={events:[],currentEvent:0,markNewEvent:function(){this.currentEvent++;this.events[this.currentEvent]=0},addToEvent:function(){this.events[this.currentEvent]++},undoEvent:function(){if(this.currentEvent==0){return 0}var A=this.events.pop();this.currentEvent--;return A}};var MapActions={outAndBack:function(){MapHistory.markNewEvent();for(var A=Map.points.length-1;A>=0;A--){Map.addPoint(Map.points[A].latlng,true)}},undoLastPoint:function(){var A=MapHistory.undoEvent();console.log(A);while(A-->0){console.log("remove point");Map.points.pop()}Map.refresh()},clearAllPoints:function(){Map.points=[];isRouteLineInit=true;Map.refresh()}};var MapSettings={MapType:"G_NORMAL_MAP",DistanceCircle:{enable:false,radius:5},MileMarkers:{enable:true,distance:1},Directions:{enable:false}};var Map={config:{draggable:true,show_points:true},icon:null,iconOptions:null,instance:null,points:[],polyline:null,totalDistance:0,changeCallback:function(A){GEvent.removeListener(Map.listener);Map.listener=GEvent.addListener(Map.instance,"click",A)},init:function(){Map.icon=new GIcon();Map.icon.image="/img/dot.png";Map.icon.shadow="";Map.icon.iconSize=new GSize(12,12);Map.icon.shadowSize=new GSize(0,0);Map.icon.iconAnchor=new GPoint(6,6);Map.icon.infoWindowAnchor=new GPoint(6,6);Map.iconOptions={icon:Map.icon,draggable:Map.config.draggable}},listener:null,load:function(C,A,B){if(GBrowserIsCompatible()){if(B&&B.full_height){$("#"+C).heightBrowser()}Map.instance=new GMap2(document.getElementById(C),{mapTypes:[G_NORMAL_MAP,G_SATELLITE_MAP,G_HYBRID_MAP,G_PHYSICAL_MAP]});Map.instance.setCenter(new GLatLng(39.229984356582,-95.2734375),4);if(A!=null){Map.listener=GEvent.addListener(Map.instance,"click",A)}Map.instance.addControl(new GSmallMapControl());Map.instance.addControl(new GMapTypeControl());Map.instance.enableScrollWheelZoom();Map.instance.savePosition();MileMarkers.init()}},event_click:function(B,C,A){if(MapSettings.Directions.enable){Directions.click(B,C,A)}else{if(B){Map.addPoint(A)}if(C){Map.addPoint(C)}}},addPoint:function(E,B){if(!B){MapHistory.markNewEvent()}MapHistory.addToEvent();if(Map.polyline!=null){var D=Map.points.length;Map.polyline.insertVertex(Map.points.length,E);if(D>0){Map.totalDistance+=Map.points[D-1].latlng.distanceFrom(E)*meters_to_miles;Map.updateDistanceDisplay()}}else{Map.polyline=new GPolyline([E],"#ff0000",3,0.5);Map.instance.addOverlay(Map.polyline)}var A=new routePoint();A.latlng=E;if(Map.config.show_points){var C=new GMarker(E,Map.iconOptions);Map.instance.addOverlay(C);A.marker=C;A.marker.marker_id=Map.points.length-1;if(Map.iconOptions.draggable){GEvent.addListener(C,"dragend",Map.event_dragend)}}else{A.marker=null}Map.points.push(A);MileMarkers.update(false);DistanceCircle.draw()},refresh:function(A){opts=$.extend({},{redraw_points:true,redraw_miles:true,redraw_line:true,clear_all:true},A);if(opts.clear_all){Map.instance.clearOverlays()}if(opts.redraw_points){$.each(Map.points,function(C,B){Map.instance.addOverlay(B.marker)})}if(opts.redraw_line){if(Map.polyline){Map.instance.removeOverlay(Map.polyline)}Map.polyline=new GPolyline($.map(Map.points,function(B){return B.latlng}),"#ff0000",3);Map.instance.addOverlay(Map.polyline)}Map.totalDistance=Map.polyline.getLength()*meters_to_miles;if(opts.redraw_miles){MileMarkers.update(true)}DistanceCircle.draw();Map.updateDistanceDisplay()},updateDistanceDisplay:function(){Units.textWithUnits({dist:Map.totalDistance,target:".r_distance_disp"})},event_dragend:function(A){Map.points[this.marker_id+1].latlng=A;Map.refresh({redraw_points:false,clear_all:false})},setHomeLocation:function(B,A){Map.instance.setCenter(new GLatLng(B,A),12);Map.instance.savePosition()}};var meters_to_miles=0.000621371192;GMarker.prototype.marker_id=-1;GMarker.prototype.z_index=1;function routePoint(){}routePoint.prototype={latlng:null,marker:null};var Geocoder={instance:new GClientGeocoder(),showAddress:function(B){var A=$(B).val();if(A.toLowerCase()=="home"){Map.instance.returnToSavedPosition();return }Geocoder.instance.getLatLng(A,Geocoder.showAddressCallback)},showAddressCallback:function(A){var B=$("#location_msg");if(!A){B.text("The location you entered could not be found.");B.removeClass("success").addClass("error")}else{B.text("The map has been re-centered.");B.removeClass("error").addClass("success");Map.instance.setCenter(A,13)}}};var MapSave={submitHandler:function(A){A.r_points.value=(MapSave.routeToPolyline());A.r_start_lat.value=(Map.points[0].latlng.lat());A.r_start_lng.value=(Map.points[0].latlng.lng());A.r_distance.value=(Map.totalDistance.toFixed(2))},routeToPolyline:function(){var B=new PolylineEncoder();var A=B.dpEncodeToJSON($.map(Map.points,function(C){return C.latlng}));return $.toJSON({points:A.points,levels:A.levels})}};var MileMarkers={icon:null,icon_options:null,points:[],prevDistance:0,prevMarkerDistance:0,init:function(){MileMarkers.icon=new GIcon();MileMarkers.icon.image="/img/map/mile-20.png";MileMarkers.icon.shadow="";MileMarkers.icon.iconSize=new GSize(20,20);MileMarkers.icon.shadowSize=new GSize(0,0);MileMarkers.icon.iconAnchor=new GPoint(10,10);MileMarkers.icon.infoWindowAnchor=new GPoint(0,10);MileMarkers.icon_options={icon:MileMarkers.icon,clickable:false}},add:function(F,D,E){var G=new GLatLng(F,D);var C=$.extend({},MileMarkers.icon_options,{labelText:E,labelOffset:new GSize(-10,-8),zIndexProcess:function(H){return H.z_index}});var B=new LabeledMarker(G,C);var A=new routePoint();A.latlng=G;A.marker=B;Map.instance.addOverlay(B);MileMarkers.points.push(A)},update:function(G){if(Map.points.length<=1){MileMarkers.prevMarkerDistance=0;MileMarkers.prevDistance=0;B=1;D=0;return }var D=MileMarkers.prevDistance;var B=Map.points.length-1;if(G){for(var E=MileMarkers.points.length-1;E>=0;E--){point=MileMarkers.points.pop();Map.instance.removeOverlay(point.marker)}MileMarkers.prevMarkerDistance=0;MileMarkers.prevDistance=0;B=1;D=0}for(var C=B;C<Map.points.length;C++){var L=Map.points[C].latlng;var F=Map.points[C-1].latlng;var M=F.distanceFrom(L)*meters_to_miles;if(!Units.is_mile){M*=Units.convert}D+=M;var K=MileMarkers.prevDistance-Math.floor(MileMarkers.prevDistance/MapSettings.MileMarkers.distance)*MapSettings.MileMarkers.distance;for(var E=1;E<(D-MileMarkers.prevMarkerDistance)/MapSettings.MileMarkers.distance;E++){var A=(MapSettings.MileMarkers.distance*E-K)/(D-MileMarkers.prevDistance);var I=F.lat()+A*(L.lat()-F.lat());var J=F.lng()+A*(L.lng()-F.lng());var H=MileMarkers.prevMarkerDistance+E*MapSettings.MileMarkers.distance;MileMarkers.add(I,J,H)}MileMarkers.prevMarkerDistance=Math.floor(D/MapSettings.MileMarkers.distance)*MapSettings.MileMarkers.distance;MileMarkers.prevDistance=D}}};var DistanceCircle={polyline:null,points:18,draw:function(){if(MapSettings.DistanceCircle.enable){if(DistanceCircle.polyline!=null){Map.instance.removeOverlay(DistanceCircle.polyline)}var G=MapSettings.DistanceCircle.radius-Map.totalDistance;if(G>0&&Map.points.length>0){var A=Map.points[Map.points.length-1].latlng;var E=A.distanceFrom(new GLatLng(A.lat()+0.1,A.lng()))/160.939;var C=A.distanceFrom(new GLatLng(A.lat(),A.lng()+0.1))/160.939;var F=[];for(var B=0;B<=DistanceCircle.points;B++){var D=2*Math.PI/DistanceCircle.points*B;F[B]=new GLatLng(A.lat()+Math.cos(D)*G/E,A.lng()+Math.sin(D)*G/C)}DistanceCircle.polyline=new GPolygon(F,"#444444",1,0.5,"#555555",0.2);Map.instance.addOverlay(DistanceCircle.polyline)}}else{if(DistanceCircle.polyline!=null){Map.instance.removeOverlay(DistanceCircle.polyline)}}}};var MapData={loadRoute:function(C,E){MapActions.clearAllPoints();Map.config=$.extend({},Map.config,E);Map.init();C.zoomFactor=2;C.numLevels=18;var B=new GPolyline.fromEncoded(C);var D=B.getBounds();Map.instance.setCenter(D.getCenter(),Map.instance.getBoundsZoomLevel(D));for(var A=0;A<B.getVertexCount();A++){var F=B.getVertex(A);Map.addPoint(F)}}};var Directions={isSearching:false,instance:null,polyline:null,options:{getPolyline:true,travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true},init:function(){Directions.instance=new GDirections(null,$("#results")[0]);GEvent.addListener(Directions.instance,"load",Directions.load_event);GEvent.addListener(Directions.instance,"error",Directions.error_event)},click:function(B,E,A){if(Directions.isSearching){return }if(E){if(Map.points.length==0){Map.addPoint(E)}else{var C=Map.points[Map.points.length-1].latlng;var D="from:"+C.lat()+","+C.lng()+" to: "+E.lat()+","+E.lng();Directions.isSearching=true;Directions.instance.load(D,Directions.options)}}},load_event:function(){MapHistory.markNewEvent();Directions.polyline=Directions.instance.getPolyline();var B=Directions.polyline.getVertexCount();for(var A=0;A<B;A++){Map.addPoint(Directions.polyline.getVertex(A),true)}Directions.isSearching=false},error_event:function(){}};var Display={fullscreen:false,toggle_fullscreen:function(){if(!Display.fullscreen){$("#r_map").removeClass("map").addClass("map_full");$("#r_map").css("position","fixed");$("#r_map").heightBrowser({scale:1,clear:true});$("#r_map").parents().each(function(){$(this).data("overflow_init",$(this).css("overflow"));$(this).css("overflow","hidden")});$("#map_overlay").show();Display.fullscreen=true;Map.instance.checkResize()}else{$("#r_map").removeClass("map_full").addClass("map");$("#r_map").css("position","relative");$("#r_map").heightBrowser({clear:true});$("#r_map").parents().each(function(){$(this).css("overflow",$(this).data("overflow_init"))});$("body").css("overflow","auto");$("#map_overlay").hide();Display.fullscreen=false;Map.instance.checkResize()}}};Map.init();Directions.init();
/*
 * @name LabeledMarker
 * @version 1.3
 * @author Mike Purvis (http://uwmike.com)
 * @copyright (c) 2007 Mike Purvis (http://uwmike.com)
 * @fileoverview
 *     <p>This library extends the Google Maps API's standard GMarker class
 *         with the ability to support markers with textual labels. Please
 *         see articles here:</p>
 *     <ul>
 *       <li><a
 *           href="http://googlemapsbook.com/2007/01/22/extending-gmarker/"
 *           >Extending The API To Create Labeled Markers</a></li>
 *       <li><a
 *            href="http://googlemapsbook.com/2007/03/06/clickable-labeledmarker/"
 *            >Clickable LabeledMarker</a></li>
 *     </ul>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function LabeledMarker(B,A){this.opts_=A;this.labelText_=A.labelText||"";this.labelClass_=A.labelClass||"LabeledMarker_markerLabel";this.labelOffset_=A.labelOffset||new GSize(0,0);this.clickable_=A.clickable||true;this.title_=A.title||"";this.labelVisibility_=true;this.ownVisibility_=true;this.div_=document.createElement("div");this.div_.className=this.labelClass_;this.div_.innerHTML=this.labelText_;this.div_.style.position="absolute";this.div_.style.cursor="pointer";this.div_.title=this.title_;if(A.draggable){A.draggable=false}GMarker.apply(this,arguments)}LabeledMarker.prototype=new GMarker(new GLatLng(0,0));LabeledMarker.prototype.initialize=function(C){GMarker.prototype.initialize.apply(this,arguments);this.map_=C;if(!this.ownVisibility_){this.hide()}this.applyLabelVisibility_();C.getPane(G_MAP_MARKER_PANE).appendChild(this.div_);if(this.clickable_){var D=["click","dblclick","mousedown","mouseup","mouseover","mouseout"];for(var B=0;B<D.length;B++){var A=D[B];GEvent.addDomListener(this.div_,A,GEvent.callback(GEvent,GEvent.trigger,this,A,this.getLatLng()))}}};LabeledMarker.prototype.redraw=function(A){GMarker.prototype.redraw.apply(this,arguments);this.redrawLabel_()};LabeledMarker.prototype.redrawLabel_=function(){var A=this.map_.fromLatLngToDivPixel(this.getLatLng());var B=this.z_index;this.div_.style.left=(A.x+this.labelOffset_.width)+"px";this.div_.style.top=(A.y+this.labelOffset_.height)+"px";this.div_.style.zIndex=B};LabeledMarker.prototype.remove=function(){GEvent.clearInstanceListeners(this.div_);if(this.div_.outerHTML){this.div_.outerHTML=""}if(this.div_.parentNode){this.div_.parentNode.removeChild(this.div_)}this.div_=null;GMarker.prototype.remove.apply(this,arguments)};LabeledMarker.prototype.copy=function(){var A=new LabeledMarker(this.getLatLng(),this.opts_);A.labelVisibility_=this.labelVisibility_;A.ownVisibility_=this.ownVisibility_;return A};LabeledMarker.prototype.show=function(){GMarker.prototype.show.apply(this,arguments);this.ownVisibility_=true;this.applyLabelVisibility_()};LabeledMarker.prototype.hide=function(){GMarker.prototype.hide.apply(this,arguments);this.ownVisibility_=false;this.applyLabelVisibility_()};LabeledMarker.prototype.setLabelVisibility=function(A){this.labelVisibility_=A;this.applyLabelVisibility_()};LabeledMarker.prototype.getLabelVisibility=function(){return this.labelVisibility_};LabeledMarker.prototype.applyLabelVisibility_=function(){if((!this.isHidden())&&this.labelVisibility_){this.div_.style.display="block"}else{this.div_.style.display="none"}};LabeledMarker.prototype.setLabelText=function(A){this.labelText_=A;this.div_.innerHTML=A;this.opts_.labelText=A};eval(function(E,A,F,B,D,C){D=function(G){return(G<A?"":D(parseInt(G/A)))+((G=G%A)>35?String.fromCharCode(G+29):G.toString(36))};if(!"".replace(/^/,String)){while(F--){C[D(F)]=B[F]||D(F)}B=[function(G){return C[G]}];D=function(){return"\\w+"};F=1}while(F--){if(B[F]){E=E.replace(new RegExp("\\b"+D(F)+"\\b","g"),B[F])}}return E}('m=o(a,b,c,d){7 i;6(!a){a=18}6(!b){b=2}6(!c){c=0.1g}6(!d){d=P}4.w=a;4.Q=b;4.R=c;4.S=d;4.T=v E(a);z(i=0;i<a;i++){4.T[i]=c*n.s(b,a-i-1)}};m.q.12=o(a){7 b=0;7 c=[];7 d=v E(a.t);7 e,F,H,1h,1i,r;7 i,B,G;7 f;6(a.t>2){c.C([0,a.t-1]);U(c.t>0){r=c.1j();e=0;f=n.s(a[r[1]].8()-a[r[0]].8(),2)+n.s(a[r[1]].l()-a[r[0]].l(),2);z(i=r[0]+1;i<r[1];i++){H=4.13(a[i],a[r[0]],a[r[1]],f);6(H>e){e=H;F=i;6(e>b){b=e}}}6(e>4.R){d[F]=e;c.C([r[0],F]);c.C([F,r[1]])}}}B=4.14(a,d);G=4.15(a,d,b);p{B:B,G:G,1k:B.1l(/\\\\/g,"\\\\\\\\")}};m.q.V=o(a,b,c,d){7 e;6(!d){d=0.9}6(!c){c=3}6(!b){b="#W"}e=4.12(a);p{16:b,1m:c,17:d,1n:e.B,1o:e.G,w:4.w,Q:4.Q}};m.q.1p=o(a,b,c,d){6(!d){d=0.9}6(!c){c=3}6(!b){b="#W"}p v 1q.19(4.V(a,b,c,d))};m.q.1r=o(a,b,c,d,e,f,g,h){7 i,I;6(!b){b="#W"}6(!c){c=3}6(!d){d=0.9}6(!e){e=b}6(!f){f=d/3}6(g==J){g=P}6(h==J){h=P}I=v E(0);z(i=0;i<a.t;i++){I.C(4.V(a[i],b,c,d))}p v 1s.19({1t:I,16:e,17:f,1u:g,1v:h})};m.q.13=o(a,b,c,d){7 u,D;6(b.8()===c.8()&&b.l()===c.l()){D=n.K(n.s(c.8()-a.8(),2)+n.s(c.l()-a.l(),2))}X{u=((a.8()-b.8())*(c.8()-b.8())+(a.l()-b.l())*(c.l()-b.l()))/d;6(u<=0){D=n.K(n.s(a.8()-b.8(),2)+n.s(a.l()-b.l(),2))}6(u>=1){D=n.K(n.s(a.8()-c.8(),2)+n.s(a.l()-c.l(),2))}6(0<u&&u<1){D=n.K(n.s(a.8()-b.8()-u*(c.8()-b.8()),2)+n.s(a.l()-b.l()-u*(c.l()-b.l()),2))}}p D};m.q.14=o(a,b){7 i,Y,Z;7 c=0;7 d=0;7 e="";z(i=0;i<a.t;i++){6(b[i]!=J||i==0||i==a.t-1){7 f=a[i];7 g=f.8();7 h=f.l();7 j=n.1a(g*1b);7 k=n.1a(h*1b);Y=j-c;Z=k-d;c=j;d=k;e+=4.10(Y)+4.10(Z)}}p e};m.q.L=o(a){7 b;6(a>4.R){b=0;U(a<4.T[b]){b++}p b}};m.q.15=o(a,b,c){7 i;7 d="";6(4.S){d+=4.A(4.w-1)}X{d+=4.A(4.w-4.L(c)-1)}z(i=1;i<a.t-1;i++){6(b[i]!=J){d+=4.A(4.w-4.L(b[i])-1)}}6(4.S){d+=4.A(4.w-1)}X{d+=4.A(4.w-4.L(c)-1)}p d};m.q.A=o(a){7 b="";7 c,11;U(a>=1c){c=(1c|(a&1w))+1d;b+=(1e.1f(c));a>>=5}11=a+1d;b+=(1e.1f(11));p b};m.q.10=o(a){7 b=a<<1;6(a<0){b=~(b)}p(4.A(b))};m.M=o(y,x){4.y=y;4.x=x};m.M.q.8=o(){p 4.y};m.M.q.l=o(){p 4.x};m.1x=o(a){7 i,N;N=v E(0);z(i=0;i<a.t;i++){N.C(v m.M(a[i][0],a[i][1]))}p N};m.1y=o(a){7 i,O;O=v E(0);z(i=0;i<a.t;i++){O.C(v 1z(a[i][0],a[i][1]))}p O};',62,98,"||||this||if|var|lat|||||||||||||lng|PolylineEncoder|Math|function|return|prototype|current|pow|length||new|numLevels|||for|encodeNumber|encodedPoints|push|out|Array|maxLoc|encodedLevels|temp|boundaries|undefined|sqrt|computeLevel|latLng|latLngs|gLatLngs|true|zoomFactor|verySmall|forceEndpoints|zoomLevelBreaks|while|dpEncodeToJSON|0000ff|else|dlat|dlng|encodeSignedNumber|finalValue|dpEncode|distance|createEncodings|encodeLevels|color|opacity||fromEncoded|floor|1e5|0x20|63|String|fromCharCode|00001|first|last|pop|encodedPointsLiteral|replace|weight|points|levels|dpEncodeToGPolyline|GPolyline|dpEncodeToGPolygon|GPolygon|polylines|fill|outline|0x1f|pointsToLatLngs|pointsToGLatLngs|GLatLng".split("|"),0,{}));