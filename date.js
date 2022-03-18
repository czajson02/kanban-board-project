// Real-time date display function
function date_time(id)
{
        date = new Date;
        year = date.getFullYear();
        month = date.getMonth();

        // Custom month array
        months = new Array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        d = date.getDate();
        day = date.getDay();

        // Custom day array
        days = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        h = date.getHours();

        // Correction to time format for single digit values
        if(h<10){
                h = "0"+h;
        }
        m = date.getMinutes();
        if(m<10){
                m = "0"+m;
        }
        s = date.getSeconds();
        if(s<10){
                s = "0"+s;
        }
        
        // Final display format
        result = ''+d+'-'+months[month]+'-'+year+' '+h+':'+m+':'+s;
        document.getElementById(id).innerHTML = result;

        // Refresh interval 
        setTimeout('date_time("'+id+'");','1000');
        return true;
}
