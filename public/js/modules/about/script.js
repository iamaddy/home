///////////////////////////////////////
// !//////////////onload general//////////////
///////////////////////////////////////


var color=[];
color['craft']="rgb(61,177,178)",
color['web']="rgb(216,124,42)",
color['print']="rgb(61,178,100)",
color['other']="rgb(102,102,102)";


//top nav

$('.nav_scroll_to').click(function(e){
	e.preventDefault();
	var target=$(this).attr('href');
	$(document).scrollTo(target,600);

})

$('.nav_scroll_top').click(function(e){
	e.preventDefault();
	$(document).scrollTo(0,600);

});

//language
//first load lang=eng
var lang='ch';
$('#show_ch').addClass('lang_selected');
$('.en').hide();
$('.ch').show();
draw_pie(lang,color);
draw_curve(lang,color);

$('#show_en').click(function(){
	$('.lang_selected').removeClass('lang_selected');
	$(this).addClass('lang_selected');
	$('.en').show();
	$('.ch').hide();
	$('.chart').empty();
	draw_pie('en',color);
	draw_curve('en',color);
	
});

$('#show_ch').click(function(){
	$('.lang_selected').removeClass('lang_selected');
	$(this).addClass('lang_selected');
	$('.ch').show();
	$('.en').hide();
	$('.chart').empty();
	draw_pie('ch',color);
	draw_curve('ch',color);
});


//color
 


///////////////////////////////////////
// !//////////////pie chart//////////////
///////////////////////////////////////
function draw_pie(lang,color){
	var paper=Raphael("piechart", 860, 480)
	
	var l_offset=30;	//offset from left px
	var t_offset=30;	//offset from top px
	var w_chart=800;	//width of chart px
	var h_chart=380;	//height of chart px
	
	var axis=paper.path("M "+l_offset+","+t_offset+" l 0,"+h_chart+" l "+w_chart+",0");
	 
	var axis_attr={
		stroke:"#fff",
		"stroke-width":1
	/* 	"arrow-end":"classic-wide-long" */
	}
	var mm_text={
		fill:"#fff",
		"font-size":16
	};
	
	
	//draw axis
	
	axis.attr(axis_attr);
	
	if(lang=='ch'){
		var x_txt="从业年数",
			y_txt="兴趣"
	}else{
			
		var x_txt="Year of Experience",
			y_txt="Interests"
	}
	
	//axis text
	paper.text((l_offset+w_chart)/2,t_offset+h_chart+20,x_txt).attr(mm_text);
	paper.text(10, h_chart/2, y_txt).rotate(-90).attr(mm_text);
	//draw measurements , 160px
	var offset=150;
	
	
	
	for(i=0;i<5;i++){
		var num=(i+1)*2,
			t=t_offset+h_chart,
			l=l_offset+(i+1)*offset,
			measure=paper.path("M "+l+","+t+"l 0,-10").attr(axis_attr),
			text=paper.text(l, t-20, num).attr(mm_text);
	
	}
	
	
	//get data from table
	
	
	
	Raphael.fn.pieChart = function (cx, cy, r, values, labels, stroke,fill,title,text,category) {
		var paper = this,
		     rad = Math.PI / 180,
		     chart = this.set();
		
		var circle_attr={ 
			stroke: 'none', 
			fill:"r#333-#000",
			opacity:0.75,
			cursor:"pointer",
		};
		var circle_text={
			fill:"#fff",
			"font-family":"MuseoSlab500",
			"font-size":19,
			cursor:"pointer",
		}
		
	    function sector(cx, cy, r, startAngle, endAngle, params) {
	        var x1 = cx + r * Math.cos(-startAngle * rad),
	            x2 = cx + r * Math.cos(-endAngle * rad),
	            y1 = cy + r * Math.sin(-startAngle * rad),
	            y2 = cy + r * Math.sin(-endAngle * rad);
	        return paper.path(["M", cx, cy, "L", x1, y1, "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, "z"]).attr(params);
	    }
	    
		var circle=paper.circle(cx, cy, r-15).attr(circle_attr).toFront(),
		 	txt=paper.text(cx,cy,title).attr(circle_text);
	
	
	    var angle = 0,
	        total = 0,
	        start = 0,
	        process = function (j) {
	            var value = values[j],
	                label = labels[j],
	
	                angleplus = 360 * value / total,
	                popangle = angle + (angleplus / 2),
	                ms = 500,
	                delta = 30,
	                p = sector(cx, cy, r, angle, angle + angleplus, {fill: fill, stroke: stroke, "stroke-width": 1,stroke:"rgba(0,0,0,0.6)",opacity:0.8}).toBack(),
	                
	               easing="elastic";
	               
	            p.mouseover(function () {
	            
	                p.stop().animate({opacity:1,transform: "s1.1 1.1 " + cx + " " + cy}, ms, easing);
					circle.stop().animate({r: 38, opacity: .5},200);
					txt.stop().attr({ text:label+ '\n' + value + '%' }).animate({ opacity: 1 }, 500, '<');
	
	            }).mouseout(function () {
	                p.stop().animate({transform: "",opacity:0.8}, ms, easing);
	                circle.stop().animate({r: r-15, opacity:0.75},200);
	                txt.stop().attr({ text:title}).animate({ opacity: 1 }, 500, '<');
	
	            });
	            circle.mouseover(function(){
					circle.stop().animate({r: 38, opacity: .5},200);
	
					txt.stop().attr({ text:text}).animate({ opacity: 1 }, 500, '<');
				});
	
				
				
	            angle += angleplus;
	            chart.push(p);
	/*             chart.push(txt); */
	            start += .1;
	        };
	        
	        if(category){
					circle.click(function(){
					
						view_works(category);
								
					});
					
					txt.click(function(){
						
						view_works(category);
					});
	
				}
	    for (var i = 0, ii = values.length; i < ii; i++) {
	        total += values[i];
	    }
	    for (i = 0; i < ii; i++) {
	        process(i);
	    }
	    return chart;
	};
	
	function view_works(target){
		current=$('.current').attr('name');
	
		$(document).scrollTo('#works',600);
		load_index(target,current);
	}
	
	var values_craft= [],
	    labels_craft= [],
	    values_web= [],
	    labels_web= [],
	    values_print= [],
	    labels_print= [],
	    values_app= [],
	    labels_app= [],
	    values_info= [],
	    labels_info= [],
	    values_3d= [],
	    labels_3d= [];
	$("#craft_data tr").each(function () {
	    values_craft.push(parseInt($("td", this).text(), 10));
	    labels_craft.push($("th", this).text());
	});
	$("#web_data tr").each(function () {
	    values_web.push(parseInt($("td", this).text(), 10));
	    labels_web.push($("th", this).text());
	});
	
	$("#print_data tr").each(function () {
	    values_print.push(parseInt($("td", this).text(), 10));
	    labels_print.push($("th", this).text());
	});
	$("#app_data tr").each(function () {
	    values_app.push(parseInt($("td", this).text(), 10));
	    labels_app.push($("th", this).text());
	});
	
	$("#info_data tr").each(function () {
	    values_info.push(parseInt($("td", this).text(), 10));
	    labels_info.push($("th", this).text());
	});
	
	$("#3d_data tr").each(function () {
	    values_3d.push(parseInt($("td", this).text(), 10));
	    labels_3d.push($("th", this).text());
	});
	$("table").hide();
	
/*
	var color['craft']="rgb(61,177,178)",
		color['web']="rgb(216,124,42)",
		color['print']="rgb(61,178,100)";
		color['other']="rgb(102,102,102)";
*/
	//draw pie
	//todo:href, last param
	
	var label_color="#fff";
	if(lang=='ch'){
		var craft_txt="手工 ",
			web_txt="网络",
			print_txt="平面",
			app_txt="App",
			info_txt="信息图",
			id_txt="立体",
			works_txt="查看\n作品"
	
	}else{
		var craft_txt="Craft",
			web_txt="Web",
			print_txt="Print",
			app_txt="App",
			info_txt="Infographic",
			id_txt="3D",
			works_txt="View works"
	}
	
	
	var pie_craft=paper.pieChart(720, 200, 100, values_craft, labels_craft, label_color,color['craft'],craft_txt,works_txt,"craft");
	 
	var pie_web=paper.pieChart(260, 100, 70, values_web, labels_web, label_color,color['web'],web_txt,works_txt,"web");    
	var pie_print=paper.pieChart(500, 250, 90, values_print, labels_print, label_color,color['print'],print_txt,works_txt,"print"); 
	
	var pie_app=paper.pieChart(100, 100, 50, values_app, labels_app, label_color,color['web'],app_txt,app_txt,""); 
	
	var pie_info=paper.pieChart(180, 190, 60, values_info, labels_info, label_color,color['web'],info_txt,info_txt,""); 
	
	var pie_3d=paper.pieChart(360, 290, 60, values_3d, labels_3d, label_color,color['other'],id_txt,id_txt,"");
	


}


///////////////////////////////////////
// !//////////////curve chart////////////
///////////////////////////////////////

function draw_curve(lang,color){



	var exp_graph=Raphael("curvechart", 960, 440);
	
	//draw ax
	
	var ax_attr={
		stroke:"#fff",
		"stroke-width":1
	/* 	"arrow-end":"classic-wide-long" */
	},
	dash_attr={
		"stroke-dasharray":".",
		stroke:"#fff",
		opacity:0.3
	},
	loc_attr={
		stroke:"#000",
		"stroke-width":2,
		opacity:0.6
	},
	
	loc_text={
		fill:"#000",
		"font-size":20,
		"font-family":"museoSlab500",
		opacity:0.6
	
	},
	
	mm_text={
		fill:"#fff",
		"font-size":16
	};
	
	w_chart=960,	//width of chart px
	h_chart=400;	//height of chart px
	t_offset=50;
	l_offset=10;
	var ax=exp_graph.path("M "+l_offset+","+h_chart/2+" h "+w_chart).attr(ax_attr);
	//draw horizontal lines
	
	exp_graph.path("M "+l_offset+","+h_chart/6+"h "+w_chart).attr(dash_attr).toBack();
	exp_graph.text(l_offset,h_chart/6,"8h").attr(mm_text);
	exp_graph.path("M "+l_offset+","+h_chart/3+"h "+w_chart).attr(dash_attr).toBack();
	exp_graph.text(l_offset,h_chart/3,"4h").attr(mm_text);
	
	exp_graph.path("M "+l_offset+","+h_chart/3*2+"h "+w_chart).attr(dash_attr).toBack();
	exp_graph.text(l_offset,h_chart/3*2,"4h").attr(mm_text);
	
	exp_graph.path("M "+l_offset+","+h_chart/6*5+"h "+w_chart).attr(dash_attr).toBack();
	exp_graph.text(l_offset,h_chart/6*5,"8h").attr(mm_text);
	
	
	//draw measurement
	
	
	var	num_year=2014-2001+1,
		
		yr_offset=Math.ceil((w_chart-l_offset)/num_year);
	
	for(i=0;i<num_year;i++){
		var t=h_chart/2,
			l=15+l_offset+yr_offset*i;
			year=2001+i;
			mm="'"+year.toString().slice(-2);
			exp_graph.path("M"+l+","+t+"l 0,-6").attr(ax_attr);
			exp_graph.text(l, t+15, mm).attr(mm_text);
	
	}
	
	//locator
	//locator="M16,3.5c-4.142,0-7.5,3.358-7.5,7.5c0,4.143,7.5,18.121,7.5,18.121S23.5,15.143,23.5,11C23.5,6.858,20.143,3.5,16,3.5z M16,14.584c-1.979,0-3.584-1.604-3.584-3.584S14.021,7.416,16,7.416S19.584,9.021,19.584,11S17.979,14.584,16,14.584z";
	
	
	
	
	//draw text
	title_attr={
		"font-family":"MuseoSlab500",
		"font-size":14,
		fill:"#ccc"
	}
	
	if(lang=='ch'){
		var y1_txt="工作(小时)",
			y2_txt="学习(小时)",
			x_txt="年份"
	}else{
		var y1_txt="Work load (hr)",
			y2_txt="Study load (hr)",
			x_txt="Year"
	}
	
	
	exp_graph.text(42, 30, y1_txt).attr(title_attr);
	
	exp_graph.text(43, h_chart-30, y2_txt).attr(title_attr);
	
	exp_graph.text(w_chart-15,h_chart/2+15,x_txt).attr(title_attr);
	
	//get points, education
	//start point
	
	function getPoint(yr,mth,hr,name){
		mth_offset=20+yr_offset/12*(mth-1);
		offset_x=Math.round(l_offset+(yr-2001)*yr_offset+mth_offset);
		if(name=='edu'){
			offset_y=Math.round(h_chart/2+h_chart/24*hr);
		}else if(name=='work'){
			offset_y=Math.round(h_chart/2-h_chart/24*hr);
		}
		
		return {x:offset_x,y:offset_y};
	}
	
	var edu_labels= [],
		edu_cos=[],
	    edu_yrs= [],
	    edu_mths=[],
	    edu_hrs=[],
	    edu_webs=[],
	    edu_prints=[],
	    edu_others=[],
	    work_labels= [],
	    work_cos=[],
	    work_yrs= [],
	    work_mths=[],
	    work_hrs=[],
	    work_webs=[],
	    work_prints=[],
	    work_others=[];

	
	
	var edu_fill={
		fill:'black',
		opacity:0.4,
		"stroke-width":0
	};
	
	var work_fill={
		fill:'black',
		opacity:0.2,
		"stroke-width":0
	};
	
	
	if(lang=='ch'){
		$("#edu_data tr").each(function () {
		    edu_labels.push($("th .ch", this).text());
			edu_cos.push($("td.co .ch", this).text());
		    edu_yrs.push(parseInt($("td.year", this).text(), 10));
		    edu_mths.push(parseInt($("td.month", this).text(), 10));
		    edu_hrs.push(parseInt($("td.hour", this).text(), 10));
		    edu_webs.push(parseInt($("td.web", this).text()));
		    edu_prints.push($("td.print", this).text());
		    edu_others.push($("td.other", this).text());
		});
		
		$("#work_data tr").each(function () {
		    work_labels.push($("th .ch", this).text());
		    work_cos.push($("td.co .ch", this).text());
		    work_yrs.push(parseInt($("td.year", this).text(), 10));
		    work_mths.push(parseInt($("td.month", this).text(), 10));
		    work_hrs.push(parseInt($("td.hour", this).text(), 10));
		    work_webs.push(parseInt($("td.web", this).text()));
		    work_prints.push($("td.print", this).text());
		    work_others.push($("td.other", this).text());
		});
	
	}else{
		$("#edu_data tr").each(function () {
		    edu_labels.push($("th .en", this).text());
			edu_cos.push($("td.co .en", this).text());
		    edu_yrs.push(parseInt($("td.year", this).text(), 10));
		    edu_mths.push(parseInt($("td.month", this).text(), 10));
		    edu_hrs.push(parseInt($("td.hour", this).text(), 10));
		    edu_webs.push(parseInt($("td.web", this).text()));
		    edu_prints.push($("td.print", this).text());
		    edu_others.push($("td.other", this).text());
		});
		
		$("#work_data tr").each(function () {
		    work_labels.push($("th .en", this).text());
		    work_cos.push($("td.co .en", this).text());
		    work_yrs.push(parseInt($("td.year", this).text(), 10));
		    work_mths.push(parseInt($("td.month", this).text(), 10));
		    work_hrs.push(parseInt($("td.hour", this).text(), 10));
		    work_webs.push(parseInt($("td.web", this).text()));
		    work_prints.push($("td.print", this).text());
		    work_others.push($("td.other", this).text());
		});

	}
		
	for(i=0;i<edu_labels.length;i++){
		process(i,edu_labels,edu_cos,edu_yrs,edu_mths,edu_hrs,edu_prints,edu_webs,edu_others,'edu');
		blanket.attr(edu_fill);
	}
	
	
	
	for(x=0;x<work_labels.length;x++){
		process(x,work_labels,work_cos,work_yrs,work_mths,work_hrs,work_prints,work_webs,work_others,'work');
		blanket.attr(work_fill);
	}
	$('table').hide();
	
	function process(j,labels,cos,yrs,mths,hrs,prints,webs,others,name){
		var role=labels[j]
			company=cos[j];
			
			
		
		var pt1=getPoint(yrs[j],mths[j],hrs[j],name),
			pt2=getPoint(yrs[j+1],mths[j+1],hrs[j+1],name);
		
		var cx=pt1.x+15,
			cy=pt1.y,
			cx2=pt2.x-15,
			cy2=pt2.y;
			
		if(pt1.y==pt2.y){
			var hover=true,
				width=12,
				stroke="rgba(0,0,0,0.8)",
				fill;
			if(prints[j]>0){
				fill=color['print'];
			}else if(webs[j]>0){
				fill=color['web'];
			}else if(others[j]>0){
				fill=color['other'];
			}else{
				fill="#fff";
				width=1;
				stroke="#fff";
				hover=false;
			}
			p=exp_graph.rect(pt1.x,pt1.y-width/2,(pt2.x-pt1.x-1),width).attr({fill:fill,stroke:stroke,opacity:0.75,cursor:"pointer"});
		}else{
			var hover=false;
			curve_attr={
				stroke:'#fff',
				"stroke-width":1,
			};
			p=exp_graph.path("M "+pt1.x+","+pt1.y+"C "+cx+" "+cy+" "+" "+cx2+" "+cy2+" "+pt2.x+" "+pt2.y).attr(curve_attr);
		}
		
		
		//curve
		
		
		if(hover==true){
			//p.attr({title:txt});
					
			var box_attr={
				fill:'white',
				//stroke:'#333',
				"stroke-width":0,
				//opacity:0
				
			},
					
			role_text={
				fill:fill,
				"font-size":16,
				"font-family":"museoSlab500"
				//opacity:0
			},
			company_text={
				fill:'black',
				"font-size":14,
				"font-family":"helvetica"
			}
			
			var	x=(pt1.x+pt2.x)/2,
				y=pt1.y-5,
				
				
				box_w,
				box_h,
				role_w,
				
				box = exp_graph.set();
				if(lang=='en'){
					role_w=role.length*9

				}else if(lang=='ch'){
					role_w=role.length*20

				}
				
				//calculate box_h,box_w
					//has company info
					if(company.length>0){
						box_h=53;
						
						company_w=company.length*7;
						if(role_w>company_w){
							box_w=role_w+35;
						}else{
							box_w=company_w+35;
						}
						box_y=y-38;
						role_y=y-20;
					}else{
						//no company info
						box_w=role_w+35;
						box_h=30;
						box_y=y-15;
						role_y=y;
					}
						box_x=x-box_w/2,
					
					
					box.push(
						
					    exp_graph.rect(box_x, box_y, box_w, box_h, 10).attr(box_attr),
					    exp_graph.path("M "+(x-4)+","+(y+14)+"l 4,5 l 4,-5 z").attr(box_attr),
						exp_graph.text(x,role_y,role).attr(role_text),
						exp_graph.text(x,y,company).attr(company_text)
					);
				
				
				box.attr({opacity:0}).toBack();
				 
			p.mouseover(function(){
				//show label
				
				box.stop().animate({opacity:1,transform:"t0,-25"},200,"backOut").toFront();
				this.attr({opacity:1}).toFront();
	
			}).mouseout(function(){
				this.attr({opacity:0.75});
	
	
				box.stop().animate({opacity:0},200,function(){
					box.transform("t0,25")
				}).toBack();
			});
		
		}
		//fill color
		
		blanket=exp_graph.path("M "+pt1.x+","+ h_chart/2+"L"+pt1.x+","+pt1.y+"C "+cx+" "+cy+" "+" "+cx2+" "+cy2+" "+pt2.x+" "+pt2.y+"L"+pt2.x+","+h_chart/2+"z").toBack();
	}
	
	

// !draw credits
	
	star_path="M14.615,4.928c0.487-0.986,1.284-0.986,1.771,0l2.249,4.554c0.486,0.986,1.775,1.923,2.864,2.081l5.024,0.73c1.089,0.158,1.335,0.916,0.547,1.684l-3.636,3.544c-0.788,0.769-1.28,2.283-1.095,3.368l0.859,5.004c0.186,1.085-0.459,1.553-1.433,1.041l-4.495-2.363c-0.974-0.512-2.567-0.512-3.541,0l-4.495,2.363c-0.974,0.512-1.618,0.044-1.432-1.041l0.858-5.004c0.186-1.085-0.307-2.6-1.094-3.368L3.93,13.977c-0.788-0.768-0.542-1.525,0.547-1.684l5.026-0.73c1.088-0.158,2.377-1.095,2.864-2.081L14.615,4.928z";
	
	
	color_credit="rgb(245,209,25)";
	var star_attr={
		fill:color_credit,
		opacity:0.6
	},
	star_txt1={
		fill:color_credit,
		"font-family":"MuseoSlab500",
		"font-size":16,
		opacity:0
	},
	star_txt2={
		fill:color_credit,
		"font-size":14,
		opacity:0
	}
	
	
	
	function drawStar(j,x,y,t1,t2,offset){
		scale=0.8;
		var st1=txt1[j],
			st2=txt2[j],
			sx=x[j],
			sy=y[j],
			yoffset=offset[j],
			star_txt=exp_graph.set();
		star=exp_graph.path(star_path).transform("t"+sx+","+sy+"s"+scale).attr(star_attr).toFront();
		
		star_txt.push(
		
			exp_graph.text(sx+15,sy+yoffset,st1).attr(star_txt1),
			exp_graph.text(sx+15,sy+yoffset+20,st2).attr(star_txt2)
		
		)
		star_txt.toBack();
		star.mouseover(function(){
			this.animate({opacity:1},300);
		
			star_txt.animate({opacity:1},300).toFront();
		
		}).mouseout(function(){
			this.animate({opacity:0.6},300);
			star_txt.animate({opacity:0},300).toFront();
		
		
		});
	
	};
	
	//star1, audacious
	star_x=[],
	star_y=[],
	txt1=[],
	txt2=[]
	offset=[];
	
	star_x[0]=520;
	star_y[0]=340;
	if(lang=='ch'){
		txt1[0]="最佳媒体运用奖";
		txt2[0]="Otago地区Audacious商业计划竞赛";
	}else{
		txt1[0]="Best use of interactive media";
		txt2[0]="Otago Audacious Business Plan Competition";
	}
	
	offset[0]=40;
	
	star_x[1]=630;
	star_y[1]=95;
	if(lang=='ch'){
		txt1[1]="入围决赛／最佳人物／最佳化妆";
		txt2[1]="新西兰48小时微电影大赛";

	}else{	
		txt1[1]="Finalist/Best Character/Best makeup";
		txt2[1]="48hours Film Competition";

	}
		offset[1]=-30;
	
	star_x[2]=680;
	star_y[2]=95;
	if(lang=='ch'){
		txt1[2]="纸艺道具艺术指导";
		txt2[2]="新西兰2010国际电影节";
	}else{
		txt1[2]="Art Director of Origami";
		txt2[2]="2010 NZ International Film Festival";
	}
	
	offset[2]=-30;
	
	
	for(i=0;i<=2;i++){
		drawStar(i,star_x,star_y,txt1,txt2,offset);
	
	}
	
	
	
	// !draw location
	nz_offset=20+l_offset+yr_offset*(2008-2001)+yr_offset/12*(2-1);
	exp_graph.path("M "+nz_offset+",20 l 0,360").attr(loc_attr).toBack();
	
	ch_offset=20+l_offset+yr_offset*(2012-2001)+yr_offset/12*(8-1);
	exp_graph.path("M "+ch_offset+",20 l 0,360").attr(loc_attr).toBack();
	
	//txt, china new zealand
	if(lang=='ch'){
		var ct1="中国",
			ct2="新西兰"
	}else{
		var ct1="China",
			ct2="New Zealand"
	}
	
	
	exp_graph.text(nz_offset-240,30,ct1).attr(loc_text);
	//exp_graph.path(locator).transform("t"+(nz_offset-165)+",12s0.9").attr(loc_text);
	exp_graph.text((nz_offset+ch_offset)/2,30,ct2).attr(loc_text);
	//exp_graph.path(locator).transform("t"+((nz_offset+ch_offset)/2-105)+",12s0.9").attr(loc_text);
	
	exp_graph.text(ch_offset+80,30,ct1).attr(loc_text);
	//exp_graph.path(locator).transform("t"+(ch_offset+35)+",12s0.9").attr(loc_text);
	
	
	
	//draw call to action
	call_attr={
		fill:color['craft'],
		"fill-opacity":0.3,
		stroke:color['craft'],
		"stroke-width":1,
		"stroke-opacity":0.5,
	
	};
	
	call_text={
		fill:"white",
		"font-size":17,
		"font-family":"museoslab500",
		//opacity:0
	}
	contact_text={
		fill:color['web'],
		"font-size":16,
		href:"",
		opacity:0
	}
	/*
	call_box=exp_graph.rect(830,78,120,227).attr(call_attr).toBack();
	call_box.mouseover(function(){
		this.animate({"fill-opacity":0.5});
	}).mouseout(function(){
		this.animate({"fill-opacity":0.3});
	});
	*/
// !bulb
/*
bulb="M12.75,25.498h5.5v-5.164h-5.5V25.498zM15.5,28.166c1.894,0,2.483-1.027,2.667-1.666h-5.334C13.017,27.139,13.606,28.166,15.5,28.166zM15.5,2.833c-3.866,0-7,3.134-7,7c0,3.859,3.945,4.937,4.223,9.499h1.271c-0.009-0.025-0.024-0.049-0.029-0.078L11.965,8.256c-0.043-0.245,0.099-0.485,0.335-0.563c0.237-0.078,0.494,0.026,0.605,0.25l0.553,1.106l0.553-1.106c0.084-0.17,0.257-0.277,0.446-0.277c0.189,0,0.362,0.107,0.446,0.277l0.553,1.106l0.553-1.106c0.084-0.17,0.257-0.277,0.448-0.277c0.189,0,0.36,0.107,0.446,0.277l0.554,1.106l0.553-1.106c0.111-0.224,0.368-0.329,0.604-0.25s0.377,0.318,0.333,0.563l-1.999,10.998c-0.005,0.029-0.02,0.053-0.029,0.078h1.356c0.278-4.562,4.224-5.639,4.224-9.499C22.5,5.968,19.366,2.833,15.5,2.833zM17.458,10.666c-0.191,0-0.364-0.107-0.446-0.275l-0.554-1.106l-0.553,1.106c-0.086,0.168-0.257,0.275-0.446,0.275c-0.191,0-0.364-0.107-0.449-0.275l-0.553-1.106l-0.553,1.106c-0.084,0.168-0.257,0.275-0.446,0.275c-0.012,0-0.025,0-0.037-0.001l1.454,8.001h1.167l1.454-8.001C17.482,10.666,17.47,10.666,17.458,10.666z";
	call=exp_graph.text(900,120,"Contact me...").attr(call_text);
	
	idea=exp_graph.path(bulb).transform("t880,120s2.5").attr({fill:"black","stroke-width":0,opacity:0.5}).toFront();
	
	idea.mouseover(function(){
		this.animate({fill:"white",opacity:1},200,function(){
			this.glow({
				width:20,
				color:color_credit
			});
			call.animate({opacity:1,transform:"t0,-35"},300,"backOut");
		});
	
	});
	
*/
	/* exp_graph.text(890,130,"?").attr(call_text); */
	
	if(lang=='ch'){
		var call1="期待与您合作",
			call2="联系我 >"
	}else{
		var call1="Work together",
			call2="Contact me >"
	}
	var call_to_action=exp_graph.text(900,85,call1).attr(call_text),
	
		link=exp_graph.text(900,80,call2).attr(contact_text);
	
	call_to_action.mouseover(function(){
		
			link.animate({opacity:1,transform:"t0,35"},300,"backOut").click(function(e){
				e.preventDefault();
				var target="#contact";

				$(document).scrollTo(target,600);
			
			});
		
	
	});

	
	
	
	
// !travel around the world
	if(lang=='ch'){
		travel_label="环游世界";
	}else{
		travel_label="Travel around the world";

	}

	travel_x=723;
	text_y=180;
	travel=exp_graph.circle(travel_x,h_chart/2,10).attr({fill:color['craft'],"stroke-width":0});
	/* exp_graph.path("M "+travel_x+", "+arrow_y+" l 0,"+(h_chart/2-arrow_y)).attr({stroke:color['craft']}); */
	travel_text=exp_graph.text(travel_x-5,text_y-3,travel_label).attr({fill:color['craft'],"font-size":16,opacity:0,"font-family":"MuseoSlab500"}).toBack();
	travel.mouseover(function(){
		travel_text.animate({opacity:1},300).toFront();
	
	}).mouseout(function(){
	
		travel_text.animate({opacity:0},300).toBack();
	
	});
	
	
}
	
	





///////////////////////////////////////
// !/////////footer contact//////////////
///////////////////////////////////////

icon_attr={
	fill:"white",
	"stroke-width":0,
	opacity:0.5
};

var trans="t-240,-240s0.07",
trans_nav="t-243,-238s0.05"

var mobile=Raphael("ft_mobile",35,35),
	mobile_path="M256.417,50c-113.771,0-206,92.229-206,206c0,113.771,92.229,206,206,206c113.771,0,205.998-92.229,205.998-206C462.415,142.229,370.188,50,256.417,50zM125.633,329.839c9.148-18.195,19.302-43.169,18.781-60.581c-12.322-14.86-19.284-34.009-19.284-53.513c0-54.989,53.346-95.195,113.07-95.195c59.354,0,113.071,39.903,113.071,95.195c0,57.574-63.431,111.343-150.461,89.971C185.005,315.486,150.151,325.366,125.633,329.839zM376.528,371.45c-16.641-3.036-40.297-9.741-51.024-16.373c-38.474,9.448-70.147-2.742-87.562-23.187c60.014,0.39,106.771-31.53,125.729-74.239c18.422,22.684,16.726,52.646,0.11,72.68C363.428,342.15,370.32,359.101,376.528,371.45z";

mobile.path(mobile_path).transform(trans).attr(icon_attr);


var email=Raphael("ft_email",35,35),
	email_path="M256,50C142.229,50,50,142.229,50,256s92.229,206,206,206s206-92.229,206-206S369.771,50,256,50z M358.042,306.051c-15.183,15.183-35.037,23.941-54.598,23.941c-11.388,0-20.73-4.38-25.109-11.388c-1.46-2.334-1.752-3.795-2.628-9.051c-11.095,12.848-22.773,18.395-39.123,18.395c-28.905,0-47.299-21.605-47.299-54.891c0-49.342,32.408-90.217,71.238-90.217c16.352,0,24.527,4.086,32.409,16.351l2.92-10.22h34.453c-1.752,5.84-7.008,23.649-8.468,30.072l-17.519,64.818c-0.875,3.21-1.167,6.131-1.167,8.759c0,4.671,2.627,7.298,7.591,7.298c5.547,0,12.555-2.92,18.394-7.591c14.307-11.095,23.649-32.701,23.649-55.183c0-26.86-12.555-50.219-33.576-63.355c-13.138-7.885-31.531-12.264-52.262-12.264c-61.605,0-104.816,40.876-104.816,98.686c0,56.059,38.831,92.846,97.517,92.846c15.183,0,31.24-2.336,44.671-6.424c11.388-3.503,18.686-7.008,32.701-15.474l17.811,25.692c-15.768,9.344-22.774,12.848-36.496,16.934c-19.854,6.133-40.876,9.345-61.897,9.345c-41.167,0-73.284-11.68-96.35-34.745c-21.897-21.605-33.868-52.555-33.868-86.423c0-36.204,12.263-67.444,35.619-91.095c26.861-26.859,62.19-40,107.444-40c68.029,0,116.496,43.797,116.496,104.818C385.779,263.131,375.852,288.241,358.042,306.051z M281.837,230.722c0,13.432-4.67,31.241-11.677,44.965c-7.592,14.891-17.811,23.356-28.613,23.356c-11.096,0-16.935-8.175-16.935-23.649c0-13.431,4.672-30.656,11.68-43.795c7.299-13.722,17.518-21.021,28.612-21.021C275.124,210.577,281.837,218.753,281.837,230.722z";
email.path(email_path).transform(trans).attr(icon_attr);

var dribbble=Raphael("ft_dribbble",35,35),
	dribbble_path="M273.961,276.658c10.744,28.04,18.644,56.891,23.666,86.439c-12.814,4.947-26.675,7.77-41.21,7.77c-26.58,0-51.013-9.156-70.496-24.371C208.737,310.713,237.863,287.424,273.961,276.658zM204.59,153.628c-29.497,14.993-51.626,42.312-59.648,75.167c38.856-0.266,72.475-4.526,100.63-12.814C233.687,194.62,220.019,173.817,204.59,153.628z M326.435,165.146c-19.407-14.993-43.653-24.014-70.018-24.014c-9.736,0-19.145,1.35-28.177,3.644c15.19,20.415,28.649,41.442,40.425,63.007C293.211,197.388,312.48,183.146,326.435,165.146zM264.752,254.305c-2.47-5.564-5.055-11.093-7.757-16.59c-32.233,10.125-70.703,15.258-115.295,15.32c-0.026,0.993-0.151,1.964-0.151,2.964c0,27.889,10.011,53.479,26.604,73.397C193.322,291.637,225.596,266.545,264.752,254.305zM297.928,271.559c9.729,26.003,17.168,52.661,22.246,79.92c26.08-17.476,44.516-45.361,49.581-77.643C343.585,269.29,319.657,268.492,297.928,271.559zM462.417,256c0,113.771-92.229,206-206,206s-206-92.229-206-206s92.229-206,206-206S462.417,142.229,462.417,256z M395.467,256c0-76.796-62.255-139.05-139.05-139.05c-76.796,0-139.05,62.254-139.05,139.05c0,76.793,62.254,139.05,139.05,139.05C333.212,395.05,395.467,332.793,395.467,256zM279.772,229.208c3.126,6.438,6.058,12.939,8.886,19.468c25.021-4.31,52.463-4.048,82.299,0.847c-1.444-25.685-11.262-49.139-26.898-67.609C328.037,201.881,306.529,217.624,279.772,229.208z";
dribbble.path(dribbble_path).transform(trans).attr(icon_attr);


/*
var facebook=Raphael("ft_facebook",35,35),
facebook_path="M256.417,50c-113.771,0-206,92.229-206,206s92.229,206,206,206s206-92.229,206-206S370.188,50,256.417,50zM317.385,171.192c0,0-20.604,0-28.789,0c-10.162,0-12.28,4.163-12.28,14.678c0,8.75,0,25.404,0,25.404h41.069l-3.951,44.596h-37.118v133.227h-53.2V256.435h-27.666v-45.16h27.666c0,0,0-6.493,0-35.565c0-33.379,17.849-50.807,57.437-50.807c6.484,0,36.833,0,36.833,0V171.192z";
facebook.path(facebook_path).transform(trans).attr(icon_attr);

*/

var linkedin=Raphael("ft_linkedin",35,35),
linkedin_path="M256.417,50c-113.771,0-206,92.229-206,206s92.229,206,206,206s206-92.229,206-206S370.188,50,256.417,50z M201.456,355.592h-45.229V209.469h45.229V355.592zM178.626,190.333c-14.771,0-26.746-12.072-26.746-26.963s11.975-26.963,26.746-26.963c14.77,0,26.745,12.072,26.745,26.963S193.396,190.333,178.626,190.333z M370.953,355.592h-45.01c0,0,0-55.666,0-76.703s-7.991-32.781-24.626-32.781c-18.103,0-27.562,12.231-27.562,32.781c0,22.504,0,76.703,0,76.703h-43.38V209.469h43.38v19.679c0,0,13.047-24.137,44.032-24.137c30.986,0,53.165,18.918,53.165,58.058C370.953,302.209,370.953,355.592,370.953,355.592z";
linkedin.path(linkedin_path).transform(trans).attr(icon_attr);


var weibo=Raphael("ft_weibo",35,35),
	weibo_path="M231.37,290.319c-2.825-1.122-6.355,0.234-8.011,3.012c-1.607,2.793-0.721,5.971,2.109,7.135c2.879,1.186,6.553-0.182,8.214-3.022C235.269,294.581,234.238,291.366,231.37,290.319zM219.894,240.975c-47.513,4.699-83.544,33.788-80.457,64.981c3.086,31.197,44.105,52.677,91.624,47.987c47.523-4.699,83.538-33.793,80.457-65.002C308.437,257.754,267.417,236.275,219.894,240.975z M261.586,314.548c-9.698,21.933-37.591,33.629-61.254,25.997c-22.846-7.375-32.517-29.933-22.515-50.253c9.821-19.924,35.379-31.192,57.99-25.308C259.209,271.03,271.151,293.106,261.586,314.548zM213.235,297.838c-7.359-3.087-16.87,0.086-21.409,7.204c-4.598,7.151-2.441,15.669,4.865,18.996c7.413,3.386,17.254,0.171,21.853-7.162C223.055,309.47,220.679,301.011,213.235,297.838zM256.417,50c-113.771,0-206,92.229-206,206c0,113.771,92.229,206,206,206c113.771,0,205.999-92.229,205.999-206C462.416,142.229,370.188,50,256.417,50zM230.901,370.958c-59.55,0-120.419-28.859-120.419-76.324c0-24.816,15.722-53.515,42.797-80.596c36.154-36.144,78.316-52.608,94.171-36.742c6.997,6.985,7.674,19.091,3.178,33.542c-2.344,7.279,6.831,3.247,6.831,3.263c29.222-12.234,54.717-12.955,64.035,0.358c4.973,7.097,4.497,17.046-0.085,28.576c-2.12,5.314,0.651,6.136,4.694,7.348c16.464,5.105,34.792,17.452,34.792,39.209C360.895,325.603,308.965,370.958,230.901,370.958zM338.705,220.029c1.928-5.949,0.722-12.731-3.771-17.708c-4.485-4.967-11.112-6.852-17.228-5.56v-0.011c-5.1,1.111-10.125-2.163-11.22-7.257c-1.095-5.116,2.163-10.147,7.273-11.236c12.507-2.66,26.056,1.207,35.23,11.385c9.195,10.179,11.652,24.042,7.722,36.208c-1.603,4.978-6.937,7.69-11.909,6.099c-4.972-1.613-7.689-6.953-6.088-11.92H338.705zM393.839,237.834c-0.006,0.011-0.006,0.032-0.006,0.043c-1.868,5.768-8.068,8.929-13.836,7.06c-5.789-1.869-8.951-8.053-7.082-13.837l-0.005-0.005c5.735-17.751,2.099-38.002-11.289-52.848c-13.404-14.846-33.164-20.518-51.423-16.641c-5.938,1.266-11.78-2.526-13.046-8.459c-1.271-5.928,2.516-11.771,8.454-13.041h0.01c25.666-5.458,53.473,2.51,72.324,23.412C396.798,184.399,401.887,212.863,393.839,237.834z";
	
weibo.path(weibo_path).transform(trans).attr(icon_attr);


var nav_email=Raphael("nav_email",30,30);

nav_email.path(email_path).transform(trans_nav).attr(icon_attr).mouseover(function(){
	this.stop().animate({opacity:1},200);

}).mouseout(function(){
	this.stop().animate({opacity:0.5},200);
});

var nav_weibo=Raphael("nav_weibo",30,30);

nav_weibo.path(weibo_path).transform(trans_nav).attr(icon_attr).mouseover(function(){
	this.stop().animate({opacity:1},200);

}).mouseout(function(){
	this.stop().animate({opacity:0.5},200);
});

var nav_linkedin=Raphael("nav_linkedin",30,30);

nav_linkedin.path(linkedin_path).transform(trans_nav).attr(icon_attr).mouseover(function(){
	this.stop().animate({opacity:1},200);

}).mouseout(function(){
	this.stop().animate({opacity:0.5},200);
});
