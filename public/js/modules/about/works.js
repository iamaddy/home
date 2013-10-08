$('.name,.date,.thumb,.kw,.no_js').hide();
//$('.index').hide()
$('#web_index').css({"z-index":"999","opacity":1}).siblings().css({"opacity":0});

///////////////////////////////////////
// !//////////////work nav//////////////
///////////////////////////////////////


$('.work_nav_item').live('click',function(){
	target=$(this).attr('name');
	current=$('.current').attr('name');
	load_index(target,current);	
	
});

function load_index(target,current){
	$('.current').removeClass('current');
	$('.work_nav_item[name~='+target+']').addClass('current');
	
	if($('#work_display:hidden').length>0){
		$('#'+current+'_index').stop().animate({opacity:0},300).css({"z-index":"0"});
		$('#'+target+'_index').stop().animate({opacity:1},300).css({"z-index":"999"});
		
	}else{
		$('#'+current+'_index').css({"z-index":"0"});
		$('#'+target+'_index').css({"z-index":"999"});
		back_to_index(target);

	}
	



}

///////////////////////////////////////
// !//////////////sliding//////////////
///////////////////////////////////////



//60 is topbar.height
//index_h=$('#index_box').height();
//display_h=$(window).height()-140;

$('.index li').live('click',function(){
	var this_id=$(this).attr('id');
	$('.index').stop().animate({opacity:0},300);
	
	
	var name=$(this).find('.name').text();
	var kw=$(this).find('.kw').text();
	

	
	
	$('#index_box').stop().animate({left:-1000},300,'easeInOutExpo',function(){
			$('#work_display').show().animate({opacity:1},300);
			
			$('.camera_wrap').removeClass('camera_wrap');
			
			//load proj slides
			load_slides(this_id);
		});
	
		

});

$('#prev_item.active').live('click',function(){
	var target=$(this).attr('name');
	$('#slides').stop().animate({opacity:0},300,function(){
		load_slides(target);
	});
	
});

$('#next_item.active').live('click',function(){
	var target=$(this).attr('name');

	$('#slides').stop().animate({opacity:0},300,function(){
		load_slides(target);
	});


});


function load_slides(id){

	var files=load_images(id);
			
	var num=files.length;
	var path='images/works/'+id+'/';
	var html='';
	
	//pull target name and keywords
	var current=$('.index').find('li[id="'+id+'"]');
	if(lang=='ch'){
		var name=current.find('.name .ch').text();

	}else if(lang=='en'){
		var name=current.find('.name .en').text();

	}
	var kw=current.find('.kw').text();
	//push
	$('#name').empty().append(name);
	$('#kw').empty().append(kw);


	//prev and next
	var prev=current.prev('li').attr('id');
	var next=current.next('li').attr('id');
	if(prev){
		$('#prev_item').addClass('active').attr('name',prev);
			
	}else{
		$('#prev_item').removeClass('active').attr('name','');
	}
	
	if(next){
		$('#next_item').addClass('active').attr('name',next);
	}else{
		$('#next_item').removeClass('active').attr('name','');

	}
	
	$('#slides').css({"height":"430px"}).empty();
	if(num==1){
		html='<img src="'+path+files[0]+'" />';
		$('#slides').append(html).animate({opacity:1},300);
	}else{
		for(i=0;i<num;i++){
			html+='<div data-src="'+path+files[i]+'"></div>';
		}
		//call plugin
		$('#slides').addClass('camera_wrap').append(html).animate({opacity:1},300,function(){
			$('#slides').camera({
				height: '430px',
				pagination: true,
				alignment: 'center',
				navigationHover: true,
				mobileNavHover: true,
				easing:			'easeInOutQuart',
				fx:				'scrollHorz',
				thumbnails:		false,
				hover:			true,
				overlayer:		true,
				opacityOnGrid:	false,
				playPause:		false,
				loader:			"none",
				time:			3000,
				transPeriod: 	600
			
			});
		});
		
		
	}

}



$('#back_to_index').live('click',function(e){
	e.preventDefault();
	target=$('.current').attr('name');

	back_to_index(target);

});

function back_to_index(target){
	$('#work_display').fadeOut();

	$('#index_box').stop().animate({left:0},300,'easeInOutExpo',function(){
		$('#'+target+'_index').animate({opacity:1});
	});

}

///////////////////////////////////////
// !//////////////draw circles//////////////
///////////////////////////////////////
w_index=222;
h_index=222;
circle_attr={
	stroke:"white",
	fill:"#333"
}

	
$('.index').each(function(){
	var title=$(this).find('h3').text();
	if(title=="Web"){
		var stroke=color['web'];
	}else if(title=="Print"){
		var stroke=color['print'];	
	}else if(title=="Craft"){
		var stroke=color['craft'];
	}else{
		var stroke=color['other'];
	}
	$(this).find('li').each(function(){
		r=105;
		c_path="M "+w_index/2+","+ h_index/2 +"m -"+r+", 0 a "+r+","+r+" 0 1,0 "+r*2+",0 a "+r+","+r+" 0 1,0 -"+r*2+",0";
		sq_path="M"+w_index/2+","+ h_index/2 +"m -"+r+",0 l 0,"+r+"l"+r*2+",0 l0,-"+r*2+"l-"+r*2+",0 z";

		//draw message
		var image=$(this).find('img').attr('src');
		if(lang=='ch'){
			var name=$(this).find('.name .ch').text();

		}else{
			var name=$(this).find('.name .en').text();

		}
		var date=$(this).find('.date').text();
		
		
		var paper=Raphael(this, w_index, h_index);
		//var canvas=paper.circle(w_index/2,h_index/2,w_index/2-1).attr(circle_attr);
		var canvas=paper.path(c_path).attr({stroke:"black",
			"stroke-width":12,
			"stroke-opacity":0.6,
			fill:"url("+image+")",
			"fill-opacity":1,
			cursor:"pointer"
		});
		//title,desc

		var msg_box=paper.set();
		msg_box.toBack().push(
			paper.text(w_index/2,96,name).attr({fill:"white",opacity:0,cursor:"pointer","font-size":18,"font-family":"museoslab500",cursor:"pointer"}),
			paper.text(w_index/2,h_index/2+20,date).attr({fill:"#ccc",opacity:0,cursor:"pointer","font-size":14,cursor:"pointer"})
		
		);		
		
		msg_box.hide();
		canvas.mouseover(canvas_hover).mouseout(canvas_out);
		
		
		msg_box.mouseover(canvas_hover).mouseout(canvas_out);
		
		function canvas_hover(){
			canvas.stop().animate({path:sq_path,stroke:stroke,"stroke-width":2,"stroke-opacity":1,"fill-opacity":0.1},200);
			msg_box.toFront().show().stop().animate({opacity:1},300);

		}
		function canvas_out(){
		
			canvas.stop().animate({path:c_path,stroke:"black","stroke-width":12,"stroke-opacity":0.6,"fill-opacity":1},200);
			msg_box.stop().animate({opacity:0},100).toBack().hide();

		}
				
	});
	
	

});


function load_images(id){
	
	switch(id){
		case "web-1":
			images=[
				"1.jpg"
			];
		break;
		
		case "web-2":
			images=[
				"1.jpg"
			];
			
		break;
		
		case "web-3":
			images=[
				
			];
		break;
		
		case "web-4":
			images=[
				"1.jpg"
			];
		break;
		
		case "web-5":
			images=[
				"1.jpg"
			];
		break;
		case "web-6":
			images=[
				"1.jpg"/* ,"2.jpg","3.jpg" */
			];
		break;
		case "web-7":
			images=[
				"1.jpg"/* ,"2.jpg","3.jpg" */
			];
		break;
		
		case "print-1":
			images=[
				"1.jpg"/* ,"2.jpg" */
			];
		break;
		case "print-2":
			images=[
				"1.jpg"/* ,"2.jpg" */
			];
		break;
		case "print-3":
			images=[
				"1.jpg"
			];
		break;

		case "print-4":
			images=[
				"1.jpg"
			];
		break;
		
		case "print-5":
			images=[
				"1.jpg"/* ,"2.jpg","3.jpg" */
			];
		break;
		
		case "print-6":
			images=[
				"1.jpg"
			];
		break;
		
		case "print-7":
			images=[
				"1.jpg"
			];
		break;
		
		case "craft-1":
			images=[
				"1.gif"
			];
		break;
		
		
		case "other-1":
			images=[
				"1.jpg"
			];
		break;
		
		
	}
	return images;
}
