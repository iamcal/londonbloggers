function map(){

	this.tiles = {};
	this.tiles.exists = {};

	this.w = 0;
	this.h = 0;
	this.cursor_open = 'url(http://maps.google.com/intl/en_ALL/mapfiles/openhand_8_8.cur), default';
	this.cursor_closed = 'url(http://maps.google.com/intl/en_ALL/mapfiles/closedhand_8_8.cur), default';

	this.drag = {};
	this.start_tile_x = 1;
	this.start_tile_y = 1;

	this.zoom_level = 3;

	this.view_box = {};

	this.slab = {
		x : 0,
		y : 0,
		mx : 1,
		my : 1,
		zoom : 3,
		zooms : {},
	};

	this.tile_path = '';

	this.parent = null;
	this.w = 0;
	this.h = 0;
	this.cover = null;

	this.onzoomchange = null;
	this.onpan = null;

	// how often to trigger event while panning
	this.pan_trigger_ms = 500;

	// how many pixels we allow you to drag and it still count as a click
	this.click_drag_allow = 5;
};

map.prototype.init = function(tiles, zooms){
	this.tile_path = tiles;
	this.slab.zooms = zooms;
	this.slab.tx = this.slab.zooms[this.zoom_level][0];
	this.slab.ty = this.slab.zooms[this.zoom_level][1];
}

map.prototype.create = function(parent, w, h){

	this.parent = parent;
	this.w = w;
	this.h = w;


	//
	// create the map cover
	//

	this.cover = document.createElement('DIV');
	this.cover.style.position = 'absolute';
	this.cover.style.left = '0px';
	this.cover.style.top = '0px';
	this.cover.style.width = this.w+'px';
	this.cover.style.height = this.h+'px';
	//this.cover.style.backgroundColor = 'pink';
	this.cover.style.zIndex = 20;
	this.cover.style.cursor = this.cursor_open;


	//
	// drag handlers
	//

	this.drag.is_dragging = 0;
	this.drag.new_slab_x = 0;
	this.drag.new_slab_y = 0;

	this.click_timer = null;
	this.click_timer_wait = 200;

	var self = this;
	this.cover.onmousedown = function(e){
		var myself = self;
		return myself.onmousedown_cover(e);
	};
	this.cover.mousemovefunc = function(e){
		var myself = self;
		return myself.onmousemove_cover(e);
	};
	this.cover.mouseupfunc = function(e){
		var myself = self;
		return myself.onmouseup_cover(e);
	};
	this.cover.onclick = function(e){
		var myself = self;
		return myself.onclick_cover(e);
	};
	this.cover.ondblclick = function(e){
		var myself = self;
		return myself.ondblclick_cover(e);
	};
	this.onPanTimer = function(){
		var myself = self;
		return myself.pan_timer_fired();
	};
	this.onClickTimer = function(){
		var myself = self;
		return myself.click_timer_fired();
	};

	this.parent.appendChild(this.cover);


	//
	// zoom bar
	//

	this.zoom = {};
	this.zoom.barelm = document.createElement('DIV');
	this.zoom.barelm.style.position = 'absolute';
	this.zoom.barelm.style.width = '19px';
	this.zoom.barelm.style.height = '43px';
	this.zoom.barelm.style.left = '20px';
	this.zoom.barelm.style.top = '20px';
	this.zoom.barelm.style.backgroundImage = 'url(images/zoom_widget.gif)';
	//this.zoom.barelm.style.backgroundColor = 'red';
	this.zoom.barelm.style.zIndex = 22;
	this.zoom.barelm.style.cursor = 'pointer';
	this.zoom.barelm.onclick = function(e){
		return self.onzoombar_click(e);
	}
	this.parent.appendChild(this.zoom.barelm);


	//
	// create the 'slab'
	//

	this.slab.x = 0 - ((this.start_tile_x-1) * 256);
	this.slab.y = 0 - ((this.start_tile_y-1) * 256);

	this.drag.new_slab_x = this.slab.x;
	this.drag.new_slab_y = this.slab.y;

	this.slab.x_min = (-256 * this.slab.tx) + this.w;
	this.slab.y_min = (-256 * this.slab.ty) + this.h;

	this.slab.x_max = (-256 * (this.slab.mx-1));
	this.slab.y_max = (-256 * (this.slab.my-1));

	this.slab.elm = document.createElement('DIV');
	this.slab.elm.id = 'slab';
	this.slab.elm.style.position = 'absolute';
	this.slab.elm.style.left = this.slab.x+'px';
	this.slab.elm.style.top  = this.slab.y+'px';
	this.slab.elm.style.width = '100px';
	this.slab.elm.style.height = '100px';
	//this.slab.elm.style.backgroundColor = 'lime';
	this.slab.elm.style.zIndex = 11;
	this.parent.appendChild(this.slab.elm);

	// create some tiles
	this.recalc_visibles();

	// the arrow represents sharing
	this.slab.a = document.createElement('img');
	this.slab.a.src = 'images/cross.gif';
	this.slab.a.style.position = 'absolute';
	this.slab.a.style.width = '11px';
	this.slab.a.style.height = '11px';
	this.slab.a.style.zIndex = 19;
	this.parent.appendChild(this.slab.a);


	// position everything correctly
	// TODO: some of the stuff this does has just been done
	// above. it should be removed from the function above.
	this.set_size(w, h);
};

map.prototype.onmousedown_cover = function(e){
	if (this.drag.is_dragging) return;

	var event = e ? e : window.event;
	this.drag.is_dragging = 1;
	this.drag.could_click = 1;
	this.drag.click_x = event.layerX;
	this.drag.click_y = event.layerY;
	this.drag.mouse_x = event.screenX;
	this.drag.mouse_y = event.screenY;
	this.drag.old_slab_x = this.slab.x;
	this.drag.old_slab_y = this.slab.y;
	this.drag.new_slab_x = this.slab.x;
	this.drag.new_slab_y = this.slab.y;

	document.documentElement.style.cursor = this.cursor_closed;
	this.cover.style.cursor = this.cursor_closed;

	document.onselectstart = function() { return false; };

	document.addEventListener("mousemove", this.cover.mousemovefunc, false);
	document.addEventListener("mouseup"  , this.cover.mouseupfunc, false);

	return false;
};

map.prototype.onmousemove_cover = function(e){

	if (!this.drag.is_dragging) return;
	var event = e ? e : window.event;

	var this_x = event.screenX;
	var this_y = event.screenY;

	if (this.drag.could_click){
		if (Math.abs(this_x - this.drag.mouse_x) > this.click_drag_allow) this.drag.could_click = 0;
		if (Math.abs(this_y - this.drag.mouse_y) > this.click_drag_allow) this.drag.could_click = 0;
	}

	this.drag.new_slab_x = this.drag.old_slab_x + (this_x - this.drag.mouse_x);
	this.drag.new_slab_y = this.drag.old_slab_y + (this_y - this.drag.mouse_y);

	// do the zero check last, so that if the map is too small to fill the
	// screen, it appears in the top left corner, rather than bottom right

	this.check_slab_pos();

	this.slab.elm.style.left = this.drag.new_slab_x+'px';
	this.slab.elm.style.top  = this.drag.new_slab_y+'px';

	this.recalc_visibles();
};

map.prototype.onmouseup_cover = function(e){

	this.drag.is_dragging = 0;
	this.slab.x = this.drag.new_slab_x;
	this.slab.y = this.drag.new_slab_y;

	document.documentElement.style.cursor = 'default';
	this.cover.style.cursor = this.cursor_open;

	document.onselectstart = function() { return true; };

	this.pan_ended();
};

map.prototype.onclick_cover = function(e){

	if (this.click_timer == null){
		this.click_timer = window.setTimeout(this.onClickTimer, this.click_timer_wait);
	}
};

map.prototype.click_timer_fired = function(e){

	this.click_timer = null;

	if (this.drag.could_click){
		if (this.onclick){
			var x = 0 - (this.slab.x - this.drag.click_x);
			var y = 0 - (this.slab.y - this.drag.click_y);
			this.onclick(x, y);
		}
	}
};

map.prototype.ondblclick_cover = function(e){

	if (this.click_timer != null){
		window.clearTimeout(this.click_timer);
		this.click_timer = null;
	}

	var event = e ? e : window.event;

	// first, move the map to center the clicked point

	this.set_slab_pos(
		this.slab.x - (event.layerX - (this.w / 2)),
		this.slab.y - (event.layerY - (this.h / 2)),
		1
	);

	if (this.slab.zoom == 1){
		this.recalc_visibles();
	}else{
		//recalc_visibles();
		this.set_zoom_level(this.slab.zoom - 1);
	}

	//console.log(event);
};


map.prototype.resize = function(l, t, w, h){

	this.parent.style.left = l+'px';
	this.parent.style.top = t+'px';
	this.parent.style.width = w+'px';
	this.parent.style.height = h+'px';

	this.set_size(w, h);
};

map.prototype.recalc_visibles = function(force){

	// calculate which tiles should be visible

	this.view_box = {
		x_lo : 0 - (this.drag.new_slab_x - 1),
		x_hi : 0 - ((this.drag.new_slab_x - this.w) - 1),

		y_lo : 0 - (this.drag.new_slab_y - 1),
		y_hi : 0 - ((this.drag.new_slab_y - this.h) - 1),
	};

	var lo_x = 0 - ((this.drag.new_slab_x - 1) >> 8);
	var hi_x = 0 - (((this.drag.new_slab_x - this.w) - 1) >> 8);

	var lo_y = 0 - ((this.drag.new_slab_y - 1) >> 8);
	var hi_y = 0 - (((this.drag.new_slab_y - this.h) - 1) >> 8);

	if (hi_x > this.slab.tx) hi_x = this.slab.tx;
	if (hi_y > this.slab.ty) hi_y = this.slab.ty;

	if (lo_x < this.slab.mx) lo_x = this.slab.mx;
	if (lo_y < this.slab.my) lo_y = this.slab.my;

	var changes = 0;

	if (this.tiles.last_lo_x != lo_x) changes = 1;
	if (this.tiles.last_hi_x != hi_x) changes = 1;
	if (this.tiles.last_lo_y != lo_y) changes = 1;
	if (this.tiles.last_hi_y != hi_y) changes = 1;

	if (changes || force){
		this.tiles.last_lo_x = lo_x;
		this.tiles.last_hi_x = hi_x;
		this.tiles.last_lo_y = lo_y;
		this.tiles.last_hi_y = hi_y;

		// first, recycle old tiles
		for (i in this.tiles.exists){
			var a = i.split('-');
			var kill = 0;
			if (a[0] < lo_x) kill = 1;
			if (a[0] > hi_x) kill = 1;
			if (a[1] < lo_y) kill = 1;
			if (a[1] > hi_y) kill = 1;
			if (kill){
				this.tiles.exists[i].parentNode.removeChild(this.tiles.exists[i]);
				delete(this.tiles.exists[i]);
			}
		}

		// next, create missing tiles
		for (var x=lo_x; x<=hi_x; x++){
		for (var y=lo_y; y<=hi_y; y++){

			var key = x + '-' + y;

			if (!this.tiles.exists[key]){

				this.tiles.exists[key] = this.create_tile(this.slab.elm, x, y);
			}			
		}
		}
	}


	//
	// tell our owner we've pan'd
	//

	if (!this.panTimer){
		this.panTimer = window.setTimeout(this.onPanTimer, this.pan_trigger_ms);
	}
};

map.prototype.pan_ended = function(){

	if (this.panTimer){
		window.clearTimeout(this.panTimer);
		this.panTimer = 0;
	}

	if (this.onpan) this.onpan();
};

map.prototype.pan_timer_fired = function(){

	this.panTimer = 0;
	if (this.onpan) this.onpan();
};

map.prototype.create_tile = function(slab, x, y){

	var px = 256 * (x - 1);
	var py = 256 * (y - 1);

	var tile = document.createElement('IMG');
	tile.style.position = 'absolute';
	tile.style.left = px+'px';
	tile.style.top = py+'px';
	tile.style.width = '256px';
	tile.style.height = '256px';
	tile.style.zIndex = 1;
	//tile.style.border = '1px solid blue';

	var fx = ''+x; while (fx.length < 3) fx = '0' + fx;
	var fy = ''+y; while (fy.length < 3) fy = '0' + fy;

	tile.src = this.tile_path+'/tile_'+this.slab.zoom+'_'+fx+'_'+fy+'.jpg';
	slab.appendChild(tile);

	return tile;
};

map.prototype.set_size = function(w, h){

	this.w = w;
	this.h = h;

	this.cover.style.width = this.w+'px';
	this.cover.style.height = this.h+'px';

	this.slab.a.style.left = (Math.round(this.w / 2) - 5) + 'px';
	this.slab.a.style.top  = (Math.round(this.h / 2) - 5) + 'px';

	this.recalc_minmax();

	this.set_slab_pos(this.slab.x, this.slab.y);

	this.recalc_visibles();
};

map.prototype.recalc_minmax = function(){

	this.slab.x_min = (-256 * this.slab.tx) + this.w;
	this.slab.y_min = (-256 * this.slab.ty) + this.h;

	this.slab.x_max = (-256 * (this.slab.mx-1));
	this.slab.y_max = (-256 * (this.slab.my-1));
};

map.prototype.check_slab_pos = function(){

	if (this.drag.new_slab_x < this.slab.x_min) this.drag.new_slab_x = this.slab.x_min;
	if (this.drag.new_slab_y < this.slab.y_min) this.drag.new_slab_y = this.slab.y_min;

	if (this.drag.new_slab_x > this.slab.x_max) this.drag.new_slab_x = this.slab.x_max;
	if (this.drag.new_slab_y > this.slab.y_max) this.drag.new_slab_y = this.slab.y_max;

	if (256 * this.slab.tx < this.w) this.drag.new_slab_x = Math.round((this.w - 256 * this.slab.tx) / 2);
	if (256 * this.slab.ty < this.h) this.drag.new_slab_y = Math.round((this.h - 256 * this.slab.ty) / 2);
};

map.prototype.set_zoom_level = function(z){

	if (z == this.slab.zoom) return;

	//
	// we calculate the new slab x/y point by figuring out how far the
	// visible center of the map is from the actual center of the map,
	// then scaling that number and working backwards to get the slab
	// origin. we need to do this because different zoom levels have
	// different padding at the edges to make it a multiple of 256.
	//

	var mul = Math.pow(2, z - this.slab.zoom);

	var new_slab_x = 0 - ((((((0 - this.slab.x) + (this.w / 2)) - (this.slab.zooms[this.slab.zoom][0] * 128)) / mul) + (this.slab.zooms[z][0] * 128)) - (this.w / 2))
	var new_slab_y = 0 - ((((((0 - this.slab.y) + (this.h / 2)) - (this.slab.zooms[this.slab.zoom][1] * 128)) / mul) + (this.slab.zooms[z][1] * 128)) - (this.h / 2))


	//
	// cull all existing tiles
	//

	for (i in this.tiles.exists){
		this.tiles.exists[i].parentNode.removeChild(this.tiles.exists[i]);
		delete(this.tiles.exists[i]);
	}


	//
	// recalc min/max for new zoom level
	//

	this.zoom_level = z;
	this.slab.zoom = z;
	this.slab.tx = this.slab.zooms[z][0];
	this.slab.ty = this.slab.zooms[z][1];

	this.recalc_minmax();


	//
	// set new slab pos (and have it corrected)
	//

	this.set_slab_pos(new_slab_x, new_slab_y);


	//
	// and build our view of the world
	//

	this.recalc_visibles(1);


	//
	// tell our owner we've changed zoom levels
	//

	if (this.onzoomchange) this.onzoomchange();
};

map.prototype.set_slab_pos = function(x, y, no_check){

	this.drag.new_slab_x = Math.round(x);
	this.drag.new_slab_y = Math.round(y);

	if (!no_check){
		this.check_slab_pos();
	}

	this.slab.x = this.drag.new_slab_x;
	this.slab.y = this.drag.new_slab_y;

	this.slab.elm.style.left = this.drag.new_slab_x+'px';
	this.slab.elm.style.top  = this.drag.new_slab_y+'px';
};

map.prototype.get_slab = function(){

	return this.slab.elm;
};

map.prototype.center_on_pos = function(x, y){

	this.set_slab_pos((this.w/2)-x, (this.h/2)-y);

	this.recalc_visibles();
};

map.prototype.onzoombar_click = function(e){

	var event = e ? e : window.event;

	var y = event.layerY;

	if (y<=21){
		if (this.slab.zoom > 1){
			this.set_zoom_level(this.slab.zoom - 1);
		}
		return;
	}

	if (y >=23){
		if (this.slab.zoom < 4){
			this.set_zoom_level(this.slab.zoom + 1);
		}
		return;
	}
};
