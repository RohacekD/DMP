<?php
include 'Settings.class.php';
header("Content-Type: text/javascript");
?>

function jsRedirect(select){
	var SelectedOption = select.options[select.selectedIndex];
	window.location="<?php echo Settings::$url_base?>/"+select.id+"/"+SelectedOption.value;
}

function Wysiwyg(form, target){
	var WysiwygDIV = form.getElementById("wysiwyg");
	form[target].value=WysiwygDIV.innerHTML;
	alert(form[target]);
}

var ajax;
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
	ajax=new XMLHttpRequest();
}
else
{// code for IE6, IE5
	ajax=new ActiveXObject("Microsoft.XMLHTTP");
}
  
function Whisper(input, form){
	var MyInput = input;

	function Change(input){
		if (ajax.readyState==4 && ajax.status==200){
			var bubble = document.getElementById(input.name+"-whisper");
			if(ajax.responseText=="")
				bubble.style.display="none";
			else{
				bubble.style.display="inline-block";
				var inner = bubble.firstChild.firstChild;
				inner.innerHTML=ajax.responseText;
			}
		}
	}

	//ajax.onreadystatechange=Change(MyInput);
	ajax.open("POST", "<?php echo Settings::$url_base?>/Ajax/", false);
	ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	ajax.send("form="+form+"&type="+MyInput.name+"&value="+MyInput.value);
	Change(MyInput);
}

function Whisper_notNull(input) {
	var bubble = document.getElementById(input.name+"-whisper");
	if (input.value=="") {
		bubble.style.display="inline-block";
		var inner = bubble.firstChild.firstChild;
		inner.innerHTML="Toto pole musí obsahovat text.";
	}
	else
		bubble.style.display="none";
}


function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

function Whisper_email(input, form){
	if(validateEmail(input.value)){
		Whisper(input, form);
	}
	else{
		var bubble = document.getElementById(input.name+"-whisper");
		bubble.style.display="inline-block";
		var inner = bubble.firstChild.firstChild;
		inner.innerHTML="Zadejte validní email.";
	}
}
	

function Whisper_pass(input, repeat){
	var bubble = document.getElementById(input.name+"-whisper");
	var next = document.getElementById(repeat);
	Whisper_pass_repeat(next, input.name);
	if(input.value.length<9){
		bubble.style.display="inline-block";
		var inner = bubble.firstChild.firstChild;
		inner.innerHTML="Heslo je moc krátké.";
	}
	else{
		bubble.style.display="none";
	}
}

function Whisper_pass_repeat(input, first){
	var bubble = document.getElementById(input.name+"-whisper");
	var first = document.getElementById(first);
	if(input.value!=first.value){
		bubble.style.display="inline-block";
		var inner = bubble.firstChild.firstChild;
		inner.innerHTML="Hesla se neschodují.";
	}
	else{
		bubble.style.display="none";
	}
}

function Next_User(button){
/*var trm = document.getElementById("new-user").getElementsByTagName("TR")[1];
var a = trm.innerHTML;*/
var a = "\t\t\t<tr>\r\n\t\t\t\t<td><input type=\"text\" name=\"name[]\" id=\"[][name]\" \/><\/td>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<label class=\"select\">\r\n\t\t\t\t\t\t<select name=\"title[]\">\r\n\t\t\t\t\t\t\t<option value=\"NULL\">\u017D\u00E1dn\u00FD<\/option>\r\n\t\t\t\t\t\t\t<option value=\"Bc.\">Bc.<\/option>\r\n\t\t\t\t\t\t\t<option value=\"Ing.\">Ing.<\/option>\r\n\t\t\t\t\t\t\t<option value=\"Mgr.\">Mgr.<\/option>\r\n\t\t\t\t\t\t\t<option value=\"PhDr.\">PhDr.<\/option>\r\n\t\t\t\t\t\t<\/select>\r\n\t\t\t\t\t<\/label>\r\n\t\t\t\t<\/td>\r\n\t\t\t\t<td><input type=\"text\" name=\"firstName[]\" id=\"firstName[]\" \/><\/td>\r\n\t\t\t\t<td><input type=\"text\" name=\"lastName[]\" id=\"lastName[]\" \/><\/td>\r\n\t\t\t\t<td><input type=\"text\" name=\"email[]\" id=\"[][email]\" \/><\/td>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<label class=\"select\">\r\n\t\t\t\t\t\t<select name=\"access[]\">\r\n\t\t\t\t\t\t\t<option value=\"0\">U\u010Ditel<\/option>\r\n\t\t\t\t\t\t\t<option value=\"1\">\u00DA\u010Detn\u00ED<\/option>\r\n\t\t\t\t\t\t\t<option value=\"2\">Z\u00E1stupce<\/option>\r\n\t\t\t\t\t\t\t<option value=\"3\">\u0158editel<\/option>\r\n\t\t\t\t\t\t\t<option value=\"4\">Admin<\/option>\r\n\t\t\t\t\t\t<\/select>\r\n\t\t\t\t\t<\/label>\r\n\t\t\t\t<\/td>\r\n\t\t\t\t<td align=\"center\"><input type=\"checkbox\" name=\"info_email[]\" id=\"[][info_email]\" \/><\/td>\r\n\t\t\t<\/tr>\r\n";
var form=document.createElement("tr");
form.innerHTML=a;
button.parentNode.parentNode.parentNode.insertBefore(form,button.parentNode.parentNode);
}

function Submit_all(checkbox){
	var check = document.getElementsByName("info_email[]");
	for(var i=0, n=check.length;i < n;i++) {
		check[i].checked = checkbox.checked;
	}
}


/*----------------------Calendar -----------------*/
function positionInfo(object) {
    var p_elm = object;
    this.getElementLeft = getElementLeft;
    function getElementLeft() {
        var x = 0;
        var elm;
        if(typeof(p_elm) == "object"){
            elm = p_elm;
        } else {
            elm = document.getElementById(p_elm);
        }
        while (elm != null) {
            x+= elm.offsetLeft;
            elm = elm.offsetParent;
        }
        return parseInt(x);
    }
    this.getElementWidth = getElementWidth;
    function getElementWidth(){
        var elm;
        if(typeof(p_elm) == "object"){
            elm = p_elm;
        } else {
            elm = document.getElementById(p_elm);
        }
        return parseInt(elm.offsetWidth);
    }
    this.getElementRight = getElementRight;
    function getElementRight(){
        return getElementLeft(p_elm) + getElementWidth(p_elm);
    }
    this.getElementTop = getElementTop;
    function getElementTop() {
        var y = 0;
        var elm;
        if(typeof(p_elm) == "object"){
            elm = p_elm;
        } else {
            elm = document.getElementById(p_elm);
        }
        while (elm != null) {
            y+= elm.offsetTop;
            elm = elm.offsetParent;
        }
        return parseInt(y);
    }
    this.getElementHeight = getElementHeight;
    function getElementHeight(){
        var elm;
        if(typeof(p_elm) == "object"){
            elm = p_elm;
        } else {
            elm = document.getElementById(p_elm);
        }
        return parseInt(elm.offsetHeight);
    }
    this.getElementBottom = getElementBottom;
    function getElementBottom(){
        return getElementTop(p_elm) + getElementHeight(p_elm);
    }
}
function CalendarControl() {
    var calendarId = 'CalendarControl';
    var currentYear = 0;
    var currentMonth = 0;
    var currentDay = 0;
    var selectedYear = 0;
    var selectedMonth = 0;
    var selectedDay = 0;
    var months = ['Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
    var dateField = null;
    function getProperty(p_property){
        var p_elm = calendarId;
        var elm = null;
        if(typeof(p_elm) == "object"){
            elm = p_elm;
        } else {
            elm = document.getElementById(p_elm);
        }
        if (elm != null){
            if(elm.style){
                elm = elm.style;
                if(elm[p_property]){
                    return elm[p_property];
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }
    }
    function setElementProperty(p_property, p_value, p_elmId){
        var p_elm = p_elmId;
        var elm = null;
        if(typeof(p_elm) == "object"){
            elm = p_elm;
        } else {
            elm = document.getElementById(p_elm);
        }
        if((elm != null) && (elm.style != null)){
            elm = elm.style;
            elm[ p_property ] = p_value;
        }
    }
    function setProperty(p_property, p_value) {
        setElementProperty(p_property, p_value, calendarId);
    }
    function getDaysInMonth(year, month) {
        return [31,((!(year % 4 ) && ( (year % 100 ) || !( year % 400 ) ))?29:28),31,30,31,30,31,31,30,31,30,31][month-1];
    }
    function getDayOfWeek(year, month, day) {
        var date = new Date(year,month-1,day)
        return date.getDay();
    }
    this.clearDate = clearDate;
    function clearDate() {
        dateField.value = '';
        hide();
    }
    this.setDate = setDate;
    function setDate(year, month, day) {
        if (dateField) {
            if (month < 10) {
                month = "0" + month;
            }
            if (day < 10) {
                day = "0" + day;
            }
            var dateString = year+"-"+month+"-"+day;
            dateField.value = dateString;
            hide();
        }
        return;
    }
    this.changeMonth = changeMonth;
    function changeMonth(change) {
        currentMonth += change;
        currentDay = 0;
        if(currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        } else if(currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        calendar = document.getElementById(calendarId);
        calendar.innerHTML = calendarDrawTable();
    }
    this.changeYear = changeYear;
    function changeYear(change) {
        currentYear += change;
        currentDay = 0;
        calendar = document.getElementById(calendarId);
        calendar.innerHTML = calendarDrawTable();
    }
    function getCurrentYear() {
        var year = new Date().getYear();
        if(year < 1900) year += 1900;
        return year;
    }
    function getCurrentMonth() {
        return new Date().getMonth() + 1;
    }
    function getCurrentDay() {
        return new Date().getDate();
    }
    function calendarDrawTable() {
        var dayOfMonth = 1;
        var validDay = 0;
        var startDayOfWeek = getDayOfWeek(currentYear, currentMonth, dayOfMonth-1);
        var daysInMonth = getDaysInMonth(currentYear, currentMonth);
        var css_class = null; //CSS class for each day
        var table = "<table cellspacing='0′ cellpadding='0′ border='0′>";
        table = table + "<tr class='calendar_header'>";
        table = table + "  <td colspan='2′ class='calendar_previous'> <a href='javascript:changeCalendarControlYear(-1);'>&lt;</a> <br> <a href='javascript:changeCalendarControlMonth(-1);'>&lt;</a></td>";
        table = table + "  <td colspan='3′ class='calendar_title'>" + currentYear + "<br>" + months[currentMonth-1] + "</td>";
        table = table + "  <td colspan='2′ class='calendar_next'> <a href='javascript:changeCalendarControlYear(1);'>&gt;</a> <br> <a href='javascript:changeCalendarControlMonth(1);'>&gt;</a> </td>";
        table = table + "</tr>";
        table = table + "<tr><th>Po</th><th>Ut</th><th>St</th><th>Ct</th><th>Pa</th><th>So</th><th>Ne</th></tr>";
        for(var week=0; week < 6; week++) {
            table = table + "<tr>";
            for(var dayOfWeek=0; dayOfWeek < 7; dayOfWeek++) {
                if(week == 0 && startDayOfWeek == dayOfWeek) {
                    validDay = 1;
                } else if (validDay == 1 && dayOfMonth > daysInMonth) {
                    validDay = 0;
                }
                if(validDay) {
                    if (dayOfMonth == selectedDay && currentYear == selectedYear && currentMonth == selectedMonth) {
                        css_class = 'calendar_current';
                    } else if (dayOfWeek == 5 || dayOfWeek == 6) {
                        css_class = 'calendar_weekend';
                    } else {
                        css_class = 'calendar_weekday';
                    }
                    table = table + "<td><a class='"+css_class+"' href=\"javascript:setCalendarControlDate("+currentYear+","+currentMonth+","+dayOfMonth+")\">"+dayOfMonth+"</a></td>";
                    dayOfMonth++;
                } else {
                    table = table + "<td class='calendar_empty'>&nbsp;</td>";
                }
            }
            table = table + "</tr>";
        }
        table = table + "<tr class='calendar_header'><th colspan='7′ style='padding: 3px;'><a href='javascript:clearCalendarControl();'>Smazat</a> | <a href='javascript:hideCalendarControl();'>Zavřit</a></td></tr>";
        table = table + "</table>";
        return table;
    }
    this.show = show;
    function show(field) {
        can_hide = 0;
 
        // If the calendar is visible and associated with
        // this field do not do anything.
        if (dateField == field) {
            return;
        } else {
            dateField = field;
        }
        if(dateField) {
            try {
                var dateString = new String(dateField.value);
                var dateParts = dateString.split("/");
       
                selectedMonth = parseInt(dateParts[1],10);
                selectedDay = parseInt(dateParts[0],10);
                selectedYear = parseInt(dateParts[2],10);
            } catch(e) {}
        }
        if (!(selectedYear && selectedMonth && selectedDay)) {
            selectedMonth = getCurrentMonth();
            selectedDay = getCurrentDay();
            selectedYear = getCurrentYear();
        }
        currentMonth = selectedMonth;
        currentDay = selectedDay;
        currentYear = selectedYear;
        if(document.getElementById){
            calendar = document.getElementById(calendarId);
            calendar.innerHTML = calendarDrawTable(currentYear, currentMonth);
            setProperty('display', 'block');
            var fieldPos = new positionInfo(dateField);
            var calendarPos = new positionInfo(calendarId);
            var x = fieldPos.getElementLeft();
            var y = fieldPos.getElementBottom();
            setProperty('left', x + "px");
            setProperty('top', y + "px");
 
            if (document.all) {
                setElementProperty('display', 'block', 'CalendarControlIFrame');
                setElementProperty('left', x + "px", 'CalendarControlIFrame');
                setElementProperty('top', y + "px", 'CalendarControlIFrame');
                setElementProperty('width', calendarPos.getElementWidth() + "px", 'CalendarControlIFrame');
                setElementProperty('height', calendarPos.getElementHeight() + "px", 'CalendarControlIFrame');
            }
        }
    }
    this.hide = hide;
    function hide() {
        if(dateField) {
            setProperty('display', 'none');
            setElementProperty('display', 'none', 'CalendarControlIFrame');
            dateField = null;
        }
    }
    this.visible = visible;
    function visible() {
        return dateField
    }
    this.can_hide = can_hide;
    var can_hide = 0;
}
var calendarControl = new CalendarControl();
function showCalendarControl(textField) {
    // textField.onblur = hideCalendarControl;
    document.getElementById("CalendarControlIFrame").style.display="block";
    
    calendarControl.show(textField);
}
function clearCalendarControl() {
    calendarControl.clearDate();
}
function hideCalendarControl() {
    if (calendarControl.visible()) {
        calendarControl.hide();
    }
}
function setCalendarControlDate(year, month, day) {
    calendarControl.setDate(year, month, day);
}
function changeCalendarControlYear(change) {
    calendarControl.changeYear(change);
}
function changeCalendarControlMonth(change) {
    calendarControl.changeMonth(change);
}
document.write("<iframe id='CalendarControlIFrame' style='display: none' src='javascript:false;' frameBorder='0′ scrolling='no'></iframe>");
document.write("<div id='CalendarControl'></div>");