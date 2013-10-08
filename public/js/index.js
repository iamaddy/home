$('#prev').on('click', function(){
	if($('[data-target="#step3"]').hasClass('active')){
		$('[data-target="#step2"]').attr('class', 'active');
		$('[data-target="#step3"]').attr('class', '');
		$('#step3').removeClass('active');
		$('#step2').addClass('active');
		return;
	}
	if($('[data-target="#step2"]').hasClass('active')){
		$('[data-target="#step1"]').attr('class', 'active');
		$('[data-target="#step2"]').attr('class', '');
		$('#step2').removeClass('active');
		$('#step1').addClass('active');
		$('#prev').attr('disabled', 'disabled');
		$('#next').html('下一步<i class="icon-arrow-right icon-on-right"></i>').removeAttr('disabled');
	}
});

$('#next').on('click', function(){
	if($('[data-target="#step1"]').hasClass('active')){
		if(!checkFisrtStep()){
			alert('请出入完整的信息');
			return;
		}
		var fee = 0;
		if(data.ndis > 10){
			fee += 90 + (data.ndis-10) * 8;
		} else {
			fee += 90;
		}
		fee += 35 * data.nperson * data.ntime;
		if(!data.ofee){
			fee += 8 * data.nperson * data.ofloor;
		}
		if(!data.nfee){
			fee += 8 * data.nperson * data.ofloor;
		}
		if (!confirm("费用大概需要"+fee+"元，确认要继续下订单？")) {
	           return; 
	    }
		$('[data-target="#step1"]').attr('class', 'complete');
		$('[data-target="#step2"]').attr('class', 'active');
		$('#step1').removeClass('active');
		$('#step2').addClass('active');
		$('#prev').removeAttr('disabled');
		$('#next').text('完成');
		return;
	}
	if($('[data-target="#step2"]').hasClass('active')){
		save();
	}
});
$('#addbig').on('click', addBig);
$('#addsmall').on('click', addSmall);
$('.btn-danger').live('click', function(){
	$(this).parent().parent().remove();
});
// 显示珍贵物品名称
$('#is_precious').click(function(){
	if(this.checked === true){
		$('#preciousdiv').show();
	} else{
		$('#preciousdiv').hide();
	}
});
var data = {};
function save(){
	var pname = $('#person_name').val();
	var phone = $('#inner_phone').val();
	if(!pname || !phone){
		alert("请输入完成的信息");
		$('#person_name').focus();
		return;
	}
	data.pname = pname;
	data.phone = phone;
	data.box = $('#box_num').val();
	data.is_dismount = $('#is_dismount')[0].checked ? 1 : 0;
	data.is_pack = $('#is_pack')[0].checked ? 1 : 0;
	data.is_precious = $('#is_precious')[0].checked ? 1 : 0;
	if(data.is_precious){
		data.precious_name = $('#precious').val();
	}
	data.bobject = [];
	data.sobject = [];
	var name = $('#bigtable  .name');
	var num = $('#bigtable  .num');
	var size = $('#bigtable  .size');
	var weight = $('#bigtable  .weight');
	for(var i = 0; i < name.length; i++){
		if(name[i].value === "" || num[i].value < 1){
			continue;
		}
		data.bobject.push({
			name: name[i].value,
			num: num[i].value,
			size: size[i].value,
			weight: weight[i].value
		});
	}
	var name = $('#smalltable  .name');
	var num = $('#smalltable  .num');
	var size = $('#smalltable  .size');
	var weight = $('#smalltable  .weight');
	for(var i = 0; i < name.length; i++){
		if(name[i].value === "" || num[i].value < 1){
			continue;
		}
		data.sobject.push({
			name: name[i].value,
			num: num[i].value,
			size: size[i].value,
			weight: weight[i].value
		});
	}
	$.post('./order/index/save', data, function(dt){
		if(dt.status){
			$('[data-target="#step2"]').attr('class', 'complete');
			$('[data-target="#step3"]').attr('class', 'active');
			$('#step2').removeClass('active');
			$('#step3').addClass('active');
			$('#prev').attr('disabled', 'disabled').hide();
			$('#next').hide();
			$('#redirecturl').attr('href', $('#redirecturl').attr('href') + dt.status);
			setTimeout(function(){
				document.location.href="order/index/details?id="+dt.status;
			}, 3000);
		} else {
			alert('提交失败，稍后再试');
		}
	}, 'json');
}
function checkFisrtStep(){
	var oaddress = $('#oldaddress').val(),
		ofloor = $('#oldfloor').val(),
		ofee = $('#oldfee').val() || 0,
		oleft = $('#oldleft')[0].checked ? 1 : 0;
	var naddress = $('#newaddress').val(),
		nfloor = $('#newfloor').val(),
		nfee = $('#newfee').val() || 0,
		nleft = $('#newleft')[0].checked ? 1 : 0;
	var ntime = $('#time').val(),
		nperson = $('#person').val(),
		ndis = $('#distance').val();
	if(!oaddress || !ofloor || !naddress || !nfloor  || !ntime || !nperson || !ndis){
		return false;
	}
	data.oaddress = oaddress;
	data.ofloor = ofloor;
	data.ofee = ofee;
	data.oleft = oleft;
	data.naddress = naddress;
	data.nfloor = nfloor;
	data.nfee = nfee;
	data.nleft = nleft;
	data.ntime= ntime;
	data.nperson = nperson;
	data.ndis = ndis;
	return true;
}
function initInt(){
	function ValidateNumber(){
		var pnumber = $(this).val();
		if (!/^\d+$/.test(pnumber)){
			this.value = /^\d+/.exec();
		}
		return false;
	}
	$('#person').on('keyup', ValidateNumber);
	$('#oldfloor').on('keyup', ValidateNumber);
	$('#newfloor').on('keyup', ValidateNumber);
	$('.num').live('keyup', ValidateNumber);
	$('#box_num').on('keyup', ValidateNumber);
	$('#inner_phone').on('keyup', ValidateNumber);
}
initInt();
function intiFloat(){
	function floatFun(){
		if(!this.value.match(/^[\+]?\d*?\.?\d*?$/)){
			this.value=this.t_value;
		}else {
			this.t_value=this.value;
		}
		if(this.value.match(/^(?:[\+]?\d+(?:\.\d+)?)?$/)){
			this.o_value=this.value;
		}
	}
	$('#oldfee').on('keyup keypress blur', floatFun);
	$('#newfee').on('keyup keypress blur', floatFun);
	$('#distance').on('keyup keypress blur', floatFun);
	$('#time').on('keyup keypress blur', floatFun);
	$('.size').live('keyup keypress blur', floatFun);
	$('.weight').live('keyup keypress blur', floatFun);	
}
intiFloat();
var strHtml = '<tr>'+
'<td><input type="text" class="span3 name" id="input" placeholder=""></td>'+
'<td><input type="text" class="span3 num" id="input" placeholder=""></td>'+
'<td><input type="text" class="span3 size" id="input" placeholder=""></td>'+
'<td><input type="text" class="span3 weight" id="input" placeholder=""></td>'+
'<td><button class="btn btn-xs btn-danger" title="删除">'+
		'<i class="icon-trash bigger-120"></i>'+
'</button></td>'+
'</tr>';
function addBig(){
	$('#bigtable').append(strHtml);
}
function addSmall(){
	$('#smalltable').append(strHtml);
}
$('#info_prec').tooltip('show');
$('#info_pack').tooltip('show');
$('#info_dismount').tooltip('show');

$('#inner_phone').on('blur', check);
$('#inner_phone').on('focus', function(){
	$('#phone-error').addClass('hide');
});
function check(){
	var number = $('#inner_phone').val();
	if(!/^(1(([35][0-9])|(47)|[8][0126789]))\d{8}$/.test(number)){
		$('#phone-error').removeClass('hide');
	} else{
		$('#phone-error').addClass('hide');
		$('#next').removeAttr('disabled');
	}
}