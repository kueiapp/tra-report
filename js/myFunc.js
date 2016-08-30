$(document).ready(function(){

    function getCurrentLocation()
    {
        if(navigator.geolocation){ 
            $("#demo").html('Loading your location...');
            navigator.geolocation.getCurrentPosition(
                function success(position) {
                    curLatitude  = position.coords.latitude;
                    curLongitude = position.coords.longitude;

                    $("#demo").html('<p>Your location is ' + curLatitude + '°, ' + curLongitude + '°</p>');
                    getNearestStation(curLatitude,curLongitude);
                },function error() {
                    $("#demo").html("Unable to retrieve your location<br/>");
            });
        }
        else{
            $("#demo").html('Your browser does not support location<br/>');
        }
    }
    
    function getDistanceBetween(lat1,lon1,lat2,lon2)
    {
        //console.log(lat1+','+lon1+'&'+lat2+','+lon2);
        var R = 6371; // km (change this constant to get miles)
        var dLat = (lat2-lat1) * Math.PI / 180;
        var dLon = (lon2-lon1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
         Math.cos(lat1 * Math.PI / 180 ) * Math.cos(lat2 * Math.PI / 180 ) *
         Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c; //km
        //if (d>1) return Math.round(d);
        //else if (d<=1) return Math.round(d*1000);
        
        return Math.round(d * 1000); //meter
    }
    
    /* 尋找矩陣中最小值 */
    Array.min = function( array ){
        return Math.min.apply( Math, array );
    };
    
    /* 抓取站點名稱與代碼 */
    function getNearestStation(curLatitude,curLongitude)
    {
        $.ajax({
            url: 'db/db_mobile.php',
            data: 'act=getStations',
            type: 'POST',
            dataType: 'json',
            delegate: true,
            async: false,
            success: function(data){  
                distanceArray = [];
                distanceArray['key'] = [];
                distanceArray['val'] = [];
                
                $.each(data,function(key,val){
                    $("#station").append('<option value="'+val.stationid+'">'+val.cname+' '+val.ename+'</option>');           
                    distanceArray['val'].push(getDistanceBetween(curLatitude,curLongitude,val.latitude,val.longitude) );
                    distanceArray['key'].push(val.stationid);
                });            
                
                // find shortest distance
                var min = Array.min(distanceArray['val']);
                var index = distanceArray['val'].indexOf(min);
                
                // select station
                $("#station").val(distanceArray['key'][index]);
                $("#submitBtn").prop("disabled",false);
            },
            error: function(msg){
                console.log(msg.responseText);
            }
        });
    }
    
    /* 抓取使用者所在位置 */
    getCurrentLocation();
    
    /* 送出表單 */
    $("#form1").ajaxForm({
        delegate: true,
        success: function(responseText, statusText, xhr, $form){
            //console.log(responseText);
            if( parseInt(responseText)==0 ){
                alert('欄位不能留空');
            }
            else if(responseText=="no car"){
                alert("無次車次");
            }
            else{
                $("#result").html('<h1>'+responseText+' 人投票</h1>');

            }
        }
    });
});